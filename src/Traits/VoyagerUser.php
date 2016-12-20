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

    /**
     * Check if User has a Role(s) associated.
     *
     * @param string|array $name    The role to check.
     *
     * @return boolean
     */
    public function hasRole($name)
    {
        return in_array($this->role->name, (is_array($name) ? $name : [$name]));
    }

    public function setRole($name)
    {
        $role = Role::where('name', '=', $name)->first();

        if ($role) {
            $this->role()->associate($role);
            $this->save();
        }

        return $this;
    }

    public function hasPermission($name)
    {
        return in_array($name, $this->role->permissions->pluck('key')->toArray());
    }
}
