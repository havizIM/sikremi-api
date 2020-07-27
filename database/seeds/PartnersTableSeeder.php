<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PartnersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('partners')->insert([
            [
                'province_id' => 1,
                'city_id' => 1,
                'partner_name' => 'PT. Maju Mundur Abadi',
                'address' => 'Jl. Gg Vanilli No. 19f Rt. 010 Rww. 005',
                'phone' => '021123123',
                'fax' => '021123123',
                'handphone' => '081355754092',
                'website' => 'https://majumundurabadi.com',
                'npwp' => '13123218563153',
            ],
            [
                'province_id' => 3,
                'city_id' => 5,
                'partner_name' => 'PT. Sejahtera Selalu Gumawo',
                'address' => 'Jl. Gg Vanilli No. 19f Rt. 010 Rww. 005',
                'phone' => '021123123',
                'fax' => '021123123',
                'handphone' => '081355754092',
                'website' => 'https://gumawo.com',
                'npwp' => '13123218563153',
            ],
        ]);
    }
}
