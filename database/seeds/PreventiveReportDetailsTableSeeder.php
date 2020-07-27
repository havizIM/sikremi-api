<?php

use Illuminate\Database\Seeder;

class PreventiveReportDetailsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('preventive_report_details')->insert([
            [
                'preventive_report_id' => 1,
                'description' => 'Text 1',
                'periode' => 'Weekly',
                'tools' => 'Tools 1',
                'check' => 'Y'
            ],
            [
                'preventive_report_id' => 1,
                'description' => 'Text 2',
                'periode' => 'Weekly',
                'tools' => 'Tools 1',
                'check' => 'Y'
            ],
            [
                'preventive_report_id' => 2,
                'description' => 'Text 1',
                'periode' => 'Monthly',
                'tools' => 'Tools 2',
                'check' => 'Y'
            ],
            [
                'preventive_report_id' => 2,
                'description' => 'Text 2',
                'periode' => 'Monthly',
                'tools' => 'Tools 2',
                'check' => 'Y'
            ],
        ]);
    }
}
