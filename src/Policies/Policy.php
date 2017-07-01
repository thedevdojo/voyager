<?php

namespace TCG\Voyager\Policies;

use Auth;
use TCG\Voyager\Facades\Voyager as Voyager;
use TCG\Voyager\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class Policy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the given user can browse the model.
     *
     * @param  \TCG\Voyager\Models\User  $user
     * @param  $model
     * @return bool
     */
    // TODO: Any way to get more specific on the $model parameter?
    public function browse(User $user, $model)
    {
        $dataType = Voyager::model('DataType')::where('model_name',get_class($model))->first();
        return Voyager::can('browse_'.$dataType->name);
    }

    /**
     * Determine if the given user can create the model.
     *
     * @param  \TCG\Voyager\Models\User  $user
     * @param  $model
     * @return bool
     */
    // TODO: Any way to get more specific on the $model parameter?
    public function create(User $user, $model)
    {
        $dataType = Voyager::model('DataType')::where('model_name',get_class($model))->first();
        return Voyager::can('add_'.$dataType->name);
    }

    /**
     * Determine if the given model can be edited by the user.
     *
     * @param  \TCG\Voyager\Models\User  $user
     * @param  $model
     * @return bool
     */
    // TODO: Any way to get more specific on the $model parameter?
    public function edit(User $user, $model)
    {
        $dataType = Voyager::model('DataType')::where('model_name',get_class($model))->first();
        return Voyager::can('edit_'.$dataType->name);
    }

    /**
     * Determine if the given user can store this model.
     *
     * @param  \TCG\Voyager\Models\User  $user
     * @param  $model
     * @return bool
     */
    // TODO: Any way to get more specific on the $model parameter?
    public function store(User $user, $model)
    {
        $dataType = Voyager::model('DataType')::where('model_name',get_class($model))->first();
        return Voyager::can('add_'.$dataType->name);
    }

    /**
     * Determine if the given model can be updated by the user.
     *
     * @param  \TCG\Voyager\Models\User  $user
     * @param  $model
     * @return bool
     */
    // TODO: Any way to get more specific on the $model parameter?
    public function update(User $user, $model)
    {
        $dataType = Voyager::model('DataType')::where('model_name',get_class($model))->first();
        return Voyager::can('edit_'.$dataType->name);
    }

    /**
     * Determine if the given model can be viewed by the user.
     *
     * @param  \TCG\Voyager\Models\User  $user
     * @param  $model
     * @return bool
     */
    // TODO: Any way to get more specific on the $model parameter?
    public function view(User $user, $model)
    {
        $dataType = Voyager::model('DataType')::where('model_name',get_class($model))->first();
        return Voyager::can('read_'.$dataType->name);
    }

    /**
     * Determine if the given model can be deleted by the user.
     *
     * @param  \TCG\Voyager\Models\User  $user
     * @param  $model
     * @return bool
     */
    // TODO: Any way to get more specific on the $model parameter?
    public function delete(User $user, $model)
    {
        $dataType = Voyager::model('DataType')::where('model_name',get_class($model))->first();
        return Voyager::can('delete_'.$dataType->name);
    }
}