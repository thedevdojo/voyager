<?php

namespace TCG\Voyager\Policies;

use Auth;
use TCG\Voyager\Facades\Voyager as Voyager;
use TCG\Voyager\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Http\Request;

class BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the given user can browse the model.
     *
     * @param  \TCG\Voyager\Models\User  $user
     * @param  $model
     * @return bool
     */
    public function browse(User $user, $model)
    {
        $dataType = Voyager::model('DataType');
        $dataType = $dataType::where('model_name', get_class($model))->first();
        return Voyager::can('browse_'.$dataType->name);
    }

    /**
     * Determine if the given model can be viewed by the user.
     *
     * @param  \TCG\Voyager\Models\User  $user
     * @param  $model
     * @return bool
     */
    public function read(User $user, $model)
    {
        $dataType = Voyager::model('DataType');
        $dataType = $dataType::where('model_name', get_class($model))->first();
        return Voyager::can('read_'.$dataType->name);
    }

    /**
     * Determine if the given model can be edited by the user.
     *
     * @param  \TCG\Voyager\Models\User  $user
     * @param  $model
     * @return bool
     */
    public function edit(User $user, $model)
    {
        $dataType = Voyager::model('DataType');
        $dataType = $dataType::where('model_name', get_class($model))->first();
        return Voyager::can('edit_'.$dataType->name);
    }

    /**
     * Determine if the given user can create the model.
     *
     * @param  \TCG\Voyager\Models\User  $user
     * @param  $model
     * @return bool
     */
    public function add(User $user, $model)
    {
        $dataType = Voyager::model('DataType');
        $dataType = $dataType::where('model_name', get_class($model))->first();
        return Voyager::can('add_'.$dataType->name);
    }

    /**
     * Determine if the given model can be deleted by the user.
     *
     * @param  \TCG\Voyager\Models\User  $user
     * @param  $model
     * @return bool
     */
    public function delete(User $user, $model)
    {
        $dataType = Voyager::model('DataType');
        $dataType = $dataType::where('model_name', get_class($model))->first();
        return Voyager::can('delete_'.$dataType->name);
    }
}