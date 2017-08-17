<?php

namespace TCG\Voyager\Models;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as AuthUser;
use TCG\Voyager\Traits\VoyagerUser;

class User extends AuthUser
{
    use VoyagerUser;

    protected $guarded = [];

    public function getAvatarAttribute()
    {
        if (!is_null($this->attributes['avatar'])) {
            return $this->attributes['avatar'];
        }

        return config('voyager.user.default_avatar', 'users/default.png');
    }

    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }
}
