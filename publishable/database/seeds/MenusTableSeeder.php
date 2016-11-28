<?php

use Illuminate\Database\Seeder;

class MenusTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('menus')->delete();

        \DB::table('menus')->insert([
            0 => [
                'id'         => 1,
                'name'       => 'main',
                'created_at' => '2016-05-19 18:31:14',
                'updated_at' => '2016-05-19 18:31:14',
            ],
            1 => [
                'id'         => 2,
                'name'       => 'admin',
                'created_at' => '2016-05-19 19:55:51',
                'updated_at' => '2016-05-19 19:55:51',
            ],
        ]);
    }
}
