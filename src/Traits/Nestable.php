<?php

namespace TCG\Voyager\Traits;

use TCG\Voyager\Facades\Voyager;

trait Nestable
{
    public function nestableChildren()
    {
        $dataType = Voyager::model('DataType')->where('model_name', '=', get_class())->first();

        return $this->hasMany(self::class, $this->parentField)
                    ->orderBy($dataType->order_column, $dataType->order_direction);
    }

    public function getParentField()
    {
        return $this->parentField;
    }
}
