<?php

namespace Tests\Feature\api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use App\Cart;
use App\User;
use App\Dish;

class CartTest extends TestCase
{
    /**
     * Waiter can view his carts test.
     *
     * @return void
     */
    public function testWaiterCanViewHisCarts()
    {
        $waiter = factory(User::class)->create(['role_id' => 4]);
        $carts = factory(Cart::class)->create(['user_id' => $waiter->id]);
        Passport::actingAs($waiter);
        $this->json('GET', '/api/cart')->assertStatus(200)->assertJsonStructure(['carts']);
    }

    /**
     * Manager can view his carts test.
     *
     * @return void
    */
    public function testManagerCanViewHisCarts()
    {
    	$manager = factory(User::class)->create(['role_id' => 2]);
    	$cart = factory(Cart::class)->create(['user_id' => $manager->id]);
    	Passport::actingAs($manager);
    	$this->json('GET', '/api/cart')->assertStatus(200)->assertJsonStructure(['carts']);
    }

    /**
     * Admin can view his carts test.
     *
     * @return void
    */
    public function testAdminCanViewHisCarts()
    {
    	$admin = factory(User::class)->create(['role_id' => 1]);
    	$cart = factory(Cart::class)->create(['user_id' => $admin->id]);
    	Passport::actingAs($admin);
    	$this->json('GET', '/api/cart')->assertStatus(200)->assertJsonStructure(['carts']);
    }

    /**
     * Chef can not view his carts test.
     *
     * @return void
    */
    public function testCheftCantViewHisCarts()
    {
    	$chef = factory(User::class)->create(['role_id' => 3]);
    	$cart = factory(Cart::class)->create(['user_id' => $chef->id]);
    	Passport::actingAs($chef);
    	$this->json('GET', '/api/cart')->assertStatus(403);
    }

    /**
     * store cart test.
     *
     * @return void
    */
    public function testStoreCart()
    {
    	//admin test
    	$this->storeCartWithSuccessResponse(factory(User::class)->create(['role_id' => 1]));

    	//manager test
    	$this->storeCartWithSuccessResponse(factory(User::class)->create(['role_id' => 2]));

    	//waiter test
    	$this->storeCartWithSuccessResponse(factory(User::class)->create(['role_id' => 4]));

    	//chef test
    	$this->storeCartWithForbiddenResponse(factory(User::class)->create(['role_id' => 3]));
    }

    /**
     * store / add cart with success response.
     *
     * @return void
    */
    public function storeCartWithSuccessResponse($user)
    {
    	Passport::actingAs($user);
    	$this->json('POST', '/api/cart', [
    		'dish_id' => factory(Dish::class)->create()->id,
    		'quantity' => 1,
    		'user_id' => $user->id
    	])->assertStatus(201)->assertJsonStructure(['cart']);
    }

    /**
     * store / add cart with forbidden response.
     *
     * @return void
    */
    public function storeCartWithForbiddenResponse($user)
    {
    	Passport::actingAs($user);
    	$this->json('POST', '/api/cart', [
    		'dish_id' => factory(Dish::class)->create()->id,
    		'quantity' => 1,
    		'user_id' => $user->id
    	])->assertStatus(403);
    }

    /**
     * update cart test.
     *
     * @return void
    */
    public function testUpdateCart()
    {
    	//admin test
    	$this->updateCartWithSuccessResponse(factory(User::class)->create(['role_id' => 1]));

    	//manager test
    	$this->updateCartWithSuccessResponse(factory(User::class)->create(['role_id' => 2]));

    	//waiter test
    	$this->updateCartWithSuccessResponse(factory(User::class)->create(['role_id' => 4]));

    	//chef test
    	$this->updateCartWithForbiddenResponse(factory(User::class)->create(['role_id' => 3]));

    	//manager test return forbidden response caused update the cart is made by other manager
    	$this->updateCartWithForbiddenResponseCausedDifferentUser(factory(User::class)->create(['role_id' => 2]));

    }

    /**
     * update cart with success response.
     *
     * @return void
    */
    public function updateCartWithSuccessResponse($user)
    {
    	$cart = factory(Cart::class)->create(['user_id' => $user->id]);
    	Passport::actingAs($user);
    	$this->json('PATCH', '/api/cart/'.$cart->id, [
    		'quantity' => 2
    	])->assertStatus(200)->assertJsonStructure(['cart']);
    	$this->assertDatabaseHas('carts', [
    		'id' => $cart->id,
    		'dish_id' => $cart->dish_id,
    		'quantity' => 2
    	]);
    }	

    /**
     * update cart with forbidden response.
     *
     * @return void
    */
    public function updateCartWithForbiddenResponse($user)
    {
    	$cart = factory(Cart::class)->create(['user_id' => $user->id, 'quantity' => 1]);
    	Passport::actingAs($user);
    	$this->json('PATCH', '/api/cart/'.$cart->id, [
    		'quantity' => 2
    	])->assertStatus(403);
    	$this->assertDatabaseHas('carts', [
    		'id' => $cart->id,
    		'dish_id' => $cart->dish_id,
    		'quantity' => 1
    	]);
    }

    /**
     * update cart with forbidden response.
     *
     * @return void
    */
    public function updateCartWithForbiddenResponseCausedDifferentUser($user)
    {
    	$cart = factory(Cart::class)->create(['user_id' => $user->id, 'quantity' => 1]);
    	Passport::actingAs(factory(User::class)->create(['role_id' => 2]));
    	$this->json('PATCH', '/api/cart/'.$cart->id, [
    		'quantity' => 2
    	])->assertStatus(403);
    	$this->assertDatabaseHas('carts', [
    		'id' => $cart->id,
    		'dish_id' => $cart->dish_id,
    		'quantity' => 1
    	]);
    }

    /**
     * delete cart test.
     *
     * @return void
    */
    public function testDeleteCart()
    {
    	//admin test
    	$this->deleteCartWithSuccessResponse(factory(User::class)->create(['role_id' => 1]));

    	//manager test
    	$this->deleteCartWithSuccessResponse(factory(User::class)->create(['role_id' => 2]));

    	//waiter test
    	$this->deleteCartWithSuccessResponse(factory(User::class)->create(['role_id' => 4]));

    	//chef test
    	$this->deleteCartWithForbiddenResponse(factory(User::class)->create(['role_id' => 3]));

    	//manager test return forbidden response caused update the cart is made by other manager
    	$this->deleteCartWithForbiddenResponseCausedDifferentUser(factory(User::class)->create(['role_id' => 2]));

    }

    /**
     * delete cart with success response.
     *
     * @return void
    */
    public function deleteCartWithSuccessResponse($user)
    {
    	$cart = factory(Cart::class)->create(['user_id' => $user->id]);
    	Passport::actingAs($user);
    	$this->json('delete', '/api/cart/'.$cart->id)->assertStatus(200)->assertJsonStructure(['message']);
    	$this->assertDatabaseMissing('carts', [
    		'id' => $cart->id,
    		'dish_id' => $cart->dish_id
    	]);
    }	

    /**
     * delete cart with forbidden response.
     *
     * @return void
    */
    public function deleteCartWithForbiddenResponse($user)
    {
    	$cart = factory(Cart::class)->create(['user_id' => $user->id, 'quantity' => 1]);
    	Passport::actingAs($user);
    	$this->json('delete', '/api/cart/'.$cart->id)->assertStatus(403);
    	$this->assertDatabaseHas('carts', [
    		'id' => $cart->id,
    		'dish_id' => $cart->dish_id,
    		'quantity' => 1
    	]);
    }

    /**
     * delete cart with forbidden response.
     *
     * @return void
    */
    public function deleteCartWithForbiddenResponseCausedDifferentUser($user)
    {
    	$cart = factory(Cart::class)->create(['user_id' => $user->id, 'quantity' => 1]);
    	Passport::actingAs(factory(User::class)->create(['role_id' => 2]));
    	$this->json('delete', '/api/cart/'.$cart->id)->assertStatus(403);
    	$this->assertDatabaseHas('carts', [
    		'id' => $cart->id,
    		'dish_id' => $cart->dish_id,
    		'quantity' => 1
    	]);
    }				
}
