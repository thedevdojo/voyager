<?php

namespace TCG\Voyager\Models;

use TCG\Voyager\Database\Model;

class Translation extends Model
{
    protected $table = 'translations';

    protected $fillable = ['table_name', 'column_name', 'foreign_key', 'locale', 'value'];
}
