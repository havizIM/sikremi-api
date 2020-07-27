<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PreventiveProceduresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('preventive_procedures')->insert([
            [
                'procedure_id' => 1,
                'description' => 'Text 1',
                'periode' => 'Weekly',
                'tools' => 'Tools 1',
            ],
            [
                'procedure_id' => 1,
                'description' => 'Text 2',
                'periode' => 'Monthly',
                'tools' => 'Tools 2',
            ],
            [
                'procedure_id' => 1,
                'description' => 'Text 3',
                'periode' => 'Yearly',
                'tools' => 'Tools 3',
            ],
            [
                'procedure_id' => 2,
                'description' => 'Text 1',
                'periode' => 'Weekly',
                'tools' => 'Tools 1',
            ],
            [
                'procedure_id' => 2,
                'description' => 'Text 2',
                'periode' => 'Monthly',
                'tools' => 'Tools 2',
            ],
            [
                'procedure_id' => 2,
                'description' => 'Text 3',
                'periode' => 'Yearly',
                'tools' => 'Tools 3',
            ],
            [
                'procedure_id' => 3,
                'description' => 'Text 1',
                'periode' => 'Weekly',
                'tools' => 'Tools 1',
            ],
            [
                'procedure_id' => 3,
                'description' => 'Text 2',
                'periode' => 'Monthly',
                'tools' => 'Tools 2',
            ],
            [
                'procedure_id' => 3,
                'description' => 'Text 3',
                'periode' => 'Yearly',
                'tools' => 'Tools 3',
            ],
            [
                'procedure_id' => 4,
                'description' => 'Text 1',
                'periode' => 'Weekly',
                'tools' => 'Tools 1',
            ],
            [
                'procedure_id' => 4,
                'description' => 'Text 2',
                'periode' => 'Monthly',
                'tools' => 'Tools 2',
            ],
            [
                'procedure_id' => 4,
                'description' => 'Text 3',
                'periode' => 'Yearly',
                'tools' => 'Tools 3',
            ],
        ]);
    }
}
