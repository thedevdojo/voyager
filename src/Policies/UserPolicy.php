<?php

namespace TCG\Voyager\Policies;

use TCG\Voyager\Contracts\User;

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
        // Does this post belong to the current user?
        //$current = $user->id === $model->id;//This is bad, and could give away access to a wrong person 
        $current = $user->id === $model->user_id; //Is this what  you meant?
        
        return $current || $this->checkPermission($user, $model, 'read');
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
        // Does this post belong to the current user?
        //$current = $user->id === $model->id;
        $current = $user->id === $model->user_id; //Is this what  you meant above?

        return $current || $this->checkPermission($user, $model, 'edit');
    }
}
