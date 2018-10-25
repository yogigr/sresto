<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
        	RolesTableSeeder::class,
        	UsersTableSeeder::class,
            TablesTableSeeder::class,
            DishCategoriesTableSeeder::class,
            DishesTableSeeder::class,
            CustomersTableSeeder::class,
            PaymentMethodsTableSeeder::class,
            OrderStatusesTableSeeder::class,
        ]);
    }
}
