<?php

namespace Tests\Feature\api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Traits\CodeGenerator;
use Laravel\Passport\Passport;
use App\Order;
use App\User;
use App\Customer;
use App\Table;

class OrderTest extends TestCase
{
	use WithFaker, CodeGenerator;

	/**
     * test view order.
     *
     * @return void
     */
	public function testViewOrders()
	{
		$order = factory(Order::class)->create();
        Passport::actingAs(factory(User::class)->create());
        $this->json('GET', '/api/order')->assertStatus(200)->assertJsonStructure(['orders']);
	}

	/**
     * test store order.
     *
     * @return void
     */
	public function testStoreOrder()
	{
		//test for admin
		$this->storeOrderWithSuccessResponse(factory(User::class)->create(['role_id' => 1]));

		//test for manager
		$this->storeOrderWithSuccessResponse(factory(User::class)->create(['role_id' => 2]));

		//test for waiter
		$this->storeOrderWithSuccessResponse(factory(User::class)->create(['role_id' => 4]));

		//test for chef
		$this->storeOrderWithForbiddenResponse(factory(User::class)->create(['role_id' => 3]));
	}

	/**
     * store order with success response.
     *
     * @return void
     */
	public function storeOrderWithSuccessResponse($user)
	{
		Passport::actingAs($user);
		$code = $this->getCodeWithDatetime('OR', 'orders');
		$customer = factory(Customer::class)->create();
        $table = factory(Table::class)->create();
		$this->json('POST', '/api/order', [
            'order_customer_id' => $customer->id,
            'order_table_id' => $table->id,
            'order_subtotal' => $this->faker->numberBetween($min=5, $max=10),
            'order_tax' => $this->faker->numberBetween($min=1, $max=2),
            'order_discount' => $this->faker->numberBetween($min=1, $max=2),
		])->assertStatus(201)->assertJsonStructure(['order']);
		$this->assertDatabaseHas('orders', [
			'code' => $code,
			'customer_id' => $customer->id 
		]);
        $this->assertDatabaseHas('tables', [
            'id' => $table->id,
            'is_in_use' => true
        ]);
	}

	/**
     * store order with success response.
     *
     * @return void
     */
	public function storeOrderWithForbiddenResponse($user)
	{
		Passport::actingAs($user);
		$code = $this->getCodeWithDatetime('OR', 'orders');
		$customer = factory(Customer::class)->create();
		$this->json('POST', '/api/order', [
            'order_customer_id' => $customer->id,
            'order_table_id' => factory(Table::class)->create()->id,
            'order_subtotal' => $this->faker->numberBetween($min=5, $max=10),
            'order_tax' => $this->faker->numberBetween($min=1, $max=2),
            'order_discount' => $this->faker->numberBetween($min=1, $max=2),
		])->assertStatus(403);
		$this->assertDatabaseMissing('orders', [
			'code' => $code,
			'customer_id' => $customer->id 
		]);
	}

	/**
     * test show order.
     *
     * @return void
     */
	public function testShowOrder()
	{
		$order = factory(Order::class)->create();
		Passport::actingAs(factory(User::class)->create());
		$this->json('GET', '/api/order/'.$order->id)->assertStatus(200)->assertJsonStructure(['order']);
	}

	/**
     * A Manager must be able to update orders made by other users.
     *
     * @return void
    */
    public function testManagerMustBeAbleToUpdateOrdersMadeByOtherUsers()
    {
    	$order = factory(Order::class)->create();
    	$customer = factory(Customer::class)->create();
    	Passport::actingAs(factory(User::class)->create(['role_id' => 2]));
    	$this->json('PATCH', '/api/order/'.$order->id, [
    		'order_customer_id' => $customer->id,
            'order_table_id' => $order->table_id,
            'order_subtotal' => $this->faker->numberBetween($min=5, $max=10),
            'order_tax' => $this->faker->numberBetween($min=1, $max=2),
            'order_discount' => $this->faker->numberBetween($min=1, $max=2),
    	])->assertStatus(200)->assertJsonStructure(['message', 'order']);
    	$this->assertDatabaseHas('orders', ['code' => $order->code, 'customer_id' => $customer->id]);
    }

    /**
     * The Chef is forbidden to update the order.
     *
     * @return void
    */
    public function testTheChefIsForbiddenToUpdateTheOrders()
    {
    	$chef = factory(User::class)->create(['role_id' => 3]);
    	$order = factory(Order::class)->create(['waiter_id' => $chef->id]);
    	Passport::actingAs($chef);
    	$this->json('PATCH', '/api/order/'.$order->id, [])->assertStatus(403);
    }

    /**
     * the waiter is forbidden to update the order which made by other user.
     *
     * @return void
    */ 
    public function testTheWaiterIsForbiddenToUpdateTheOrderWhichMadeByOtherUser()
    {
    	$order = factory(Order::class)->create(['waiter_id' => factory(User::class)->create()]);
    	Passport::actingAs(factory(User::class)->create(['role_id' => 4]));
    	$this->json('PATCH', '/api/order/'.$order->id, [
    		'order_customer_id' => $order->customer_id,
            'order_table_id' => $order->table_id,
            'order_subtotal' => $order->subtotal,
            'order_tax' => $order->tax,
            'order_discount' => $order->discount,
    	])->assertStatus(403);
    }


    /**
     * the waiter can update the order he made.
     *
     * @return void
    */
    public function testTheWaiterCanUpdateTheOrdersHeMade()
    {
    	$waiter = factory(User::class)->create(['role_id' => 4]);
    	$order = factory(Order::class)->create(['waiter_id' => $waiter->id]);
    	Passport::actingAs($waiter);
    	$this->json('PATCH', '/api/order/'.$order->id, [
    		'order_customer_id' => $order->customer_id,
            'order_table_id' => $order->table_id,
            'order_subtotal' => 500.00,
            'order_tax' => $this->faker->numberBetween($min=1, $max=2),
            'order_discount' => $this->faker->numberBetween($min=1, $max=2),
    	])->assertStatus(200)->assertJsonStructure(['order']);
    	$this->assertDatabaseHas('orders', ['code' => $order->code, 'subtotal' => 500.00]);
    } 

    /**
     * manager can delete orders
     *
     * @return void
    */ 
    public function testTheManagerCanDeleteOrders()
    {
    	$order = factory(Order::class)->create();
    	Passport::actingAs(factory(User::class)->create(['role_id' => 2]));
    	$this->json('delete', '/api/order/'.$order->id)->assertStatus(200)->assertJsonStructure(['message']);
    	$this->assertDatabaseMissing('orders', ['id' => $order->id, 'code' => $order->code]);
    }

    /**
     * Waiters and chefs are prohibited from removing orders
     *
     * @return void
    */
    public function testTheWaiterAndTheChefAreProhibitedFromRemovingOrders()
    {
    	$order = factory(Order::class)->create();

    	//test for chef
    	Passport::actingAs(factory(User::class)->create(['role_id' => 3]));
    	$this->json('delete', '/api/order/'.$order->id)->assertStatus(403);

    	//test for waiter
    	Passport::actingAs(factory(User::class)->create(['role_id' => 4]));
    	$this->json('delete', '/api/order/'.$order->id)->assertStatus(403);

    	$this->assertDatabaseHas('orders', ['id' => $order->id, 'code' => $order->code]);
    }

    /**
     * Waiters are not permitted to accept orders
     *
     * @return void
    */
    public function testWaitersAreNotPermittedToAcceptOrders()
    {
    	$order = factory(Order::class)->create();

    	Passport::actingAs(factory(User::class)->create(['role_id' => 4]));
    	$this->json('PATCH', '/api/order/'.$order->id.'/accept')->assertStatus(403);

    	$this->assertDatabaseHas('orders', ['code' => $order->code, 'order_status_id' => 1]);
    }

    /**
     * chef or manager can accept orders
     *
     * @return void
    */
    public function testChefOrManagerCanAcceptOrder()
    {
    	//test for Chef
    	$this->acceptOrderWithSuccessResponse(factory(User::class)->create(['role_id' => 3]));

    	//test for manager
    	$this->acceptOrderWithSuccessResponse(factory(User::class)->create(['role_id' => 2]));
    }

    /**
     * accept order with success response
     *
     * @return void
    */
    public function acceptOrderWithSuccessResponse($user)
    {
    	$order = factory(Order::class)->create();
    	Passport::actingAs($user);
    	$this->json('PATCH', '/api/order/'.$order->id.'/accept')
    	->assertStatus(200)->assertJsonStructure(['order']);
    	$this->assertDatabaseHas('orders', [
    		'id' => $order->id,
    		'order_status_id' => 2
    	]);
    }

    /**
     * Waiters are not permitted to reject orders
     *
     * @return void
    */
    public function testWaitersAreNotPermittedToDeleteOrders()
    {
    	$order = factory(Order::class)->create();

    	Passport::actingAs(factory(User::class)->create(['role_id' => 4]));
    	$this->json('PATCH', '/api/order/'.$order->id.'/reject')->assertStatus(403);

    	$this->assertDatabaseHas('orders', ['code' => $order->code, 'order_status_id' => 1]);
    }

    /**
     * chef or manager can reject orders
     *
     * @return void
    */
    public function testChefOrManagerCanRejectOrder()
    {
    	//test for Chef
    	$this->rejectOrderWithSuccessResponse(factory(User::class)->create(['role_id' => 3]));

    	//test for manager
    	$this->rejectOrderWithSuccessResponse(factory(User::class)->create(['role_id' => 2]));
    }

    /**
     * accept order with success response
     *
     * @return void
    */
    public function rejectOrderWithSuccessResponse($user)
    {
    	$order = factory(Order::class)->create();
    	Passport::actingAs($user);
    	$this->json('PATCH', '/api/order/'.$order->id.'/reject')
    	->assertStatus(200)->assertJsonStructure(['order']);
    	$this->assertDatabaseHas('orders', [
    		'id' => $order->id,
    		'order_status_id' => 3
    	]);
    }

    /**
     * Waiters are not permitted to cook orders
     *
     * @return void
    */
    public function testWaitersAreNotPermittedToCookOrders()
    {
    	$order = factory(Order::class)->create(['order_status_id' => 2]);

    	Passport::actingAs(factory(User::class)->create(['role_id' => 4]));
    	$this->json('PATCH', '/api/order/'.$order->id.'/cook')->assertStatus(403);

    	$this->assertDatabaseHas('orders', ['code' => $order->code, 'order_status_id' => 2]);
    }

    /**
     * Chefs are not permitted to cook orders approved by other users
     *
     * @return void
    */
    public function testChefsAreNotPermittedToCookOrdersApprovedByOtherUsers()
    {
    	$order = factory(Order::class)->create([
    		'order_status_id' => 2,
    		'chef_id' => factory(User::class)->create(['role_id' => 3])->id
    	]);

    	Passport::actingAs(factory(User::class)->create(['role_id' => 3]));
    	$this->json('PATCH', '/api/order/'.$order->id.'/cook')->assertStatus(403);

    	$this->assertDatabaseHas('orders', ['code' => $order->code, 'order_status_id' => 2]); 
    }

    /**
     * The chef is allowed to cook orders approved by him
     *
     * @return void
    */
    public function testChefIsAllowedToCookOrdersApprovedByHim()
    {
    	$chef = factory(User::class)->create(['role_id' => 3]);
    	$order = factory(Order::class)->create([
    		'order_status_id' => 2,
    		'chef_id' => $chef->id
    	]);

    	Passport::actingAs($chef);
    	$this->json('PATCH', '/api/order/'.$order->id.'/cook')->assertStatus(200)
    	->assertJsonStructure(['order']);

    	$this->assertDatabaseHas('orders', [
    		'id' => $order->id, 
    		'order_status_id' => 4,
    		'chef_id' => $chef->id
    	]); 
    } 

    /**
     * The waiter is not permitted to set the order status to be cooked
     *
     * @return void
    */
    public function testWaitersIsNotPermittedToSetTheOrderStatusToBeCooked()
    {
    	$order = factory(Order::class)->create(['order_status_id' => 4]);

    	Passport::actingAs(factory(User::class)->create(['role_id' => 4]));
    	$this->json('PATCH', '/api/order/'.$order->id.'/set-cooked')->assertStatus(403);

    	$this->assertDatabaseHas('orders', ['code' => $order->code, 'order_status_id' => 4]);
    }

    /**
     * The chef is not permitted to set the status of orders approved by other users to be cooked
     *
     * @return void
    */
    public function testChefsAreNotPermittedToSetTheStatusOfOrdersApprovedByOtherUsersTobeCooked()
    {
    	$order = factory(Order::class)->create([
    		'order_status_id' => 4,
    		'chef_id' => factory(User::class)->create(['role_id' => 3])->id
    	]);

    	Passport::actingAs(factory(User::class)->create(['role_id' => 3]));
    	$this->json('PATCH', '/api/order/'.$order->id.'/set-cooked')->assertStatus(403);

    	$this->assertDatabaseHas('orders', ['code' => $order->code, 'order_status_id' => 4]); 
    }

    /**
     * The chef is allowed to set order status to be cooked if the order cook by him
     *
     * @return void
    */
    public function testChefIsAllowedToSetTheOrdersToBeCooked()
    {
    	$chef = factory(User::class)->create(['role_id' => 3]);
    	$order = factory(Order::class)->create([
    		'order_status_id' => 4,
    		'chef_id' => $chef->id
    	]);

    	Passport::actingAs($chef);
    	$this->json('PATCH', '/api/order/'.$order->id.'/set-cooked')->assertStatus(200)
    	->assertJsonStructure(['order']);

    	$this->assertDatabaseHas('orders', [
    		'id' => $order->id, 
    		'order_status_id' => 5,
    		'chef_id' => $chef->id
    	]); 
    }

    /**
     * The chef is not permitted to set order status to be finished
     *
     * @return void
    */
    public function testChefIsPermittedToSetOrderStatusTobeFinished()
    {
    	$chef = factory(User::class)->create(['role_id' => 3]);
    	$order = factory(Order::class)->create([
    		'order_status_id' => 5,
    		'chef_id' => $chef->id
    	]);

    	Passport::actingAs($chef);
    	$this->json('PATCH', '/api/order/'.$order->id.'/set-finished')->assertStatus(403);
    	
    	$this->assertDatabaseHas('orders', [
    		'id' => $order->id, 
    		'order_status_id' => 5,
    		'chef_id' => $chef->id,
    		'end_time' => null
    	]); 
    } 

    /**
     * The waiter is allowed to set order status to be finished, 
     *
     * @return void
    */
    public function testWaiterIsAllowedToSetOrderStatusTobeFinished()
    {
    	$waiter = factory(User::class)->create(['role_id' => 4]);
    	$order = factory(Order::class)->create([
    		'order_status_id' => 5,
    		'chef_id' => factory(User::class)->create(['role_id' => 3])->id,
    		'waiter_id' => $waiter->id
    	]);

    	Passport::actingAs($waiter);
    	$this->json('PATCH', '/api/order/'.$order->id.'/set-finished')->assertStatus(200)
    	->assertJsonStructure(['order']);

    	$this->assertDatabaseHas('orders', [
    		'id' => $order->id, 
    		'order_status_id' => 6
    	]); 
    }

    /**
     * The waiter is permitted to set order status to be finished, if current order status isn't cooked 
     *
     * @return void
    */
    public function testWaiterIsNotPermittedToSetOrderStatusTobeFinished()
    {
    	$waiter = factory(User::class)->create(['role_id' => 4]);
    	$order = factory(Order::class)->create([
    		'order_status_id' => 2,
    		'chef_id' => factory(User::class)->create(['role_id' => 3])->id,
    		'waiter_id' => $waiter->id
    	]);

    	Passport::actingAs($waiter);
    	$this->json('PATCH', '/api/order/'.$order->id.'/set-finished')->assertStatus(403);

    	$this->assertDatabaseHas('orders', [
    		'id' => $order->id, 
    		'order_status_id' => 2,
    		'end_time' => null
    	]); 
    }
}
