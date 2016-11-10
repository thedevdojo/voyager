<?php

namespace TCG\Voyager\Models;

use Illuminate\Foundation\Auth\User as AuthUser;
use TCG\Voyager\Traits\VoyagerUser;

class User extends AuthUser
{
    use VoyagerUser;

    //
    public function getNameAttribute($value)
    {
        return ucwords($value);
    }

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('F jS, Y h:i A');
    }

    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] = \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
    }
}
