<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;
use App\User as LaravelUser;
use TCG\Voyager\Traits\VoyagerUser;

class User extends LaravelUser
{
    use VoyagerUser;
    //
    public function getNameAttribute($value)
    {
        return ucwords($value);
    }

    public function setPasswordAttribute($value){
    	$this->attributes['password'] = \Hash::make($value);
    }

    public function getCreatedAtAttribute($value){
    	return \Carbon\Carbon::parse($value)->format('F jS, Y h:i A');
    }
}
