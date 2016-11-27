<?php

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('categories')->delete();

        \DB::table('categories')->insert([
            0 => [
                'id'         => 1,
                'parent_id'  => 0,
                'order'      => 1,
                'name'       => 'Category 1',
                'slug'       => 'category-1',
                'created_at' => '2016-10-27 00:54:55',
                'updated_at' => '2016-10-27 00:54:55',
            ],
            1 => [
                'id'         => 2,
                'parent_id'  => 0,
                'order'      => 1,
                'name'       => 'Category 2',
                'slug'       => 'category-2',
                'created_at' => '2016-10-27 00:54:55',
                'updated_at' => '2016-10-27 00:54:55',
            ],
        ]);
    }
}
