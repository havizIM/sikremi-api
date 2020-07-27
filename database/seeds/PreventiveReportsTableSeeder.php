<?php

use Illuminate\Database\Seeder;

class PreventiveReportsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('preventive_reports')->insert([
            [
                'schedule_id' => 1,
                'equipment_id' => 1,
                'report_number' => 'PR-2006-001',
                'date' => '2020-07-18',
            ],
            [
                'schedule_id' => 1,
                'equipment_id' => 2,
                'report_number' => 'PR-2006-002',
                'date' => '2020-07-18',
            ],
        ]);
    }
}
