<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkOrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('work_orders')->insert([
            [
                'building_id' => 1,
                'equipment_id' => null,
                'wo_number' => 'WO-001-001',
                'date' => '2020-07-12',
                'description' => 'Test 1',
            ],
            [
                'building_id' => 1,
                'equipment_id' => null,
                'wo_number' => 'WO-001-002',
                'date' => '2020-07-12',
                'description' => 'Test 2',
            ],
        ]);
    }
}
