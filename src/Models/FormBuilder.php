<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;

class FormBuilder extends Model
{
    public $timestamps = false;

    protected $fillable = ['data_type_id', 'details'];

    public function dataTypeId()
    {
        return $this->belongsTo(DataType::class);
    }

    /**
     * Get the column of details and decode it from json to array.
     *
     * @return array
     */
    public function getDetails()
    {
        return json_decode($this->details);
    }
}
