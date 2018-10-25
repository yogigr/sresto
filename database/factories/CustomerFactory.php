<?php

use Faker\Generator as Faker;
use App\User;

$factory->define(App\Customer::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'phone' => $faker->unique()->phoneNumber,
        'address' => $faker->unique()->address,
        'user_id' => 1
    ];
});
