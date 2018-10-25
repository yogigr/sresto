<?php

namespace Tests\Feature\api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use App\Payment;
use App\Order;
use App\User;
use App\GiftCard;

class PaymentTest extends TestCase
{
	use WithFaker;

	/**
     * tess pay order with cash
     *
     * @return void
    */ 
	public function testPayOrderWithCash()
	{
		//test for chef
		$this->payOrderWithCashForbiddenResponse(factory(User::class)->create(['role_id' => 3]));
		//test for waiter
		$this->payOrderWithCashSuccessResponse(factory(User::class)->create(['role_id' => 4]));
		$this->payOrderWithCashForbiddenResponse(factory(User::class)->create(['role_id' => 4]));
		//test for manager
		$this->payOrderWithCashSuccessResponse(factory(User::class)->create(['role_id' => 2]));
		$this->payOrderWithCashForbiddenResponse(factory(User::class)->create(['role_id' => 2]));
	}
	

	/**
     * tess pay order with credit card
     *
     * @return void
    */
    public function testPayOrderWithCreditCard()
    {
    	//test for chef
    	$this->payOrderWithCreditCardForbiddenResponse(factory(User::class)->create(['role_id' => 3]));
    	//test for waiter
    	$this->payOrderWithCreditCardSuccessResponse(factory(User::class)->create(['role_id' => 4]));
    	$this->payOrderWithCreditCardForbiddenResponse(factory(User::class)->create(['role_id' => 4]));
		//test for manager
		$this->payOrderWithCreditCardSuccessResponse(factory(User::class)->create(['role_id' => 2]));
		$this->payOrderWithCreditCardForbiddenResponse(factory(User::class)->create(['role_id' => 2]));
    }

    /**
     * tess pay order with gift card
     *
     * @return void
    */
    public function testPayOrderWithGiftCard()
    {
    	//test for chef
    	$this->payOrderWithGiftCardForbiddenResponse(factory(User::class)->create(['role_id' => 3]));
    	//test for waiter
    	$this->payOrderWithGiftCardSuccessResponse(factory(User::class)->create(['role_id' => 4]));
    	$this->payOrderWithGiftCardForbiddenResponse(factory(User::class)->create(['role_id' => 4]));
		//test for manager
		$this->payOrderWithGiftCardSuccessResponse(factory(User::class)->create(['role_id' => 2]));
		$this->payOrderWithGiftCardForbiddenResponse(factory(User::class)->create(['role_id' => 2]));
    }

    /**
     * test pay order with gift card return 422 caused card periode has expire
     *
     * @return void
    */
    public function testGiftCardHasExpire()
    {
    	$waiter = factory(User::class)->create(['role_id' => 4]);
    	$giftCard = factory(GiftCard::class)->create(['expiration_date' => now()->subDay()->toDateString()]);
    	$order = factory(Order::class)->create(['waiter_id' => $waiter->id]);

    	Passport::actingAs($waiter);
    	$this->json('POST', '/api/order/'.$order->id.'/pay', [
    		'payment_amount' => $order->subtotal + $order->tax - $order->discount,
    		'payment_change' => 0,
    		'payment_method_id' => 3,
    		'gift_card_id' => $giftCard->id
    	])->assertStatus(422)
    	->assertJson([
    		'message' => 'The given data was invalid.',
    		'errors' => [
    			'gift_card_id' => [
    				0 => "card period has expired"
    			]
    		]
    	]);
    	$this->assertDatabaseMissing('payments', ['order_id' => $order->id]);
    }

    /**
     * test order payments with gift cards where the balance is insufficient
     *
     * @return void
    */
    public function testOrderPaymentsWithGiftCardWhereTheBalanceIsInsufficient()
    {
        $waiter = factory(User::class)->create(['role_id' => 4]);
        $giftCard = factory(GiftCard::class)->create(['value' => 10.00]);
        $order = factory(Order::class)->create(['waiter_id' => $waiter->id, 'subtotal' => 12.00, 'tax' => 0, 'discount' => 0]);

        Passport::actingAs($waiter);
        $this->json('POST', '/api/order/'.$order->id.'/pay', [
            'payment_amount' => $order->subtotal + $order->tax - $order->discount,
            'payment_change' => 0,
            'payment_method_id' => 3,
            'gift_card_id' => $giftCard->id
        ])->assertStatus(422)
        ->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'gift_card_id' => [
                    0 => "insufficient gift card balance."
                ]
            ]
        ]);
        $this->assertDatabaseMissing('payments', ['order_id' => $order->id]);
    }

    /**
     * pay order with GiftCard forbidden response
     *
     * @return void
    */
    public function payOrderWithGiftCardForbiddenResponse($user)
    {
    	$giftCard = factory(GiftCard::class)->create();
    	$order = factory(Order::class)->create();
    	Passport::actingAs($user);
    	$this->json('POST', '/api/order/'.$order->id.'/pay', [
    		'payment_amount' => $order->subtotal + $order->tax - $order->discount,
    		'payment_change' => 0,
    		'payment_method_id' => 3,
    		'gift_card_id' => $giftCard->id
    	])->assertStatus(403);
    	$this->assertDatabaseMissing('payments', ['order_id' => $order->id]);
    }

    /**
     * pay order with GiftCard success response
     *
     * @return void
    */
    public function payOrderWithGiftCardSuccessResponse($user)
    {
    	$giftCard = factory(GiftCard::class)->create();
    	$order = factory(Order::class)->create(['waiter_id' => $user->id]);
    	Passport::actingAs($user);
    	$this->json('POST', '/api/order/'.$order->id.'/pay', [
    		'payment_amount' => $order->subtotal + $order->tax - $order->discount,
    		'payment_change' => 0,
    		'payment_method_id' => 3,
    		'gift_card_id' => $giftCard->id
    	])->assertStatus(200)->assertJsonStructure(['message', 'payment', 'order']);
    	$this->assertDatabaseHas('payments', ['order_id' => $order->id]);
    }

	/**
     * pay order with credit card forbidden response
     *
     * @return void
    */
    public function payOrderWithCreditCardForbiddenResponse($user)
    {
    	$order = factory(Order::class)->create();
    	Passport::actingAs($user);
    	$this->json('POST', '/api/order/'.$order->id.'/pay', [
    		'payment_amount' => $order->subtotal + $order->tax - $order->discount,
    		'payment_change' => 0,
    		'payment_method_id' => 2,
    		'credit_card_number' => $this->faker->creditCardNumber,
    		'credit_card_expiration_year' => '2019',
    		'credit_card_expiration_month' => '01',
    		'credit_card_cvc' => '123'
    	])->assertStatus(403);
    	$this->assertDatabaseMissing('payments', ['order_id' => $order->id]);
    }

    /**
     * pay order with credit card success response
     *
     * @return void
    */
    public function payOrderWithCreditCardSuccessResponse($user)
    {
    	$order = factory(Order::class)->create(['waiter_id' => $user->id]);
    	Passport::actingAs($user);
    	$this->json('POST', '/api/order/'.$order->id.'/pay', [
    		'payment_amount' => $order->subtotal + $order->tax - $order->discount,
    		'payment_change' => 0,
    		'payment_method_id' => 2,
    		'credit_card_number' => $this->faker->creditCardNumber,
    		'credit_card_expiration_year' => '2019',
    		'credit_card_expiration_month' => '01',
    		'credit_card_cvc' => '123'
    	])->assertStatus(200)->assertJsonStructure(['message', 'payment', 'order']);
    	$this->assertDatabaseHas('payments', ['order_id' => $order->id]);
    }


    /**
     * pay order with cash forbidden response
     *
     * @return void
     */
    public function payOrderWithCashForbiddenResponse($user)
    {
    	$order = factory(Order::class)->create();
    	Passport::actingAs($user);
    	$this->json('POST', '/api/order/'.$order->id.'/pay', [
    		'payment_amount' => $order->subtotal + $order->tax - $order->discount,
    		'payment_change' => 0,
    		'payment_method_id' => 1
       	])->assertStatus(403);
       	$this->assertDatabaseMissing('payments', ['order_id' => $order->id]);
    }

    /**
     * pay order with cash success response
     *
     * @return void
     */
    public function payOrderWithCashSuccessResponse($user)
    {
    	$order = factory(Order::class)->create(['waiter_id' => $user->id]);
    	Passport::actingAs($user);
    	$this->json('POST', '/api/order/'.$order->id.'/pay', [
    		'payment_amount' => $order->subtotal + $order->tax - $order->discount,
    		'payment_change' => 0,
    		'payment_method_id' => 1
       	])->assertStatus(200)->assertJsonStructure(['message', 'payment', 'order']);
       	$this->assertDatabaseHas('payments', ['order_id' => $order->id]);
    }
}
