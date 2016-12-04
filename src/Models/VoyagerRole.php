<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;

class VoyagerRole extends Model
{
    protected $table = 'roles';

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
}
