<?php

namespace TCG\Voyager\Traits;

use TCG\Voyager\Models\Role;

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
            $this->role()->associate($role);
        }
    }

    public function hasPermission($name)
    {
        return in_array($name, $this->role->permissions->pluck('key')->toArray());
    }
}
