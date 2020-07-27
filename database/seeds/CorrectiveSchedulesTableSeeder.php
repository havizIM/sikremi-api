<?php

use Illuminate\Database\Seeder;

class CorrectiveSchedulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('corrective_schedules')->insert([
            [
                'schedule_id' => 3,
                'work_order_id' => 1,
            ],
        ]);
    }
}
