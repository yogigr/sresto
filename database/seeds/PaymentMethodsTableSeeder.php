<?php

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use App\PaymentMethod;

class PaymentMethodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $methods = [
            [
                'id' => 1,
                'name' => 'Cash'
            ],
            [
                'id' => 2,
                'name' => 'Credit Card'
            ],
            [
                'id' => 3,
                'name' => 'Gift Card'
            ]
        ];

        foreach ($methods as $method) {
        	PaymentMethod::create([
                'id' => $method['id'],
        		'name' => $method['name'],
        		'description' => $faker->paragraph,
        	]);
        }
    }
}
