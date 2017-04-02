<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $table = 'translations';

    protected $fillable = ['table_name', 'column_name', 'foreign_key', 'locale', 'value'];
}
