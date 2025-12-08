<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Sumsul Karim',
                'email' => 'super@admin.com',
                'email_verified_at' => NULL,
                'password' => '$2y$12$hR8aATDtAu0pjnnmok092OQ.6i6qKRtrwl/SfDTD1H6YMEoZxR8x2',
                'remember_token' => NULL,
                'created_at' => '2025-12-01 17:17:49',
                'updated_at' => '2025-12-08 16:43:38',
                'role_id' => 1,
                'phone' => '01575434362',
                'image' => '/user/1189745770.JPG',
            ),
        ));
        
        
    }
}