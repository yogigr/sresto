<?php

use Faker\Generator as Faker;

$factory->define(App\Table::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->sentence($nbWords=2),
        'user_id' => 1,
        'is_in_use' => false
    ];
});
