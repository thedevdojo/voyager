<?php

declare(strict_types=1);

namespace TCG\Voyager\Models;

use Carbon\Carbon;
use TCG\Voyager\Traits\VoyagerUser;
use TCG\Voyager\Traits\RelationshipCache;
use TCG\Voyager\Contracts\User as UserContract;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements UserContract
{
    use RelationshipCache;
    use VoyagerUser;

    protected $guarded = [];

    public function getAvatarAttribute($value)
    {
        if (\is_null($value)) {
            return config('voyager.user.default_avatar', 'users/default.png');
        }

        return $value;
    }

    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }
}
