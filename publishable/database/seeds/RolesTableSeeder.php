<?php

use TCG\Voyager\Models\Role;
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
            $role = Role::firstOrNew([
                'name'         => 'admin',
            ]);
            if (!$role->exists) {
                $role->fill([
                    'display_name' => 'Administrator',
                ])->save();
            }

            $role = Role::firstOrNew([
                'name'         => 'user',
            ]);
            if (!$role->exists) {
                $role->fill([
                    'display_name' => 'Normal User',
                ])->save();
            }
    }
}
