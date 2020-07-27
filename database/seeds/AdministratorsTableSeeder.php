<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdministratorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('administrators')->insert([
            [
                'user_id' => 1,
                'province_id' => 6,
                'city_id' => 151,
                'full_name' => 'Helpdesk',
                'address' => 'PT. Rezeki Surya Intimakmur',
                'phone' => '089877488441',
                'level' => 'Admin',
            ]
        ]);
    }
}
