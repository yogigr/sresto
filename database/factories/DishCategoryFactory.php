<?php

use Faker\Generator as Faker;

$factory->define(App\DishCategory::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->sentence($nbWords=2),
        'description' => $faker->paragraph,
        'user_id' => 1
    ];
});
