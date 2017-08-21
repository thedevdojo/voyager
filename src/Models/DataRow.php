<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;

class DataRow extends Model
{
    protected $table = 'data_rows';

    protected $guarded = [];

    public $timestamps = false;

    public function rowBefore(){
    	$previous = DataRow::where('data_type_id', '=', $this->data_type_id)->where('order', '=', ($this->order-1))->first();
    	if(isset($previous->id)){
    		return $previous->field;
    	}

    	return '__first__';
    }

    public function relationshipField(){
    	return rtrim($this->field, '_relationship');
    }
}
