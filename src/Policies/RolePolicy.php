<?php

namespace TCG\Voyager\Policies;

use TCG\Voyager\Contracts\User;

class RolePolicy extends BasePolicy
{
    public function read(User $user, $model)
    {
        $role_greater = $user->role->order == 1 || $model->order > $user->role->order;
        return $role_greater && $this->checkPermission($user, $model, 'read');
    }

    public function edit(User $user, $model)
    {
        $role_greater = $user->role->order == 1 || $model->order > $user->role->order;
        return $role_greater && $this->checkPermission($user, $model, 'edit');
    }
}
