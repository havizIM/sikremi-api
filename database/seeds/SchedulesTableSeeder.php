<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchedulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('schedules')->insert([
            [
                'building_id' => 1,
                'date' => '2020-07-18',
                'time' => '07:00',
                'estimate' => '2',
                'type' => 'Preventive',
                'shift' => null,
                'description' => 'Weekly Preventive',
                'submit' => 'Y',
            ],
            [
                'building_id' => 2,
                'date' => '2020-07-19',
                'time' => '07:00',
                'estimate' => '2',
                'type' => 'Preventive',
                'shift' => null,
                'description' => 'Weekly Preventive',
                'submit' => 'Y',
            ],
            [
                'building_id' => 1,
                'date' => '2020-07-18',
                'time' => '07:00',
                'estimate' => '2',
                'type' => 'Corrective',
                'shift' => null,
                'description' => null,
                'submit' => 'Y',
            ],
        ]);
    }
}
