<?php

use Illuminate\Database\Seeder;

class CorrectiveReportsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('corrective_reports')->insert([
            [
                'schedule_id' => 3,
                'equipment_id' => 1,
                'report_number' => 'CR-2007-001',
                'date' => '2020-07-18',
                'description' => 'Lorem ipsum dolor sit amet',
            ],
        ]);
    }
}
