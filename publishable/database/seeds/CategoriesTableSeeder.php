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
        Category::firstOrCreate([
            'name' => 'Category 1',
            'slug' => 'category-1',
        ]);

        Category::firstOrCreate([
            'name' => 'Category 2',
            'slug' => 'category-2',
        ]);
    }
}
