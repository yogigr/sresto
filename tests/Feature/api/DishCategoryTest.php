<?php

namespace Tests\Feature\api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use App\DishCategory;
use App\User;

class DishCategoryTest extends TestCase
{
	use WithFaker;

    /**
     * fetch dish category test.
     *
     * @return void
     */
    public function testFetchDishCategory()
    {
        //admin test
        Passport::actingAs(factory(User::class)->create(['role_id' => 1]));
        $this->json('GET', '/api/dish-category')->assertStatus(200)->assertJsonStructure(['dishCategories']);

        //manager test
        Passport::actingAs(factory(User::class)->create(['role_id' => 2]));
        $this->json('GET', '/api/dish-category')->assertStatus(200)->assertJsonStructure(['dishCategories']);

        //chef test
        Passport::actingAs(factory(User::class)->create(['role_id' => 3]));
        $this->json('GET', '/api/dish-category')->assertStatus(200)->assertJsonStructure(['dishCategories']);

        //waiter test
        Passport::actingAs(factory(User::class)->create(['role_id' => 4]));
        $this->json('GET', '/api/dish-category')->assertStatus(200)->assertJsonStructure(['dishCategories']);
    }

    /**
     * store dish category test.
     *
     * @return void
     */
    public function testStoreDishCategory()
    {
    	//test for admin
    	Passport::actingAs(factory(User::class)->create(['role_id' => 1]));
    	$this->json('POST', '/api/dish-category', [
    		'category_name' => $this->faker->unique()->sentence($nbWords=2),
    		'category_description' => $this->faker->paragraph
    	])->assertStatus(201)->assertJsonStructure(['message', 'dishCategory']);

    	//test for manager
    	Passport::actingAs(factory(User::class)->create(['role_id' => 2]));
    	$this->json('POST', '/api/dish-category', [
    		'category_name' => $this->faker->unique()->sentence($nbWords=2),
    		'category_description' => $this->faker->paragraph
    	])->assertStatus(201)->assertJsonStructure(['message', 'dishCategory']);

    	//test for chef
    	Passport::actingAs(factory(User::class)->create(['role_id' => 3]));
    	$this->json('POST', '/api/dish-category', [
    		'category_name' => $this->faker->unique()->sentence($nbWords=2),
    		'category_description' => $this->faker->paragraph
    	])->assertStatus(403);

    	//test for waiter
    	Passport::actingAs(factory(User::class)->create(['role_id' => 4]));
    	$this->json('POST', '/api/dish-category', [
    		'category_name' => $this->faker->unique()->sentence($nbWords=2),
    		'category_description' => $this->faker->paragraph
    	])->assertStatus(403);
    }

    /**
     * admin can update dish category made by manager.
     *
     * @return void
     */
    public function testAdminCanUpdateDishCategoryMadeByManager()
    {
    	$dishCategory = factory(DishCategory::class)->create([
            'user_id' => factory(User::class)->create(['role_id' => 2])->id
        ]);

    	Passport::actingAs(factory(User::class)->create(['role_id' => 1]));
    	$this->json('PATCH', '/api/dish-category/'.$dishCategory->id, [
    		'category_name' => $this->faker->unique()->sentence($nbWords=2),
    		'category_description' => $this->faker->Paragraph
    	])->assertStatus(200)->assertJsonStructure(['message', 'dishCategory']);
    }

    /**
     * manager cant update dish category made by admin.
     *
     * @return void
     */
    public function testManagerCantUpdateDishCategoryMadeByAdmin()
    {
    	$dishCategory = factory(DishCategory::class)->create([
            'user_id' => factory(User::class)->create(['role_id' => 1])->id
        ]);

    	Passport::actingAs(factory(User::class)->create(['role_id' => 2]));
    	$this->json('PATCH', '/api/dish-category/'.$dishCategory->id, [
    		'category_name' => $this->faker->Word,
    		'category_description' => $this->faker->Paragraph
    	])->assertStatus(403);
    }

    /**
     * manager can update dish category made him.
     *
     * @return void
     */
    public function testManagerCanUpdateDishCategoryMadeByHim()
    {
        $manager = factory(User::class)->create(['role_id' => 2]);
    	$dishCategory = factory(DishCategory::class)->create([
            'user_id' => $manager->id
        ]);

    	Passport::actingAs($manager);
    	$this->json('PATCH', '/api/dish-category/'.$dishCategory->id, [
    		'category_name' => $this->faker->unique()->sentence($nbWord=2),
    		'category_description' => $this->faker->Paragraph
    	])->assertStatus(200)->assertJsonStructure(['message', 'dishCategory']);
    }

    /**
     * admin can delete dish category made by manager.
     *
     * @return void
     */
    public function testAdminCanDeleteDishCategoryMadeByManager()
    {
    	$dishCategory = factory(DishCategory::class)->create([
            'user_id' => factory(User::class)->create(['role_id' => 2])->id
        ]);

    	Passport::actingAs(factory(User::class)->create(['role_id' => 1]));
    	$this->json('delete', '/api/dish-category/'.$dishCategory->id)->assertStatus(200)->assertJsonStructure(['message']);
    }

    /**
     * manager cant delete dish category made by admin.
     *
     * @return void
     */
    public function testManagerCantDeleteDishCategoryMadeByAdmin()
    {
    	$dishCategory = factory(DishCategory::class)->create([
            'user_id' => factory(User::class)->create(['role_id' => 1])->id
        ]);

    	Passport::actingAs(factory(User::class)->create(['role_id' => 2]));
    	$this->json('delete', '/api/dish-category/'.$dishCategory->id)->assertStatus(403);
    }

    /**
     * manager can delete dish category made him.
     *
     * @return void
     */
    public function testManagerCanDeleteDishCategoryMadeByHim()
    {
    	$manager = factory(User::class)->create(['role_id' => 2]);
        $dishCategory = factory(DishCategory::class)->create([
            'user_id' => $manager->id
        ]);

    	Passport::actingAs($manager);
    	$this->json('delete', '/api/dish-category/'.$dishCategory->id)->assertStatus(200)->assertJsonStructure(['message']);
    }
}	
