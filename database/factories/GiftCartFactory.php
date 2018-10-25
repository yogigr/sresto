<?php

use Faker\Generator as Faker;

$factory->define(App\GiftCard::class, function (Faker $faker) {
    return [
        'card_number' => $faker->unique()->numerify('############'),
        'value' => $faker->numberBetween($min=10, $max=50),
        'expiration_date' => now()->addMonth()->toDateString(),
        'user_id' => factory(\App\User::class)->create(['role_id' => 1])
    ];
});
