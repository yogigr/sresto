<?php

use Faker\Generator as Faker;
use App\Traits\CodeGenerator;
use App\Customer;
use App\Table;
use App\User;

$factory->define(App\Order::class, (new Class {
	use CodeGenerator;
	public function generatorFunction() {
		return function(Faker $faker) {
			$customer = factory(Customer::class)->create();
			$table = factory(Table::class)->create();
			$waiter = factory(User::class)->create(['role_id' => 4]);
			$chef = factory(User::class)->create(['role_id' => 3]);
			return [
				'code' => $this->getCodeWithDatetime('OR', 'orders'),
				'customer_id' => $customer->id,
				'table_id' => $table->id,
				'subtotal' => $faker->numberBetween($min=5, $max=10),
				'tax' => $faker->numberBetween($min=1, $max=5),
				'discount' => $faker->numberBetween($min=1, $max=5),
				'waiter_id' => $waiter->id,
				'chef_id' => $chef->id,
				'order_status_id' => 1,
				'start_time' => now()
			];
		};
	}
})->generatorFunction());
