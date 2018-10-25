<?php

namespace Tests\Feature\api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use App\Customer;

class CustomerTest extends TestCase
{
    use WithFaker;

    /**
     * test fetch customer successfully.
     *
     * @return void
     */
    public function testFetchCustomer()
    {
        // admin test
        Passport::actingAs(factory(\App\User::class)->create(['role_id' => 1]));
        $this->json('GET', '/api/customer')->assertStatus(200)->assertJsonStructure(['customers']);

        // manager test
        Passport::actingAs(factory(\App\User::class)->create(['role_id' => 2]));
        $this->json('GET', '/api/customer')->assertStatus(200)->assertJsonStructure(['customers']);

        // chef test
        Passport::actingAs(factory(\App\User::class)->create(['role_id' => 3]));
        $this->json('GET', '/api/customer')->assertStatus(403);

        // admin test
        Passport::actingAs(factory(\App\User::class)->create(['role_id' => 4]));
        $this->json('GET', '/api/customer')->assertStatus(200)->assertJsonStructure(['customers']);
    }


    /**
     * test fetch customer without token.
     *
     * @return void
     */
    public function testFetchCustomerWithoutToken()
    {
    	$this->json('GET', '/api/customer')->assertStatus(401);
    }

    /**
     * test store customer.
     *
     * @return void
     */
    public function testStoreCustomer()
    {
    	//test admin
    	Passport::actingAs(factory(\App\User::class)->create(['role_id' => 1]));
    	$this->json('POST', '/api/customer', [
    		'customer_name' => $this->faker->name,
    		'customer_email' => $this->faker->unique()->safeEmail,
    		'customer_phone' => $this->faker->phoneNumber,
    		'customer_address' => $this->faker->address
    	])->assertStatus(201)->assertJsonStructure(['message', 'customer']);

    	//test manager
    	Passport::actingAs(factory(\App\User::class)->create(['role_id' => 2]));
    	$this->json('POST', '/api/customer', [
    		'customer_name' => $this->faker->name,
            'customer_email' => $this->faker->unique()->safeEmail,
            'customer_phone' => $this->faker->phoneNumber,
            'customer_address' => $this->faker->address
    	])->assertStatus(201)->assertJsonStructure(['message', 'customer']);

    	//test chef
    	Passport::actingAs(factory(\App\User::class)->create(['role_id' => 3]));
    	$this->json('POST', '/api/customer', [
    		'customer_name' => $this->faker->name,
            'customer_email' => $this->faker->unique()->safeEmail,
            'customer_phone' => $this->faker->phoneNumber,
            'customer_address' => $this->faker->address
    	])->assertStatus(403);

    	//test waiter
    	Passport::actingAs(factory(\App\User::class)->create(['role_id' => 4]));
    	$this->json('POST', '/api/customer', [
    		'customer_name' => $this->faker->name,
            'customer_email' => $this->faker->unique()->safeEmail,
            'customer_phone' => $this->faker->phoneNumber,
            'customer_address' => $this->faker->address
    	])->assertStatus(201)->assertJsonStructure(['message', 'customer']);
    }

    /**
     * test view single customer.
     *
     * @return void
    */
    public function testShowCustomer()
    {
    	//test admin
    	$customer = factory(Customer::class)->create();
    	Passport::actingAs(factory(\App\User::class)->create(['role_id' => 1]));
    	$this->json('GET', '/api/customer/'.$customer->id)->assertStatus(200)->assertJsonStructure(['customer']);

    	//test manager
    	$customer = factory(Customer::class)->create();
    	Passport::actingAs(factory(\App\User::class)->create(['role_id' => 2]));
    	$this->json('GET', '/api/customer/'.$customer->id)->assertStatus(200)->assertJsonStructure(['customer']);

    	//test admin
    	$customer = factory(Customer::class)->create();
    	Passport::actingAs(factory(\App\User::class)->create(['role_id' => 3]));
    	$this->json('GET', '/api/customer/'.$customer->id)->assertStatus(403);

    	//test admin
    	$customer = factory(Customer::class)->create();
    	Passport::actingAs(factory(\App\User::class)->create(['role_id' => 4]));
    	$this->json('GET', '/api/customer/'.$customer->id)->assertStatus(200)->assertJsonStructure(['customer']);
    }

    /**
     * test admin can update customer made by manager.
     *
     * @return void
    */
    public function testAdminCanUpdateCustomerMadeByManager()
    {
    	$customer = factory(Customer::class)->create([
            'user_id' => factory(\App\User::class)->create(['role_id' => 2])->id
        ]);

    	Passport::actingAs(factory(\App\User::class)->create(['role_id' => 1]));
    	$this->json('PATCH', '/api/customer/'.$customer->id, [
    		'customer_name' => $this->faker->name,
    		'customer_email' => $this->faker->safeEmail,
    		'customer_phone' => $this->faker->phoneNumber,
    		'customer_address' => $this->faker->Address
    	])->assertStatus(200)->assertJsonStructure(['message', 'customer']);
    }

    /**
     * test manager can not update customer made by admin.
     *
     * @return void
    */
    public function testManagerCantUpdateCustomerMadeByAdmin()
    {
    	$customer = factory(Customer::class)->create([
            'user_id' => factory(\App\User::class)->create(['role_id' => 1])->id
        ]);

    	Passport::actingAs(factory(\App\User::class)->create(['role_id' => 2]));
    	$this->json('PATCH', '/api/customer/'.$customer->id, [
    		'customer_name' => $this->faker->name,
    		'customer_email' => $this->faker->safeEmail,
    		'customer_phone' => $this->faker->phoneNumber,
    		'customer_address' => $this->faker->Address
    	])->assertStatus(403);
    }

    /**
     * test manager and waiter can update customer made by him.
     *
     * @return void
    */
    public function testManagerCanUpdateCustomerMadeByHim()
    {
        //test for manager
        $manager = factory(\App\User::class)->create(['role_id' => 2]);
    	$customer = factory(Customer::class)->create([
            'user_id' => $manager->id
        ]);

    	Passport::actingAs($manager);
    	$this->json('PATCH', '/api/customer/'.$customer->id, [
    		'customer_name' => $this->faker->name,
    		'customer_email' => $this->faker->safeEmail,
    		'customer_phone' => $this->faker->phoneNumber,
    		'customer_address' => $this->faker->Address
    	])->assertStatus(200)->assertJsonStructure(['message', 'customer']);
    }

    /**
     * test admin can delete customer made by manager.
     *
     * @return void
    */
    public function testAdminCanDeleteCustomerMadeByManager()
    {
    	$customer = factory(Customer::class)->create([
            'user_id' => factory(\App\User::class)->create(['role_id' => 2])->id
        ]);

    	Passport::actingAs(factory(\App\User::class)->create(['role_id' => 1]));
    	$this->json('delete', '/api/customer/'.$customer->id)->assertStatus(200)->assertJsonStructure(['message']);
    }

    /**
     * test manager can not delete customer made by admin.
     *
     * @return void
    */
    public function testManagerCantDeleteCustomerMadeByAdmin()
    {
    	$customer = factory(Customer::class)->create([
            'user_id' => factory(\App\User::class)->create(['role_id' => 1])->id
        ]);

    	Passport::actingAs(factory(\App\User::class)->create(['role_id' => 2]));
    	$this->json('delete', '/api/customer/'.$customer->id)->assertStatus(403);
    }

    /**
     * test manager and waiter can delete customer made by him.
     *
     * @return void
    */
    public function testManagerCanDeleteCustomerMadeByHim()
    {
        //test for manager
        $manager = factory(\App\User::class)->create(['role_id' => 2]);
    	$customer = factory(Customer::class)->create(['user_id' => $manager->id]);

    	Passport::actingAs($manager);
    	$this->json('delete', '/api/customer/'.$customer->id)->assertStatus(200)->assertJsonStructure(['message']);
    }
}
