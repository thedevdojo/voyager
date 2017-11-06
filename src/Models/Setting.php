<?php

namespace TCG\Voyager\Models;

use TCG\Voyager\Database\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $guarded = [];

    public $timestamps = false;
}
