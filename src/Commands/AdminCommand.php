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
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $email = $this->argument('email');

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

        $this->info('The user have now full access to your site.');
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
