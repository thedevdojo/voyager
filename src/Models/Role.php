<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Facades\Voyager;

class Role extends Model
{
    protected $guarded = [];

    public function usersDefault()
    {
        return $this->hasMany(Voyager::modelClass('User'))->select('*');
    }

    public function usersAlternative()
    {
        return $this->belongsToMany(Voyager::modelClass('User'), 'user_roles')->select('users.*');
    }

    public function users()
    {
        return $this->usersDefault()->union($this->usersAlternative()->toBase());
    }

    public function permissions()
    {
        return $this->belongsToMany(Voyager::modelClass('Permission'));
    }
}
