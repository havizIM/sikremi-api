<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProceduresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('procedures')->insert([
            [
                'partner_id' => 1,
                'identifier_name' => 'AC Indoor',
                'type' => 'AC',
            ],
            [
                'partner_id' => 1,
                'identifier_name' => 'AC Outdoor',
                'type' => 'AC',
            ],
            [
                'partner_id' => 2,
                'category_name' => 'AC Indoor',
                'type' => 'AC',
            ],
            [
                'partner_id' => 2,
                'category_name' => 'AC Outdoor',
                'type' => 'AC',
            ],
        ]);
    }
}
