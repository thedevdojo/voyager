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

    /**
     * Base directions.
     */
    const BASE_DIRECTION_LTR = 'LTR';
    const BASE_DIRECTION_RTL = 'RTL';

    /**
     * Collection of base directions.
     *
     * @var array
     */
    public static $baseDirections = [
        self::BASE_DIRECTION_LTR,
        self::BASE_DIRECTION_RTL
    ];

    protected $guarded = [];

    protected $casts = [
        'settings' => 'array',
    ];

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

    public function setLocaleAttribute($value)
    {
        $this->attributes['settings'] = collect($this->settings)->merge(['locale' => $value]);
    }

    public function getLocaleAttribute()
    {
        return $this->settings['locale'];
    }

    /**
     * Get the user's base direction.
     *
     * @return string
     */
    public function getBaseDirectionAttribute()
    {
        return $this->settings['base_direction'];
    }

    /**
     * Set the user's base direction.
     *
     * @param string $direction
     * @return void
     */
    public function setBaseDirectionAttribute($direction)
    {
        // Throw error if given direction is not a valid base direction
        if (! in_array(strtoupper($direction), self::$baseDirections)) {
            return;
        }

        $this->attributes['settings'] = collect($this->settings)->merge(['base_direction' => $direction]);
    }
}
