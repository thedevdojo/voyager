<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = ['key', 'display_name', 'value', 'options', 'type', 'order', 'details'];

    public $timestamps = false;
}
