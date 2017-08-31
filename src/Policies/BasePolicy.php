<?php

namespace TCG\Voyager\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use TCG\Voyager\Contracts\User;
use TCG\Voyager\Facades\Voyager;

class BasePolicy
{
    use HandlesAuthorization;

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
        $dataType = Voyager::model('DataType');
        $dataType = $dataType->where('model_name', get_class($model))->first();

        return $user->hasPermission('browse_'.$dataType->name);
    }

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

        return $user->hasPermission('read_'.$dataType->name);
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

        return $user->hasPermission('edit_'.$dataType->name);
    }

    /**
     * Determine if the given user can create the model.
     *
     * @param \TCG\Voyager\Contracts\User $user
     * @param  $model
     *
     * @return bool
     */
    public function add(User $user, $model)
    {
        $dataType = Voyager::model('DataType');
        $dataType = $dataType->where('model_name', get_class($model))->first();

        return $user->hasPermission('add_'.$dataType->name);
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

        return $user->hasPermission('delete_'.$dataType->name);
    }
}
