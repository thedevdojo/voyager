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
        return $this->belongsTo(Voyager::modelClass('User'), 'id', 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Voyager::modelClass('Permission'));
    }
}
