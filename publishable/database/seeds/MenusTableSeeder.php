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
        \TCG\Voyager\Models\Menu::truncate();

        \DB::table('menus')->insert([
            0 => [
                'name'       => 'admin',
                'created_at' => '2016-05-19 18:31:14',
                'updated_at' => '2016-05-19 18:31:14',
            ],
        ]);

        \DB::table('menus')->insert([
            0 => [
                'name'       => 'main',
                'created_at' => '2016-05-19 18:31:14',
                'updated_at' => '2016-05-19 18:31:14',
            ],
        ]);
    }
}
