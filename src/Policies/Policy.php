<?php

namespace TCG\Voyager\Policies;

use Auth;
use TCG\Voyager\Facades\Voyager as Voyager;
use TCG\Voyager\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Http\Request;

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
    public function store(User $user, $model)
    {
        dump($model);exit();
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
    public function show(User $user, $model)
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
    public function delete(User $user, $model)
    {
        $dataType = Voyager::model('DataType')::where('model_name',get_class($model))->first();
        return Voyager::can('delete_'.$dataType->name);
    }
}