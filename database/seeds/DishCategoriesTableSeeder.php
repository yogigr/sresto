<?php

use Faker\Generator as Faker;
use Illuminate\Database\Seeder;
use App\DishCategory;
use App\User;

class DishCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $dishCategories = [
        	[
        		'id' => 1,
        		'name' => 'Beef'
        	],
        	[
        		'id' => 2,
        		'name' => 'Breakfast'
        	],
        	[
        		'id' => 3,
        		'name' => 'Chicken'
        	],
        	[
        		'id' => 4,
        		'name' => 'Desert'
        	],
        	[
        		'id' => 5,
        		'name' => 'Lamb'
        	],
        	[
        		'id' => 6,
        		'name' => 'Miscellaneous'
        	],
        	[
        		'id' => 7,
        		'name' => 'Pasta'
        	],
        	[
        		'id' => 8,
        		'name' => 'Pork'
        	],
        	[
        		'id' => 9,
        		'name' => 'Seafood'
        	],
        	[
        		'id' => 10,
        		'name' => 'Side'
        	],
        	[
        		'id' => 11,
        		'name' => 'Starter'
        	],
        	[
        		'id' => 12,
        		'name' => 'Vegetarian'
        	],
        ];

        $admin = User::where('role_id', 1)->firstOrFail();

        foreach ($dishCategories as $cat) {
        	DishCategory::create([
        		'id' => $cat['id'],
        		'name' => $cat['name'],
        		'description' => $faker->paragraph,
        		'user_id' => $admin->id 
        	]);
        }
        
    }
}
