<?php

use Illuminate\Database\Seeder;
use App\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
        	[
        		'id' => 1,
        		'name' => 'Administrator',
        	],
        	[
        		'id' => 2,
        		'name' => 'Manager',
        	],
        	[
        		'id' => 3,
        		'name' => 'Chef',
        	],
        	[
        		'id' => 4,
        		'name' => 'Waiter',
        	]
        ];

        foreach ($roles as $role) {
        	Role::create([
        		'id' => $role['id'],
        		'name' => $role['name']
        	]);
        }
    }
}
