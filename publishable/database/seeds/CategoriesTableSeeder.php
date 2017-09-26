<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        $category = Category::firstOrNew([
            'name' => '分类1',
        ]);
        if (!$category->exists) {
            $category->fill([
                'name'       => '分类1',
            ])->save();
        }

        $category = Category::firstOrNew([
            'name' => '分类2',
        ]);
        if (!$category->exists) {
            $category->fill([
                'name'       => '分类2',
            ])->save();
        }
    }
}
