<?php

use Faker\Generator as Faker;
use App\Order;
use App\User;

$factory->define(App\Payment::class, function (Faker $faker) {
	$order = factory(Order::class)->create();
	$user = factory(User::class)->create();
    return [
        'order_id' => $order->id,
        'amount' => $order->subtotal + $order->tax - $order->discount,
        'change' => 0,
        'payment_method_id' => 1,
        'credit_card_number' => null,
        'credit_card_expiration_year' => null,
        'credit_card_expiration_month' => null,
        'credit_card_cvc' => null,
        'gift_card_id' => null,
        'user_id' => $user->id
    ];
});
