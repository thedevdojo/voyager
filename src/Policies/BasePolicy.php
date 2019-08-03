<?php

namespace TCG\Voyager\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class BasePolicy
{
    use HandlesAuthorization;

    public function __call($name, $arguments)
    {
        if (count($arguments) < 2) {
            throw new \InvalidArgumentException('not enough arguments');
        }
        // TODO: Add logic
        return true;
    }
}
