<?php

use Illuminate\Database\Seeder;

class VoyagerDummyDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('CategoriesTableSeeder');
        $this->call('UsersTableSeeder');
        $this->call('PostsTableSeeder');
        $this->call('PagesTableSeeder');
        $this->call('SettingsTableSeeder');
    }
}
