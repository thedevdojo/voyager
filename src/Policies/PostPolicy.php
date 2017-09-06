<?php

namespace TCG\Voyager\Policies;

use TCG\Voyager\Contracts\User;
use TCG\Voyager\Facades\Voyager;

class PostPolicy extends BasePolicy
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

        // Does this post belong to the current user?
        $current = $user->id === $model->author_id ? true : false;

        $permission = $user->hasPermission('read_'.$dataType->name);

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

        // Does this post belong to the current user?
        $current = $user->id === $model->author_id ? true : false;

        $permission = $user->hasPermission('edit_'.$dataType->name);

        return $current || $permission;
    }

    /**
     * Determine if the given model can be deleted by the user.
     *
     * @param \TCG\Voyager\Contracts\User $user
     * @param  $model
     *
     * @return bool
     */
    public function delete(User $user, $model)
    {
        $dataType = Voyager::model('DataType');
        $dataType = $dataType->where('model_name', get_class($model))->first();

        // Does this post belong to the current user?
        $current = $user->id === $model->author_id ? true : false;

        $permission = $user->hasPermission('delete_'.$dataType->name);

        return $current || $permission;
    }
}
