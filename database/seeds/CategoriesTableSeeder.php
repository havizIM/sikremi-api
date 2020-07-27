<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
            [
                'partner_id' => 1,
                'category_name' => 'Electrical',
            ],
            [
                'partner_id' => 1,
                'category_name' => 'Plumbing',
            ],
            [
                'partner_id' => 1,
                'category_name' => 'AC',
            ],
            [
                'partner_id' => 1,
                'category_name' => 'Genset',
            ],
            [
                'partner_id' => 2,
                'category_name' => 'AC',
            ],
            [
                'partner_id' => 2,
                'category_name' => 'Genset',
            ]
        ]);
    }
}
