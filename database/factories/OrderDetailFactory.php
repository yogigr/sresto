<?php

use Faker\Generator as Faker;
use App\Order;
use App\Dish;

$factory->define(App\OrderDetail::class, function (Faker $faker) {
    $order = factory(Order::class)->create();
    $dish = factory(Dish::class)->create();
    return [
        'order_id' => $order->id,
        'dish_id' => $dish->id,
        'quantity' => 1,
        'price' => $dish->price,
    ];
});
