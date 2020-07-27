<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('buildings')->insert([
            [
                'partner_id' => 1,
                'province_id' => 1,
                'city_id' => 1,
                'building_code' => 'B-MMA-001',
                'building_name' => 'Building A',
                'type' => 'Gedung',
                'address' => 'Jl. Sudirman Sahid',
                'phone' => '0217482412',
                'fax' => '0217482412',
                'email' => 'mma_a@gmail.com',
            ],
            [
                'partner_id' => 1,
                'province_id' => 1,
                'city_id' => 1,
                'building_code' => 'B-MMA-002',
                'building_name' => 'Building B',
                'type' => 'Store',
                'address' => 'Jl. Thamrin',
                'phone' => '0217482412',
                'fax' => '0217482412',
                'email' => 'mma_b@gmail.com',
            ],
            [
                'partner_id' => 2,
                'province_id' => 1,
                'city_id' => 1,
                'building_code' => 'B-SSG-001',
                'building_name' => 'Building A',
                'type' => 'Gedung',
                'address' => 'Jl. Sudirman Sahid',
                'phone' => '0217482412',
                'fax' => '0217482412',
                'email' => 'ssg_a@gmail.com',
            ],
            [
                'partner_id' => 2,
                'province_id' => 1,
                'city_id' => 1,
                'building_code' => 'B-SSG-002',
                'building_name' => 'Building B',
                'type' => 'Store',
                'address' => 'Jl. Thamrin',
                'phone' => '0217482412',
                'fax' => '0217482412',
                'email' => 'ssg_b@gmail.com',
            ],
        ]);
    }
}
