<?php

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use App\OrderStatus;

class OrderStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $statuses = [
        	[
        		'id' => 1,
        		'name' => 'Pending'
        	],
        	[
        		'id' => 2,
        		'name' => 'Accepted'
        	],
        	[
        		'id' => 3,
        		'name' => 'Rejected'
        	],
        	[
        		'id' => 4,
        		'name' => 'Cooking'
        	],
        	[
        		'id' => 5,
        		'name' => 'Cooked'
        	],
        	[
        		'id' => 6,
        		'name' => 'Finished'
        	],
        ];

        foreach ($statuses as $status) {
        	OrderStatus::create([
        		'id' => $status['id'],
        		'name' => $status['name'],
        		'description' => $faker->paragraph
        	]);
        }
    }
}
