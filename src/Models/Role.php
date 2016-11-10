<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    //
    public function users()
    {
        return $this->belongsToMany('TCG\Voyager\Models\User', 'user_roles');
    }
}
