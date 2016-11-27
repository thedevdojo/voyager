<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;

class DataType extends Model
{
    protected $table = 'data_types';

    protected $fillable = [
        'name', 'slug', 'display_name_singular', 'display_name_plural', 'icon', 'model_name', 'description',
    ];

    public function rows()
    {
        return $this->hasMany(DataRow::class);
    }

    public function browseRows()
    {
        return $this->rows()->where('browse', '=', 1);
    }

    public function readRows()
    {
        return $this->rows()->where('read', '=', 1);
    }

    public function editRows()
    {
        return $this->rows()->where('edit', '=', 1);
    }

    public function addRows()
    {
        return $this->rows()->where('add', '=', 1);
    }

    public function deleteRows()
    {
        return $this->rows()->where('delete', '=', 1);
    }
}
