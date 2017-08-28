<?php

namespace TCG\Voyager\Policies;

use TCG\Voyager\Contracts\User;

class SettingPolicy extends BasePolicy
{
    /**
     * Determine if the given user can browse the model.
     *
     * @param \TCG\Voyager\Contracts\User $user
     * @param  $model
     *
     * @return bool
     */
    public function browse(User $user, $model)
    {
        return $user->hasPermission('browse_settings');
    }

    public function read(User $user, $model)
    {
        return $user->hasPermission('read_settings');
    }

    public function edit(User $user, $model)
    {
        return $user->hasPermission('edit_settings');
    }

    public function add(User $user, $model)
    {
        return $user->hasPermission('add_settings');
    }

    public function delete(User $user, $model)
    {
        return $user->hasPermission('delete_settings');
    }
}
