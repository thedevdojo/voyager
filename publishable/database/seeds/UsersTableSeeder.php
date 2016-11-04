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
                'password' => '$2y$10$zKG9LO0VcGgM4LmysRupkOaplMSDG5mvbX0kfj1MKeE0Cx/qo9zXq',
                'remember_token' => 'L0fY96Ek3r30dUE9e482JwmjxV4iKYxZ4YGBcibz7Qu0eSA31dPyR0ilR5Cc',
                'created_at' => '2016-01-28 11:20:57',
                'updated_at' => '2016-10-25 14:32:23',
                'avatar' => 'users/default.png',
            ),
        ));
        
        
    }
}
