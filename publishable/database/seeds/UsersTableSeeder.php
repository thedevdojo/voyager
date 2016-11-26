<?php

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
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => bcrypt('password'),
                'remember_token' => str_random(60),
                'created_at' => '2016-01-28 11:20:57',
                'updated_at' => '2016-10-25 14:32:23',
                'avatar' => 'users/default.png',
            ),
        ));
        
        
    }
}
