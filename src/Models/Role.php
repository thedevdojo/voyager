<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
	protected $table = 'voyager_roles';

    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
}
