<?php

namespace TCG\Voyager\Policies;

use TCG\Voyager\Contracts\User;
use TCG\Voyager\Models\DataType;

class MenuItemPolicy extends BasePolicy
{
    /**
     * Check if user has an associated permission.
     *
     * @param User   $user
     * @param object $model
     * @param string $action
     *
     * @return bool
     */
    protected function checkPermission(User $user, $model, $action)
    {
        $regex = str_replace('/', '\/', preg_quote(route('voyager.dashboard')));
        $slug = preg_replace('/'.$regex.'/', '', $model->link(true));
        $slug = str_replace('/', '', $slug);

        if ($resolvedDataType = DataType::whereSlug($slug)->first()) {
            $slug = $resolvedDataType->name;
        }

        if ($slug == '') {
            $slug = 'admin';
        }

        return $user->hasPermission('browse_'.$slug);
    }
}
