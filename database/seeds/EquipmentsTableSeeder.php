<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('equipments')->insert([
            [
                'building_id' => 1,
                'category_id' => 3,
                'procedure_id' => 1,
                'sku' => 'EQ-MMA-001',
                'equipment_name' => 'AC Indoor',
                'brand' => 'Suzuki',
                'type' => 'Indoor',
                'location' => 'Ruang Tamu',
            ],
            [
                'building_id' => 1,
                'category_id' => 3,
                'procedure_id' => 2,
                'sku' => 'EQ-MMA-002',
                'equipment_name' => 'AC Outdoor',
                'brand' => 'Suzuki',
                'type' => 'Indoor',
                'location' => 'Ruang Tamu',
            ],
            [
                'building_id' => 2,
                'category_id' => 3,
                'procedure_id' => 1,
                'sku' => 'EQ-MMA-003',
                'equipment_name' => 'AC Indoor',
                'brand' => 'Suzuki',
                'type' => 'Indoor',
                'location' => 'Ruang Tamu',
            ],
            [
                'building_id' => 2,
                'category_id' => 3,
                'procedure_id' => 2,
                'sku' => 'EQ-MMA-004',
                'equipment_name' => 'AC Outdoor',
                'brand' => 'Suzuki',
                'type' => 'Indoor',
                'location' => 'Ruang Tamu',
            ],
        ]);
    }
}
