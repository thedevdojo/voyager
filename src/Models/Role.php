<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Facades\Voyager;

class Role extends Model
{
    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(Voyager::modelClass('User'))->get()
                    ->merge($this->belongsToMany(Voyager::modelClass('User'), 'user_roles')->get());
    }

    public function permissions()
    {
        return $this->belongsToMany(Voyager::modelClass('Permission'));
    }
}
