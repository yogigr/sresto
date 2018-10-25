<?php

use Faker\Generator as Faker;
use App\Traits\CodeGenerator;
use App\DishCategory;

$factory->define(App\Dish::class, (new Class {
	use CodeGenerator;
	public function generatorFunction() {
		return function(Faker $faker) {
			$code = $this->getCode('DS', 'dishes');
			return [
				'code' => $code,
				'name' => $faker->unique()->sentence($nbWords=2),
				'description' => $faker->paragraph,
				'dish_category_id' => DishCategory::inRandomOrder()->first()->id,
				'price' => $faker->numberBetween($min=1, $max=10),
				'user_id' => 1
			];
		};
	}
})->generatorFunction());
