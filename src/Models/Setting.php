<?php

namespace TCG\Voyager\Models;

use TCG\Voyager\Events\SettingUpdated;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $guarded = [];

    public $timestamps = false;

    protected $dispatchesEvents = [
        'updating' => SettingUpdated::class
    ];
}
