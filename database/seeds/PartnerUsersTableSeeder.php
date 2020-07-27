<?php

use Illuminate\Database\Seeder;

class PartnerUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('partner_users')->insert([
            [
                'partner_id' => 1,
                'user_id' => 2,
                'full_name' => 'Devan Dirgantara Putra',
                'address' => 'Jl. Gg Vanilli No. 19f Rt. 010 Rww. 005',
                'phone' => '081355754092',
                'position' => 'Manager',
            ],
            [
                'partner_id' => 2,
                'user_id' => 5,
                'full_name' => 'Ferry Setiawan',
                'address' => 'Jl. Gg Vanilli No. 19f Rt. 010 Rww. 005',
                'phone' => '081355754092',
                'position' => 'Admin',
            ],
        ]);
    }
}
