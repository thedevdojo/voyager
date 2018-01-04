<?php

declare(strict_types=1);

namespace TCG\Voyager\Models;

use Cache;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $guarded = [];

    public $timestamps = false;

    /**
     * Clear cache on value change.
     *
     * @param string $value
     *
     * @return void
     */
    public function setValueAttribute($value)
    {
        Cache::forget('settings.'.$this->key);

        $this->attributes['value'] = $value;
    }
}
