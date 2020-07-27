<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'username' => 'helpdesk',
                'email' => 'helpdesk@gmail.com',
                'password' => bcrypt('h3lpd35k'),
                'roles' => 'ADMINISTRATOR',
                'active' => 'Y',
            ],
            // [
            //     'username' => 'devanDP',
            //     'email' => 'devan@gmail.com',
            //     'password' => bcrypt('123456'),
            //     'roles' => 'PARTNER',
            //     'active' => 'Y',
            // ],
            // [
            //     'username' => 'kalyssaIP',
            //     'email' => 'kalyssa@gmail.com',
            //     'password' => bcrypt('123456'),
            //     'roles' => 'ENGINEER',
            //     'active' => 'Y',
            // ],
            // [
            //     'username' => 'dianRS',
            //     'email' => 'dian@gmail.com',
            //     'password' => bcrypt('123456'),
            //     'roles' => 'ENGINEER',
            //     'active' => 'Y',
            // ],
            // [
            //     'username' => 'ferryS',
            //     'email' => 'ferry@gmail.com',
            //     'password' => bcrypt('123456'),
            //     'roles' => 'PARTNER',
            //     'active' => 'Y',
            // ],
        ]);
    }
}
