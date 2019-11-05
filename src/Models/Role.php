<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Traits\Prefixable;

class Role extends Model
{
    use Prefixable;

    protected $guarded = [];

    public function users()
    {
        $userModel = Voyager::modelClass('User');

        return $this->belongsToMany($userModel, Str::singular(Voyager::model('User')->getTable()).'_'.$this->getTable())
                    ->select(app($userModel)->getTable().'.*')
                    ->union($this->hasMany($userModel))->getQuery();
    }

    public function permissions()
    {
        return $this->belongsToMany(Voyager::modelClass('Permission'), Str::singular(Voyager::model('Permission')->getTable()).'_'.Str::singular($this->getTable()));
    }
}
