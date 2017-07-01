<?php

namespace TCG\Voyager\Policies;

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
     * @return bool
     */
    public function browse(User $user)
    {
        return true;
    }

    /**
     * Determine if the given user can create the model.
     *
     * @param  \TCG\Voyager\Models\User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return true;
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
        return true;
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
        return true;
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
        return true;
    }
}