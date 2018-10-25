<?php

namespace Tests\Feature\api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use App\User;

class UserTest extends TestCase
{
	use WithFaker;
    
    /**
     * fetch users test successfully.
     *
     * @return void
     */
    public function testFetchUsersSuccessfully()
    {
        Passport::actingAs(factory(User::class)->create(['role_id' => 1]));
       	$this->json('GET', '/api/user')
       	->assertStatus(200)->assertJsonStructure(['users']);

        Passport::actingAs(factory(User::class)->create(['role_id' => 2]));
        $this->json('GET', '/api/user')
        ->assertStatus(200)->assertJsonStructure(['users']);

        Passport::actingAs(factory(User::class)->create(['role_id' => 3]));
        $this->json('GET', '/api/user')
        ->assertStatus(403);

        Passport::actingAs(factory(User::class)->create(['role_id' => 4]));
        $this->json('GET', '/api/user')
        ->assertStatus(403);
    }

    /**
     * test fetch users failed (without token).
     *
     * @return void
     */
    public function testFetchUsersWithoutToken()
    {
       	$this->json('GET', '/api/user')
       	->assertStatus(401);
    }

    /**
     * test store user.
     *
     * @return void
     */
    public function testStoreUser()
    {
        //test for admin
    	Passport::actingAs(factory(User::class)->create(['role_id' => 1]));
    	$this->json('POST', '/api/user', [
    		'user_name' => $this->faker->name,
    		'user_email' => $this->faker->unique()->safeEmail,
    		'user_password' => 'secret',
    		'user_password_confirmation' => 'secret', 
    		'user_role_id' => 2,
    	])->assertStatus(201)->assertJsonStructure(['message', 'user']);

        //test for manager
        Passport::actingAs(factory(User::class)->create(['role_id' => 2]));
        $this->json('POST', '/api/user', [
            'user_name' => $this->faker->name,
            'user_email' => $this->faker->unique()->safeEmail,
            'user_password' => 'secret',
            'user_password_confirmation' => 'secret', 
            'user_role_id' => 2,
        ])->assertStatus(201)->assertJsonStructure(['message', 'user']);

        //test for chef
        Passport::actingAs(factory(User::class)->create(['role_id' => 3]));
        $this->json('POST', '/api/user', [
            'user_name' => $this->faker->name,
            'user_email' => $this->faker->unique()->safeEmail,
            'user_password' => 'secret',
            'user_password_confirmation' => 'secret', 
            'user_role_id' => 2,
        ])->assertStatus(403);

        //test for waiter
        Passport::actingAs(factory(User::class)->create(['role_id' => 4]));
        $this->json('POST', '/api/user', [
            'user_name' => $this->faker->name,
            'user_email' => $this->faker->unique()->safeEmail,
            'user_password' => 'secret',
            'user_password_confirmation' => 'secret', 
            'user_role_id' => 2,
        ])->assertStatus(403);
    }

    /**
     * test show user.
     *
     * @return void
     */
    public function testShowUser()
    {
        $user = factory(User::class)->create();

        //test for admin
    	Passport::actingAs(factory(User::class)->create(['role_id' => 1]));
    	$this->json('GET', '/api/user/'.$user->id)->assertStatus(200)->assertJsonStructure(['user']);

        //test for manager
        Passport::actingAs(factory(User::class)->create(['role_id' => 2]));
        $this->json('GET', '/api/user/'.$user->id)->assertStatus(200)->assertJsonStructure(['user']);

        //test for chef
        Passport::actingAs(factory(User::class)->create(['role_id' => 3]));
        $this->json('GET', '/api/user/'.$user->id)->assertStatus(403);

        //test for waiter
        Passport::actingAs(factory(User::class)->create(['role_id' => 4]));
        $this->json('GET', '/api/user/'.$user->id)->assertStatus(403);
    }

    /**
     * test update user.
     *
     * @return void
     */
    public function testUpdateUser()
    {
    	//create user first
    	$user = factory(User::class)->create();

    	//test for admin
    	Passport::actingAs(factory(User::class)->create(['role_id' => 1]));
    	$this->json('PATCH', '/api/user/'.$user->id, [
    		'user_name' => $this->faker->name,
    		'user_email' => $this->faker->safeEmail,
    		'user_role_id' => 3
    	])->assertStatus(200)->assertJsonStructure(['message', 'user']);

        //test for manager
        Passport::actingAs(factory(User::class)->create(['role_id' => 2]));
        $this->json('PATCH', '/api/user/'.$user->id, [
            'user_name' => $this->faker->name,
            'user_email' => $this->faker->safeEmail,
            'user_role_id' => 3
        ])->assertStatus(200)->assertJsonStructure(['message', 'user']);

        //test for chef
        Passport::actingAs(factory(User::class)->create(['role_id' => 3]));
        $this->json('PATCH', '/api/user/'.$user->id, [
            'user_name' => $this->faker->name,
            'user_email' => $this->faker->safeEmail,
            'user_role_id' => 3
        ])->assertStatus(403);

        //test for waiter
        Passport::actingAs(factory(User::class)->create(['role_id' => 4]));
        $this->json('PATCH', '/api/user/'.$user->id, [
            'user_name' => $this->faker->name,
            'user_email' => $this->faker->safeEmail,
            'user_role_id' => 3
        ])->assertStatus(403);
    }

    /**
     * test delete user.
     *
     * @return void
     */
    public function testDestroyUser()
    {
    	//admin test
    	$user = factory(User::class)->create();
    	Passport::actingAs(factory(User::class)->create(['role_id' => 1]));
    	$this->json("delete", 'api/user/'.$user->id)
    	->assertStatus(200)->assertJsonStructure(['message']);

        //manager test
        $user = factory(User::class)->create();
        Passport::actingAs(factory(User::class)->create(['role_id' => 2]));
        $this->json("delete", 'api/user/'.$user->id)
        ->assertStatus(200)->assertJsonStructure(['message']);

        //chef test
        $user = factory(User::class)->create();
        Passport::actingAs(factory(User::class)->create(['role_id' => 3]));
        $this->json("delete", 'api/user/'.$user->id)
        ->assertStatus(403);

        //waiter test
        $user = factory(User::class)->create();
        Passport::actingAs(factory(User::class)->create(['role_id' => 4]));
        $this->json("delete", 'api/user/'.$user->id)
        ->assertStatus(403);
    }

    /**
     * test change role ofuser.
     *
     * @return void
     */
    public function testChangeUserRole()
    {
    	//create user first
    	$user = factory(User::class)->create();

    	//admin test
    	Passport::actingAs(factory(User::class)->create(['role_id' => 1]));
    	$this->json('PATCH', '/api/user/'.$user->id.'/change-role', [
    		'user_role_id' => $this->faker->numberBetween($min=1, $max=4)
    	])->assertStatus(200)->assertJsonStructure(['message']);

        //manager test
        Passport::actingAs(factory(User::class)->create(['role_id' => 2]));
        $this->json('PATCH', '/api/user/'.$user->id.'/change-role', [
            'user_role_id' => $this->faker->numberBetween($min=1, $max=4)
        ])->assertStatus(200)->assertJsonStructure(['message']);

        //chef test
        Passport::actingAs(factory(User::class)->create(['role_id' => 3]));
        $this->json('PATCH', '/api/user/'.$user->id.'/change-role', [
            'user_role_id' => $this->faker->numberBetween($min=1, $max=4)
        ])->assertStatus(403);

        //waiter test
        Passport::actingAs(factory(User::class)->create(['role_id' => 4]));
        $this->json('PATCH', '/api/user/'.$user->id.'/change-role', [
            'user_role_id' => $this->faker->numberBetween($min=1, $max=4)
        ])->assertStatus(403);
    }

    /**
     * test change password of user.
     *
     * @return void
     */
    public function testChangeUserPassword()
    {
    	//admin test
        $user = factory(User::class)->create();
    	Passport::actingAs(factory(User::class)->create(['role_id' => 1]));
    	$this->json('PATCH', '/api/user/'.$user->id.'/change-password', [
    		'old_password' => 'secret',
    		'new_password' => 'secret1',
    		'new_password_confirmation' => 'secret1'
    	])->assertStatus(200)->assertJsonStructure(['message']);

        //manager test
        $user = factory(User::class)->create();
        Passport::actingAs(factory(User::class)->create(['role_id' => 2]));
        $this->json('PATCH', '/api/user/'.$user->id.'/change-password', [
            'old_password' => 'secret',
            'new_password' => 'secret1',
            'new_password_confirmation' => 'secret1'
        ])->assertStatus(200)->assertJsonStructure(['message']);

        //chef test
        $user = factory(User::class)->create();
        Passport::actingAs(factory(User::class)->create(['role_id' => 3]));
        $this->json('PATCH', '/api/user/'.$user->id.'/change-password', [
            'old_password' => 'secret',
            'new_password' => 'secret1',
            'new_password_confirmation' => 'secret1'
        ])->assertStatus(403);

        //waiter test
        $user = factory(User::class)->create();
        Passport::actingAs(factory(User::class)->create(['role_id' => 4]));
        $this->json('PATCH', '/api/user/'.$user->id.'/change-password', [
            'old_password' => 'secret',
            'new_password' => 'secret1',
            'new_password_confirmation' => 'secret1'
        ])->assertStatus(403);
    }

    /**
     * test change password of user with invalid old password.
     *
     * @return void
     */
    public function testChangeUserPasswordWithInvalidOldPassword()
    {
     	$user = factory(User::class)->create();

     	Passport::actingAs(factory(User::class)->create(['role_id' => 1]));
     	$this->json('PATCH', '/api/user/'.$user->id.'/change-password', [
    		'old_password' => 'secret1',
    		'new_password' => 'secret',
    		'new_password_confirmation' => 'secret'
    	])->assertStatus(422)->assertJsonStructure(['message', 'errors']);
    }

    /**
     * users can change their password.
     *
     * @return void
    */
    public function testUserCanChangeHisPassword()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $this->json('PATCH', '/api/user/'.$user->id.'/change-password', [
            'old_password' => 'secret',
            'new_password' => 'secret1',
            'new_password_confirmation' => 'secret1'
        ])->assertStatus(200)->assertJsonStructure(['message']);
    }

     /**
     * manager cant delete his account.
     *
     * @return void
    */
    public function testManagerCantDeleteHisAccount()
    {
        $manager = factory(User::class)->create(['role_id' => 2]);
        Passport::actingAs($manager);
        $this->json("delete", 'api/user/'.$manager->id)
        ->assertStatus(403);
    }
}
