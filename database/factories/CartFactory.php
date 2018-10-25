<?php

use Faker\Generator as Faker;
use App\Dish;
use App\User;

$factory->define(App\Cart::class, function (Faker $faker) {
	$dish = factory(Dish::class)->create();
	$user = factory(User::class)->create();
    return [
        'dish_id' => $dish->id,
        'quantity' => 1,
        'user_id' => $user->id
    ];
});
