<?php

namespace TCG\Voyager\Traits;

trait VoyagerUser
{
    //
    public function roles()
    {
        return $this->belongsToMany('TCG\Voyager\Models\Role', 'user_roles');
    }

    public function hasRole($name)
    {
        return in_array($name, array_pluck($this->roles->toArray(), 'name'));
    }

    public function addRole($name)
    {
        // If user does not already have this role
        if (!$this->hasRole($name)) {
            // Look up the role and attach it to the user
            $role = \TCG\Voyager\Models\Role::where('name', '=', $name)->first();
            $this->roles()->attach($role->id);
        }
    }

    public function deleteRole($name)
    {
        // If user has this role
        if ($this->hasRole($name)) {
            // Lookup the role and detach it from the user
            $role = \TCG\Voyager\Models\Role::where('name', '=', $name)->first();
            $this->roles()->detach($role->id);
        }
    }
}
