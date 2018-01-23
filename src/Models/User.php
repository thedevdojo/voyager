<?php

namespace TCG\Voyager\Models;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use TCG\Voyager\Contracts\User as UserContract;
use TCG\Voyager\Traits\HasRelationships;
use TCG\Voyager\Traits\VoyagerUser;

class User extends Authenticatable implements UserContract
{
    use VoyagerUser,
        HasRelationships;

    protected $guarded = [];
    
    /**
     * Override the getTable() method and get the database name from config.
     *
     * @var string
     */
    public function getTable()
    {
        return config('voyager.auth.table', 'user');
    }

    public function getAvatarAttribute($value)
    {
        if (is_null($value)) {
            return config('voyager.user.default_avatar', 'users/default.png');
        }

        return $value;
    }

    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }
}
