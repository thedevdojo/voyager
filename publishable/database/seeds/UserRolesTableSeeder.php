<?php

use Illuminate\Database\Seeder;

class UserRolesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('user_roles')->delete();

        \DB::table('user_roles')->insert([
            0 => [
                'role_id' => 1,
                'user_id' => 1,
            ],
            1 => [
                'role_id' => 2,
                'user_id' => 1,
            ],
            2 => [
                'role_id' => 3,
                'user_id' => 1,
            ],
            3 => [
                'role_id' => 2,
                'user_id' => 2,
            ],
            4 => [
                'role_id' => 3,
                'user_id' => 2,
            ],
        ]);
    }
}
