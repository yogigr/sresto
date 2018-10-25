<?php

namespace Tests\Feature\api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use App\OrderDetail;
use App\Order;
use App\Dish;
use App\User;

class OrderDetailTest extends TestCase
{
    /**
     * test store order detail.
     *
     * @return void
     */
    public function testStoreOrderDetail()
    {
        //test for manager
        $this->storeOrderDetailWithSuccessResponse(factory(User::class)->create(['role_id' => 2]));

        //test for chef
        $this->storeOrderDetailWithForbiddenResponse(factory(User::class)->create(['role_id' => 3]));

        //test for waiter
        $this->storeOrderDetailWithSuccessResponse(factory(User::class)->create(['role_id' => 4]));
    }

    /**
     * test update order detail.
     *
     * @return void
     */
    public function testUpdateOrderDetail()
    {
        //test for manager
        $this->updateOrderDetailWithSuccessResponse(factory(User::class)->create(['role_id' => 2]));

        //test for chef
        $this->updateOrderDetailWithForbiddenResponse(factory(User::class)->create(['role_id' => 3]));

        //test for waiter
        $this->updateOrderDetailWithSuccessResponse(factory(User::class)->create(['role_id' => 4]));
    }

    /**
     * test delete order detail.
     *
     * @return void
     */
    public function testDeleteOrderDetail()
    {
        //test for manager
        $this->deleteOrderDetailWithSuccessResponse(factory(User::class)->create(['role_id' => 2]));

        //test for chef
        $this->deleteOrderDetailWithForbiddenResponse(factory(User::class)->create(['role_id' => 3]));

        //test for waiter
        $this->deleteOrderDetailWithSuccessResponse(factory(User::class)->create(['role_id' => 4]));
    }

    /**
     * store order detail with success response.
     *
     * @return void
     */
    public function storeOrderDetailWithSuccessResponse($user)
    {
    	$order = factory(Order::class)->create(['waiter_id' => $user->id]);
    	$dish = factory(Dish::class)->create();
    	Passport::actingAs($user);
    	$this->json('POST', '/api/order-detail/'.$order->id.'/store', [
    		'dish_id' => $dish->id,
    		'quantity' => 1,
    		'price' => $dish->price,
    		'order_id' => $order->id
    	])->assertStatus(201)->assertJsonStructure(['orderDetail']);
    	$this->assertDatabaseHas('order_details', [
    		'order_id' => $order->id,
    		'dish_id' => $dish->id,
    		'quantity' => 1,
    		'price' => $dish->price
    	]);
    }

    /**
     * store order detail with forbidden response.
     *
     * @return void
    */
    public function storeOrderDetailWithForbiddenResponse($user)
    {
    	$order = factory(Order::class)->create(['waiter_id' => $user->id]);
    	$dish = factory(Dish::class)->create();
    	Passport::actingAs($user);
    	$this->json('POST', '/api/order-detail/'.$order->id.'/store', [
    		'dish_id' => $dish->id,
    		'quantity' => 1,
    		'price' => $dish->price,
    		'order_id' => $order->id
    	])->assertStatus(403);
    	$this->assertDatabaseMissing('order_details', ['order_id' => $order->id]);
    }

    /**
     * update order detail with success response.
     *
     * @return void
     */
    public function updateOrderDetailWithSuccessResponse($user)
    {
    	$order = factory(Order::class)->create(['waiter_id' => $user->id]);
    	$orderDetail = factory(OrderDetail::class)->create(['order_id' => $order->id]);
    	Passport::actingAs($user);
    	$this->json('PATCH', '/api/order-detail/'.$orderDetail->id, [
    		'quantity' => 2,
    		'price' => 2 * $orderDetail->dish->price
    	])->assertStatus(200)->assertJsonStructure(['orderDetail']);
    	$this->assertDatabaseHas('order_details', [
    		'order_id' => $order->id,
    		'quantity' => 2,
    		'price' => 2 * $orderDetail->dish->price
    	]);
    }

    /**
     * update order detail with forbidden response.
     *
     * @return void
     */
    public function updateOrderDetailWithForbiddenResponse($user)
    {
    	$order = factory(Order::class)->create(['waiter_id' => $user->id]);
    	$orderDetail = factory(OrderDetail::class)->create(['order_id' => $order->id]);
    	Passport::actingAs($user);
    	$this->json('PATCH', '/api/order-detail/'.$orderDetail->id, [
    		'quantity' => 2,
    		'price' => 2 * $orderDetail->dish->price
    	])->assertStatus(403);
    }

    /**
     * delete order detail with success response.
     *
     * @return void
     */
    public function deleteOrderDetailWithSuccessResponse($user)
    {
    	$order = factory(Order::class)->create(['waiter_id' => $user->id]);
    	$orderDetail = factory(OrderDetail::class)->create(['order_id' => $order->id]);
    	Passport::actingAs($user);
    	$this->json('delete', '/api/order-detail/'.$orderDetail->id)->assertStatus(200);
    	$this->assertDatabaseMissing('order_details', [
    		'order_id' => $order->id,
    		'dish_id' => $orderDetail->dish_id
    	]);
    }

    /**
     * delete order detail with Forbidden response.
     *
     * @return void
     */
    public function deleteOrderDetailWithForbiddenResponse($user)
    {
    	$order = factory(Order::class)->create(['waiter_id' => $user->id]);
    	$orderDetail = factory(OrderDetail::class)->create(['order_id' => $order->id]);
    	Passport::actingAs($user);
    	$this->json('delete', '/api/order-detail/'.$orderDetail->id)->assertStatus(403);
    	$this->assertDatabaseHas('order_details', [
    		'order_id' => $order->id,
    		'dish_id' => $orderDetail->dish_id
    	]);
    }
}
