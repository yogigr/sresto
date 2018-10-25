<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
        	[
        		'name' => 'Admin User',
        		'email' => 'admin@sresto.test',
        		'password' => 'secret',
        		'role_id' => 1
        	],
        	[
        		'name' => 'Manager User',
        		'email' => 'manager@sresto.test',
        		'password' => 'secret',
        		'role_id' => 2
        	],
        	[
        		'name' => 'Chef User',
        		'email' => 'chef@sresto.test',
        		'password' => 'secret',
        		'role_id' => 3
        	],
        	[
        		'name' => 'Waiter User',
        		'email' => 'waiter@sresto.test',
        		'password' => 'secret',
        		'role_id' => 4
        	]
        ];

        foreach ($users as $user) {
        	User::create([
        		'name' => $user['name'],
        		'email' => $user['email'],
        		'password' => bcrypt($user['password']),
        		'role_id' => $user['role_id']
        	]);
        }
    }
}
