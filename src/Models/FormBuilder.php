<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Models\DataType;

class FormBuilder extends Model
{
    public $timestamps = false;

    public function dataTypeId(){
        return $this->belongsTo(DataType::class);
    }

    /**
     * Get the column of details and decode it from json.
     */
    public function getDetails(){
        return json_decode($this->details);
    }
}
