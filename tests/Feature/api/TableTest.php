<?php

namespace Tests\Feature\api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use App\Table;
use App\User;

class TableTest extends TestCase
{
	use WithFaker;

    /**
     * fetching table test.
     *
     * @return void
     */
    public function testFetchTable()
    {
        // test for admin
        Passport::actingAs(factory(User::class)->create(['role_id' => 1]));
        $this->json('GET', '/api/table')->assertStatus(200)->assertJsonStructure(['tables']);

        // test for manager
        Passport::actingAs(factory(User::class)->create(['role_id' => 2]));
        $this->json('GET', '/api/table')->assertStatus(200)->assertJsonStructure(['tables']);

        // test for chef
        Passport::actingAs(factory(User::class)->create(['role_id' => 3]));
        $this->json('GET', '/api/table')->assertStatus(403);

        // test for waiter
        Passport::actingAs(factory(User::class)->create(['role_id' => 4]));
        $this->json('GET', '/api/table')->assertStatus(200)->assertJsonStructure(['tables']);
    }

    /**
     * store table test.
     *
     * @return void
     */
    public function testStoreTable()
    {
    	//test for admin
    	Passport::actingAs(factory(User::class)->create(['role_id' => 1]));
    	$this->json('POST', '/api/table', [
    		'table_name' => $this->faker->unique()->sentence($nbWords=2)
    	])->assertStatus(201)->assertJsonStructure(['message', 'table']);

    	//test for manager
    	Passport::actingAs(factory(User::class)->create(['role_id' => 2]));
    	$this->json('POST', '/api/table', [
    		'table_name' =>$this->faker->unique()->sentence($nbWords=2)
    	])->assertStatus(201)->assertJsonStructure(['message', 'table']);

    	//test for chef
    	Passport::actingAs(factory(User::class)->create(['role_id' => 3]));
    	$this->json('POST', '/api/table', [
    		'table_name' => $this->faker->unique()->sentence($nbWords=2)
    	])->assertStatus(403);

    	//test for chef
    	Passport::actingAs(factory(User::class)->create(['role_id' => 4]));
    	$this->json('POST', '/api/table', [
    		'table_name' => $this->faker->unique()->sentence($nbWords=2)
    	])->assertStatus(403);
    }

    /**
     * test admin can update table made by manager.
     *
     * @return void
     */
    public function testAdminCanUpdateTableMadeByManager()
    {
        $table = factory(Table::class)->create([
            'user_id' => factory(User::class)->create(['role_id' => 2])->id
        ]);

        Passport::actingAs(factory(User::class)->create(['role_id' => 1]));
        $this->json('PATCH', '/api/table/'.$table->id, [
            'table_name' => $this->faker->unique()->word
        ])->assertStatus(200)->assertJsonStructure(['message', 'table']);
    }

    /**
     * test manager cant update table made by admin.
     *
     * @return void
     */
    public function testManagerCantUpdateTableMadeByAdmin()
    {
        $table = factory(Table::class)->create([
            'user_id' => factory(User::class)->create(['role_id' => 1])->id
        ]);

        Passport::actingAs(factory(User::class)->create(['role_id' => 2]));
        $this->json('PATCH', '/api/table/'.$table->id, [
            'table_name' => $this->faker->unique()->word
        ])->assertStatus(403);
    }

    /**
     * test manager can update table made by him.
     *
     * @return void
     */
    public function testManagerCanUpdateTableMadeByHim()
    {
        $manager = factory(User::class)->create(['role_id' => 2]);
        $table = factory(Table::class)->create([
            'user_id' => $manager->id
        ]);

        Passport::actingAs($manager);
        $this->json('PATCH', '/api/table/'.$table->id, [
            'table_name' => $this->faker->unique()->word
        ])->assertStatus(200)->assertJsonStructure(['message', 'table']);
    }

    /**
     * test admin can delete table made by manager.
     *
     * @return void
     */
    public function testAdminCanDeleteTableMadeByManager()
    {
        $table = factory(Table::class)->create([
            'user_id' => factory(User::class)->create(['role_id' => 2])->id
        ]);

        Passport::actingAs(factory(User::class)->create(['role_id' => 1]));
        $this->json('delete', '/api/table/'.$table->id)->assertStatus(200)->assertJsonStructure(['message']);
    }

    /**
     * test manager cant delete table made by admin.
     *
     * @return void
     */
    public function testManagerCantDeleteTableMadeByAdmin()
    {
        $table = factory(Table::class)->create([
            'user_id' => factory(User::class)->create(['role_id' => 1])->id
        ]);

        Passport::actingAs(factory(User::class)->create(['role_id' => 2]));
        $this->json('delete', '/api/table/'.$table->id)->assertStatus(403);
    }

    /**
     * test manager can delete table made by him.
     *
     * @return void
     */
    public function testManagerCanDeleteTableMadeByHim()
    {
        $manager = factory(User::class)->create(['role_id' => 2]);
        $table = factory(Table::class)->create([
            'user_id' => $manager->id
        ]);

        Passport::actingAs($manager);
        $this->json('delete', '/api/table/'.$table->id)->assertStatus(200)->assertJsonStructure(['message']);
    }
}
