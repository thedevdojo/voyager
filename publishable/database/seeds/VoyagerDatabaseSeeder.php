<?php

use Illuminate\Database\Seeder;

class VoyagerDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('CategoriesTableSeeder');
        $this->call('DataTypesTableSeeder');
        $this->call('DataRowsTableSeeder');
        $this->call('UsersTableSeeder');
        $this->call('PostsTableSeeder');
        $this->call('PagesTableSeeder');
        $this->call('MenusTableSeeder');
        $this->call('MenuItemsTableSeeder');
        $this->call('SettingsTableSeeder');
        $this->call('RolesTableSeeder');
        $this->call('UserRolesTableSeeder');
    }
}
