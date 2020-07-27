<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('teams')->insert([
            [
                'schedule_id' => 1,
                'engineer_id' => 1,
            ],
            [
                'schedule_id' => 1,
                'engineer_id' => 2,
            ],
            [
                'schedule_id' => 2,
                'engineer_id' => 1,
            ],
            [
                'schedule_id' => 2,
                'engineer_id' => 2,
            ],
            [
                'schedule_id' => 3,
                'engineer_id' => 1,
            ],
            [
                'schedule_id' => 3,
                'engineer_id' => 2,
            ],
        ]);
    }
}
