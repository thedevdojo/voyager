<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;

class DataType extends Model
{
    protected $table = 'data_types';

    public function rows(){
    	return $this->hasMany('TCG\Voyager\Models\DataRow');
    }

    public function browseRows(){
    	return $this->hasMany('TCG\Voyager\Models\DataRow')->where('browse', '=', 1);
    }

    public function readRows(){
    	return $this->hasMany('TCG\Voyager\Models\DataRow')->where('read', '=', 1);
    }

    public function editRows(){
    	return $this->hasMany('TCG\Voyager\Models\DataRow')->where('edit', '=', 1);
    }

    public function addRows(){
    	return $this->hasMany('TCG\Voyager\Models\DataRow')->where('add', '=', 1);
    }

    public function deleteRows(){
    	return $this->hasMany('TCG\Voyager\Models\DataRow')->where('delete', '=', 1);
    }
}
