<?php

namespace TCG\Voyager\Traits;

use Illuminate\Support\Arr;
use TCG\Voyager\Models\Role;

/**
 * @property  \Illuminate\Database\Eloquent\Collection  roles
 */
trait VoyagerUser
{
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function hasRole($name)
    {
        return in_array($name, Arr::pluck($this->roles->toArray(), 'name'));
    }

    public function addRole($name)
    {
        // If user does not already have this role
        if (!$this->hasRole($name)) {
            // Look up the role and attach it to the user
            $role = Role::where('name', '=', $name)->first();
            $this->roles()->attach($role->id);
        }
    }

    public function deleteRole($name)
    {
        // If user has this role
        if ($this->hasRole($name)) {
            // Lookup the role and detach it from the user
            $role = Role::where('name', '=', $name)->first();
            $this->roles()->detach($role->id);
        }
    }
}
