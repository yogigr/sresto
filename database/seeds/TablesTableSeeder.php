<?php

use Illuminate\Database\Seeder;
use App\Table;
use App\User;

class TablesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tables = ['Table One', 'Table Two', 'Table Three', 'Table Four', 'Table Five', 'Table Six', 'Table Seven', 'Table Eight'];
        $admin = User::where('role_id', 1)->firstOrFail();

        foreach ($tables as $table) {
        	Table::create([
        		'name' => $table,
        		'user_id' => $admin->id
        	]);
        }
    }
}
