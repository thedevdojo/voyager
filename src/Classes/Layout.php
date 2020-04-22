<?php

namespace TCG\Voyager\Classes;

use TCG\Voyager\Facades\Bread as BreadFacade;
use TCG\Voyager\Facades\Voyager as VoyagerFacade;

class Layout implements \JsonSerializable
{
    public $name;
    public $type = 'list';
    public $options = [];
    public $formfields = [];

    public function __construct($json)
    {
        foreach ($json as $key => $value) {
            if ($key == 'formfields') {
                $this->formfields = collect();
                foreach ($value as $f) {
                    $formfield = clone BreadFacade::getFormfield($f->type);
                    foreach ($f as $key => $prop) {
                        $formfield->{$key} = $prop;
                    }
                    $this->formfields->push($formfield);
                }
            } else {
                $this->{$key} = $value;
            }
        }
    }

    public function searchableFormfields()
    {
        return $this->formfields->where('searchable');
    }

    public function getFormfieldByColumn($column)
    {
        return $this->formfields->where('column.column', $column)->first();
    }

    public function getFormfieldsByColumnType($type)
    {
        return $this->formfields->where('column.type', $type);
    }

    public function hasTranslatableFormfields()
    {
        return $this->formfields->filter(function ($formfield) {
            return $formfield->translatable ?? false;
        })->count() > 0;
    }

    public function jsonSerialize()
    {
        return [
            'name'       => $this->name,
            'type'       => $this->type,
            'options'    => $this->options,
            'formfields' => $this->formfields,
        ];
    }
}
