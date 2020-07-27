<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PreventiveSchedulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('preventive_schedules')->insert([
            [
                'schedule_id' => 1,
                'equipment_id' => 1,
            ],
            [
                'schedule_id' => 1,
                'equipment_id' => 2,
            ],
            [
                'schedule_id' => 2,
                'equipment_id' => 3,
            ],
            [
                'schedule_id' => 2,
                'equipment_id' => 4,
            ],
        ]);
    }
}
