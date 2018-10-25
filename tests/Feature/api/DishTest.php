<?php

namespace Tests\Feature\api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Laravel\Passport\Passport;
use App\Traits\CodeGenerator;
use App\Dish;
use App\User;

class DishTest extends TestCase
{
	use WithFaker, CodeGenerator;

    /**
     * fetch dishes test.
     *
     * @return void
     */
    public function testFetchDishes()
    {
    	//test for waiter
    	Passport::actingAs(factory(User::class)->create(['role_id' => 4]));
    	$this->json('GET', '/api/dish')->assertStatus(200)->assertJsonStructure(['dishes']);
    	//test for chef
    	Passport::actingAs(factory(User::class)->create(['role_id' => 3]));
    	$this->json('GET', '/api/dish')->assertStatus(200)->assertJsonStructure(['dishes']);
    	//test for manager
    	Passport::actingAs(factory(User::class)->create(['role_id' => 2]));
    	$this->json('GET', '/api/dish')->assertStatus(200)->assertJsonStructure(['dishes']);
    	//test for admin
    	Passport::actingAs(factory(User::class)->create(['role_id' => 1]));
    	$this->json('GET', '/api/dish')->assertStatus(200)->assertJsonStructure(['dishes']);
    }

    /**
     * store dish test.
     *
     * @return void
     */
    public function testStoreDish()
    {
    	// test for admin
    	$this->storeDishWithSuccessResponse(factory(User::class)->create(['role_id' => 1]));

    	//test for manager
    	$this->storeDishWithSuccessResponse(factory(User::class)->create(['role_id' => 2]));

    	//test for chef
    	$this->storeDishWithForbiddenResponse(factory(User::class)->create(['role_id' => 3]));

    	//test for waiter
    	$this->storeDishWithForbiddenResponse(factory(User::class)->create(['role_id' => 4]));
    }

    /**
     * show dish test.
     *
     * @return void
     */
    public function testShowDish()
    {
    	//test for admin
    	$this->showDishSuccessfully(factory(User::class)->create(['role_id' => 1]));
    	//test for manager
    	$this->showDishSuccessfully(factory(User::class)->create(['role_id' => 2]));
    	//test for chef
    	$this->showDishSuccessfully(factory(User::class)->create(['role_id' => 3]));
    	//test for waiter
    	$this->showDishSuccessfully(factory(User::class)->create(['role_id' => 4]));
    }

    /**
     * admin can updates dish made by manager test.
     *
     * @return void
     */
    public function testAdminCanUpdateDishMadeByManager()
    {
        Storage::fake('public');
        
    	$dish = factory(Dish::class)->create([
            'user_id' => factory(User::class)->create(['role_id' => 2])->id
        ]);

    	Passport::actingAs(factory(User::class)->create(['role_id' => 1]));
    	$this->json('PATCH', '/api/dish/'.$dish->id, [
            'dish_name' => $this->faker->unique()->sentence($nbWords=2),
            'dish_description' => $this->faker->paragraph,
            'dish_category_id' => \App\DishCategory::inRandomOrder()->first()->id,
            'dish_price' => $this->faker->numberBetween($min=1, $max=10),
            'dish_image' => UploadedFile::fake()->image('dish.jpg')->size(100)
        ])->assertStatus(200)->assertJsonStructure(['message', 'dish']);
    	Storage::disk('public')->assertExists('/images/dishes/' . $dish->image);
    }

    /**
     * manager cant updates dish made by admin test.
     *
     * @return void
     */
    public function testManagerCantUpdateDishMadeByAdmin()
    {
        Storage::fake('public');

        $dish = factory(Dish::class)->create([
            'user_id' => factory(User::class)->create(['role_id' => 1])->id
        ]);

        Passport::actingAs(factory(User::class)->create(['role_id' => 2]));
        $this->json('PATCH', '/api/dish/'.$dish->id, [
            'dish_name' => $this->faker->unique()->sentence($nbWords=2),
            'dish_description' => $this->faker->paragraph,
            'dish_category_id' => \App\DishCategory::inRandomOrder()->first()->id,
            'dish_price' => $this->faker->numberBetween($min=1, $max=10),
            'dish_image' => UploadedFile::fake()->image('dish.jpg')->size(100)
        ])->assertStatus(403);
    }

    /**
     * manager can updates dish made by him.
     *
     * @return void
     */
    public function testManagerCanUpdateDishMadeByHim()
    {
        Storage::fake('public');

        $manager = factory(User::class)->create(['role_id' => 1]);
        $dish = factory(Dish::class)->create([
            'user_id' => $manager->id
        ]);

        Passport::actingAs($manager);
        $this->json('PATCH', '/api/dish/'.$dish->id, [
            'dish_name' => $this->faker->unique()->sentence($nbWords=2),
            'dish_description' => $this->faker->paragraph,
            'dish_category_id' => \App\DishCategory::inRandomOrder()->first()->id,
            'dish_price' => $this->faker->numberBetween($min=1, $max=10),
            'dish_image' => UploadedFile::fake()->image('dish.jpg')->size(100)
        ])->assertStatus(200)->assertJsonStructure(['message', 'dish']);
        Storage::disk('public')->assertExists('/images/dishes/' . $dish->image);;
    }

    /**
     * admin can delete dish made by manager.
     *
     * @return void
     */
    public function testAdminCanDeleteDishMadeByManager()
    {    
        $dish = factory(Dish::class)->create([
            'user_id' => factory(User::class)->create(['role_id' => 2])->id
        ]);

        Passport::actingAs(factory(User::class)->create(['role_id' => 1]));
        $this->json('delete', '/api/dish/'.$dish->id)->assertStatus(200)->assertJsonStructure(['message']);
        Storage::disk('public')->assertMissing('/images/dishes/' . $dish->image);
    }

    /**
     * manager cant delete dish made by admin test.
     *
     * @return void
     */
    public function testManagerCantDeleteDishMadeByAdmin()
    {
        $dish = factory(Dish::class)->create([
            'user_id' => factory(User::class)->create(['role_id' => 1])->id
        ]);

        Passport::actingAs(factory(User::class)->create(['role_id' => 2]));
        $this->json('delete', '/api/dish/'.$dish->id)->assertStatus(403);
    }

    /**
     * manager can delete dish made by him.
     *
     * @return void
     */
    public function testManagerCanDeleteDishMadeByHim()
    {
        $manager = factory(User::class)->create(['role_id' => 1]);
        $dish = factory(Dish::class)->create([
            'user_id' => $manager->id
        ]);

        Passport::actingAs($manager);
        $this->json('delete', '/api/dish/'.$dish->id)->assertStatus(200)->assertJsonStructure(['message']);
        Storage::disk('public')->assertMissing('/images/dishes/' . $dish->image);;
    }

    /**
     * store dish success method.
     *
     * @param \App\User $user
     * @return void
     */
    public function storeDishWithSuccessResponse($user)
    {
        Storage::fake('public');

    	Passport::actingAs($user);
        $dishName = $this->faker->unique()->sentence($nbWords=2);
    	$this->json('POST', '/api/dish', [
            'dish_name' => $dishName,
            'dish_description' => $this->faker->paragraph,
            'dish_category_id' => \App\DishCategory::inRandomOrder()->first()->id,
            'dish_price' => $this->faker->numberBetween($min=1, $max=10),
            'dish_image' => UploadedFile::fake()->image('dish.jpg')->size(100)
        ])->assertStatus(201)->assertJsonStructure(['message', 'dish']);
    	$dish = Dish::where('name', $dishName)->firstOrFail();
    	Storage::disk('public')->assertExists('images/dishes/'.$dish->image);
    }

    /**
     * store dish failed method.
     *
     * @param \App\User $user
     * @return void
     */
    public function storeDishWithForbiddenResponse($user)
    {
    	Storage::fake('public');

    	Passport::actingAs($user);
        $dishName = $this->faker->unique()->sentence($nbWords=2);
    	$this->json('POST', '/api/dish', [
            'dish_name' => $dishName,
            'dish_description' => $this->faker->paragraph,
            'dish_category_id' => \App\DishCategory::inRandomOrder()->first()->id,
            'dish_price' => $this->faker->numberBetween($min=1, $max=10),
            'dish_image' => UploadedFile::fake()->image('dish.jpg')->size(100)
        ])->assertStatus(403);
    	Storage::disk('public')->assertMissing('images/dishes/'.str_slug($dishName).'.'.'jpg');
    }

     /**
     * show dish successfully method.
     *
     * @param \App\User $user
     * @return void
     */
    public function showDishSuccessfully($user)
    {
    	$dish = Dish::inRandomOrder()->first();
    	Passport::actingAs($user);
    	$this->json('GET', '/api/dish/'.$dish->id)->assertStatus(200)->assertJsonStructure(['dish']);
    }
}
