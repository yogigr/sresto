<?php

use Faker\Generator as Faker;
use Illuminate\Database\Seeder;
use App\Traits\CodeGenerator;
use App\Dish;
use App\DishCategory;
use App\User;

class DishesTableSeeder extends Seeder
{
    use CodeGenerator;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $dishes = [

			//beef
        	
        	[
        		'name' => 'Beef and Mustard Pie',
        		'category_id' => 1,
        		'image' => 'beef-and-mustard-pie.jpg'
        	],
        	[
        		'name' => 'Beef and Oyster pie',
        		'category_id' => 1,
        		'image' => 'beef-and-oyster-pie.jpg'
        	],
        	[
        		'name' => 'Beef Bourguignon',
        		'category_id' => 1,
        		'image' => 'beef-bourguignon.jpg'
        	],
        	[
        		'name' => 'Beef Brisket Pot Roast',
        		'category_id' => 1,
        		'image' => 'beef_brisket_pot_roast.jpg'
        	],
        	[
        		'name' => 'Beef Dumpling Stew',
        		'category_id' => 1,
        		'image' => 'beef-dumpling-stew.jpg'
        	],

        	//breakfast
        	[
        		'name' => 'English Breakfast',
        		'category_id' => 2,
        		'image' => 'english-breakfast.jpg'
        	],
        	[
        		'name' => 'Full English Breakfast',
        		'category_id' => 2,
        		'image' => 'full-english-breakfast.jpg'
        	],

        	//Chicken
        	[
        		'name' => 'Brown Stew Chicken',
        		'category_id' => 3,
        		'image' => 'brown-stew-chicken.jpg'
        	],
        	[
        		'name' => 'Chicken & mushroom Hotpot',
        		'category_id' => 3,
        		'image' => 'chicken-mushroom-hotpot.jpg'
        	],
        	[
        		'name' => 'Chicken Alfredo Primavera',
        		'category_id' => 3,
        		'image' => 'chicken-alfredo-primavera.jpg'
        	],

        	//desert
        	[
        		'name' => 'Apple Frangipan Tart',
        		'category_id' => 4,
        		'image' => 'apple-frangipan-tart.jpg'
        	],
        	[
        		'name' => 'Banana Pancakes',
        		'category_id' => 4,
        		'image' => 'banana-pancakes.jpg'
        	],
        	[
        		'name' => 'Battenberg Cake',
        		'category_id' => 4,
        		'image' => 'battenberg-cake.jpg'
        	],

        	//lamb
        	[
        		'name' => 'Kapsalon',
        		'category_id' => 5,
        		'image' => 'kapsalon.jpg'
        	],
        	[
        		'name' => 'Lamb and Potato pie',
        		'category_id' => 5,
        		'image' => 'lamb-and-potato-pie.jpg'
        	],
        	[
        		'name' => 'Lamb Biryani',
        		'category_id' => 5,
        		'image' => 'lamb-biryani.jpg'
        	],

        	//Miscellaneous
        	[
        		'name' => 'Bean & Sausage Hotpot',
        		'category_id' => 6,
        		'image' => 'bean-sausage-hotpot.jpg'
        	],
        	[
        		'name' => 'Callaloo Jamaican Style',
        		'category_id' => 6,
        		'image' => 'callaloo-Jamaican-style.jpg'
        	],
        	[
        		'name' => 'Duck Confit',
        		'category_id' => 6,
        		'image' => 'duck-confit.jpg'
        	],

        	//Pasta
        	[
        		'name' => 'Chilli prawn linguine',
        		'category_id' => 7,
        		'image' => 'chilli-prawn-linguine.jpg'
        	],
        	[
        		'name' => 'Grilled Mac and Cheese Sandwich',
        		'category_id' => 7,
        		'image' => 'grilled-mac-and-cheese-sandwich.jpg'
        	],
        	[
        		'name' => 'Lasagne',
        		'category_id' => 7,
        		'image' => 'lasagne.jpg'
        	],

        	//Pork
        	[
        		'name' => 'Bubble & Squeak',
        		'category_id' => 8,
        		'image' => 'bubble-squeak.jpg'
        	],
        	[
        		'name' => 'Hot and Sour Soup',
        		'category_id' => 8,
        		'image' => 'hot-and-sour-soup.jpg'
        	],
        	[
        		'name' => 'Pork Cassoulet',
        		'category_id' => 8,
        		'image' => 'pork-cassoulet.jpg'
        	],

        	//Seafood
        	[
        		'name' => 'Cajun spiced fish tacos',
        		'category_id' => 9,
        		'image' => 'cajun-spiced-fish-tacos.jpg'
        	],
        	[
        		'name' => 'Escovitch Fish',
        		'category_id' => 9,
        		'image' => 'escovitch-fish.jpg'
        	],
        	[
        		'name' => 'Honey Teriyaki Salmon',
        		'category_id' => 9,
        		'image' => 'honey-teriyaki-salmon.jpg'
        	],

        	//side
        	[
        		'name' => 'Fennel Dauphinoise',
        		'category_id' => 10,
        		'image' => 'fennel-dauphinoise.jpg'
        	],
        	[
        		'name' => 'French Onion Soup',
        		'category_id' => 10,
        		'image' => 'french-onion-soup.jpg'
        	],
        	[
        		'name' => 'Prawn & Fennel Bisque',
        		'category_id' => 10,
        		'image' => 'prawn-fennel-bisque.jpg'
        	],

        	//Starter
        	[
        		'name' => 'Broccoli & Stilton soup',
        		'category_id' => 11,
        		'image' => 'broccoli-stilton-soup.jpg'
        	],
        	[
        		'name' => 'Clam Chowder',
        		'category_id' => 11,
        		'image' => 'clam-chowder.jpg'
        	],
        	[
        		'name' => 'Cream Cheese Tart',
        		'category_id' => 11,
        		'image' => 'cream-cheese-tart.jpg'
        	],

        	//Vegetarian
        	[
        		'name' => 'Baingan Bharta',
        		'category_id' => 12,
        		'image' => 'baingan-bharta.jpg'
        	],
        	[
        		'name' => 'Chickpea Fajitas',
        		'category_id' => 12,
        		'image' => 'chickpea-fajitas.jpg'
        	],
        	[
        		'name' => 'Mushroom & Chestnut Rotolo',
        		'category_id' => 12,
        		'image' => 'mushroom-chestnut-rotolo.jpg'
        	],

        ];

        $admin = User::where('role_id', 1)->firstOrFail();

        foreach ($dishes as $dish) {
        	Dish::create([
        		'code' => $this->getCode('DS', 'dishes'),
        		'name' => $dish['name'],
        		'description' => $faker->paragraph,
        		'dish_category_id' => $dish['category_id'],
        		'price' => $faker->numberBetween($min = 1, $max = 10),
        		'user_id' => $admin->id,
        		'image' => $dish['image']
        	]);
        }

    }
}
