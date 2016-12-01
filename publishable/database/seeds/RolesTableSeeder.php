<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('roles')->delete();

        \DB::table('roles')->insert([
            0 => [
                'id'           => 1,
                'name'         => 'root',
                'display_name' => 'Root',
                'description'  => 'Full access, with privileges to break the system.',
                'created_at'   => '2016-10-21 22:31:20',
                'updated_at'   => '2016-10-21 22:31:20',
            ],
            1 => [
                'id'           => 2,
                'name'         => 'admin',
                'display_name' => 'Administrator',
                'description'  => 'Full access, except for managing the Menu, Database and Settings.',
                'created_at'   => '2016-10-21 22:31:20',
                'updated_at'   => '2016-10-21 22:31:20',
            ],
            2 => [
                'id'           => 3,
                'name'         => 'user',
                'display_name' => 'Normal User',
                'description'  => 'Standard User with no admin privileges.',
                'created_at'   => '2016-10-21 22:31:38',
                'updated_at'   => '2016-10-21 22:31:38',
            ],
        ]);
    }
}
