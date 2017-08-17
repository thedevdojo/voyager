<?php

namespace TCG\Voyager\Policies;

use TCG\Voyager\Facades\Voyager;

class SettingPolicy extends BasePolicy
{
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
        return Voyager::can('browse_settings');
    }

}