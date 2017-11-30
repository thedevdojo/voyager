<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Facades\Voyager;

class Role extends Model
{
    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(Voyager::modelClass('User'))->select('*')->getQuery()
                    ->union(
                        $this->belongsToMany(Voyager::modelClass('User'), 'user_roles')
                             ->select(Voyager::model('User')->getTable().'.*')
                             ->getQuery()
                    );
    }

    public function permissions()
    {
        return $this->belongsToMany(Voyager::modelClass('Permission'));
    }
}
