<?php

namespace TCG\Voyager\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use TCG\Voyager\Facades\Voyager;

class BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the given user can browse the model.
     *
     * @param $user
     * @param $model
     *
     * @return bool
     */
    public function browse($user, $model)
    {
        $dataType = Voyager::model('DataType');
        $dataType = $dataType->where('model_name', get_class($model))->first();

        return Voyager::can('browse_'.$dataType->name);
    }

    /**
     * Determine if the given model can be viewed by the user.
     *
     * @param $user
     * @param $model
     *
     * @return bool
     */
    public function read($user, $model)
    {
        $dataType = Voyager::model('DataType');
        $dataType = $dataType->where('model_name', get_class($model))->first();

        return Voyager::can('read_'.$dataType->name);
    }

    /**
     * Determine if the given model can be edited by the user.
     *
     * @param $user
     * @param $model
     *
     * @return bool
     */
    public function edit($user, $model)
    {
        $dataType = Voyager::model('DataType');
        $dataType = $dataType->where('model_name', get_class($model))->first();

        return Voyager::can('edit_'.$dataType->name);
    }

    /**
     * Determine if the given user can create the model.
     *
     * @param $user
     * @param $model
     *
     * @return bool
     */
    public function add($user, $model)
    {
        $dataType = Voyager::model('DataType');
        $dataType = $dataType->where('model_name', get_class($model))->first();

        return Voyager::can('add_'.$dataType->name);
    }

    /**
     * Determine if the given model can be deleted by the user.
     *
     * @param $user
     * @param $model
     *
     * @return bool
     */
    public function delete($user, $model)
    {
        $dataType = Voyager::model('DataType');
        $dataType = $dataType->where('model_name', get_class($model))->first();

        return Voyager::can('delete_'.$dataType->name);
    }

}