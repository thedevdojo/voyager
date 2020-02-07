<?php

namespace TCG\Voyager\Models;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use TCG\Voyager\Contracts\User as UserContract;
use TCG\Voyager\Traits\VoyagerUser;

class User extends Authenticatable implements UserContract
{
    use VoyagerUser;

    protected $guarded = [];

    public $additional_attributes = ['locale','mfa'];

    public function getAvatarAttribute($value)
    {
        return $value ?? config('voyager.user.default_avatar', 'users/default.png');
    }

    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function setSettingsAttribute($value)
    {
        $this->attributes['settings'] = $value->toJson();
    }

    public function getSettingsAttribute($value)
    {
        return collect(json_decode($value));
    }

    public function setGenericAttribute($attr, $value)
    {
        $this->settings = $this->settings->merge([$attr => $value]);
    }

    public function getGenericAttribute($attr)
    {
        return $this->settings->get($attr);
    }

    public function setLocaleAttribute($value)
    {
        $this->setGenericAttribute('locale', $value);
    }

    public function getLocaleAttribute()
    {
        return $this->getGenericAttribute('locale');
    }

    public function setMfaAttribute($value)
    {
        $this->setGenericAttribute('mfa', $value);
    }

    public function getMfaAttribute()
    {
        return $this->getGenericAttribute('mfa');
    }
}
