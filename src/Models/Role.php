<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Traits\HasRelationships;

class Role extends Model
{
    use HasRelationships;

    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(Voyager::modelClass('User'), 'user_roles');
    }

    public function permissions()
    {
        return $this->belongsToMany(Voyager::modelClass('Permission'));
    }
}
