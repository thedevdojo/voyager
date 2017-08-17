<?php

namespace TCG\Voyager\Policies;

use TCG\Voyager\Facades\Voyager;

class PostPolicy extends BasePolicy
{
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

        // Does this post belong to the current user?
        $current = $user->id === $model->author_id ? true : false;

        $permission = Voyager::can('read_'.$dataType->name);

        return $current || $permission;
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

        // Does this post belong to the current user?
        $current = $user->id === $model->author_id ? true : false;

        $permission = Voyager::can('edit_'.$dataType->name);

        return $current || $permission;
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

        // Does this post belong to the current user?
        $current = $user->id === $model->author_id ? true : false;

        $permission = Voyager::can('delete_'.$dataType->name);

        return $current || $permission;
    }

}