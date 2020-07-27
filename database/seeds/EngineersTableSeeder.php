<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EngineersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('engineers')->insert([
            [
                'user_id' => 3,
                'province_id' => 1,
                'city_id' => 1,
                'full_name' => 'Kalyssa Innara Putri',
                'address' => 'Jl. Gg Vanilli No. 19f Rt. 010 Rww. 005',
                'phone' => '081355754092',
            ],
            [
                'user_id' => 4,
                'province_id' => 1,
                'city_id' => 1,
                'full_name' => 'Dian Ratna Sari',
                'address' => 'Jl. Gg Vanilli No. 19f Rt. 010 Rww. 005',
                'phone' => '081355754092',
            ]
        ]);
    }
}
