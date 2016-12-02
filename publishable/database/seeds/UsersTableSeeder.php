<?php

use Illuminate\Database\Seeder;

use TCG\Voyager\Models\User;
use TCG\Voyager\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        if (User::count() == 0) {
            $user = User::create([
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => bcrypt('password'),
            ]);
        } else {
            $user = User::first();
        }

        $roles = Role::whereIn('name', ['admin', 'user'])->get();

        $user->roles()->attach(
            $roles->pluck('id')->all()
        );
    }
}
