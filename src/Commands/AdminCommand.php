<?php

namespace TCG\Voyager\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use TCG\Voyager\Models\Permission;
use TCG\Voyager\Models\Role;
use TCG\Voyager\Models\User;

class AdminCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'voyager:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make sure a user have the admin role, and that the role has all permissions.';

    /**
     * Get user options.
     */
    protected function getOptions()
    {
        return [
            ['create', null, InputOption::VALUE_NONE, 'Create an admin user', null],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $email = $this->argument('email');

        // If we need to create a new user go ahead and create it
        if ($this->option('create')) {
            $name = $this->ask('Enter the admin name');
            $password = $this->secret('Enter admin password');
            $this->info('Creating admin account');

            User::create([
              'name'             => $name,
              'email'            => $email,
              'password'         => \Hash::make($password),
              'avatar'           => 'users/default.png',
            ]);
        }

        $role = Role::firstOrNew([
            'name' => 'admin',
        ]);
        if (!$role->exists) {
            $role->fill([
                'display_name' => 'Administrator',
            ])->save();
        }

        $permissions = Permission::all();

        $role->permissions()->sync(
            $permissions->pluck('id')->all()
        );

        $user = User::where('email', $email)->firstOrFail();
        $user->role_id = $role->id;
        $user->save();

        $this->info('The user now has full access to your site.');
    }

    /**
     * Get command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['email', InputOption::VALUE_REQUIRED, 'The email of the user.', null],
        ];
    }
}
