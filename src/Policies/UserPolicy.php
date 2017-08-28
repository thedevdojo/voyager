<?php

namespace TCG\Voyager\Policies;

use TCG\Voyager\Contracts\User;
use TCG\Voyager\Facades\Voyager;

class UserPolicy extends BasePolicy
{
    /**
     * Determine if the given model can be viewed by the user.
     *
     * @param \TCG\Voyager\Contracts\User $user
     * @param  $model
     *
     * @return bool
     */
    public function read(User $user, $model)
    {
        $dataType = Voyager::model('DataType');
        $dataType = $dataType->where('model_name', get_class($model))->first();

        // Is this the current user's profile?
        $current = $user->id === $model->id ? true : false;

        $permission = Voyager::can('read_'.$dataType->name);

        return $current || $permission;
    }

    /**
     * Determine if the given model can be edited by the user.
     *
     * @param \TCG\Voyager\Contracts\User $user
     * @param  $model
     *
     * @return bool
     */
    public function edit(User $user, $model)
    {
        $dataType = Voyager::model('DataType');
        $dataType = $dataType->where('model_name', get_class($model))->first();

        // Is this the current user's profile?
        $current = $user->id === $model->id ? true : false;

        $permission = Voyager::can('edit_'.$dataType->name);

        return $current || $permission;
    }
}
