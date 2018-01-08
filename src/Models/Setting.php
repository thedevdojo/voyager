<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\RelationshipCache;

class Setting extends Model
{
    use RelationshipCache;

    protected $table = 'settings';

    protected $guarded = [];

    public $timestamps = false;
}
