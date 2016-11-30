<?php

namespace TCG\Voyager\Traits;

use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Models\Role;
use TCG\Voyager\Models\User;

/**
 * @property  \Illuminate\Database\Eloquent\Collection  roles
 */
trait VoyagerUser
{
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole($name)
    {
        return $this->role->name == $name;
    }

    public function setRole($name)
    {
        $role = Role::where('name', '=', $name)->first();
        if ($role) {
            $this->role()->save($role);
        }
    }

    public function hasPermission($name)
    {
        return in_array($name, $this->role->permissions->pluck('key')->toArray());
    }
}
