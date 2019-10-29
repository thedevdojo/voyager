<?php

namespace TCG\Voyager\Classes;

use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Traits\Translatable;

class Setting implements \JsonSerializable
{
    use Translatable;

    private $translatable = ['title', 'value'];

    public $group, $key, $formfield;
    protected $title, $value;

    public function __construct($parameter)
    {
        foreach ($parameter as $key => $value) {
            if ($key == 'formfield') {
                $formfield_class = clone Voyager::getFormfield($value->type);
                if (!$formfield_class) {
                    Voyager::flashMessage('Formfield "'.$value->type.'" couldn\'t be found.', 'debug');
                    continue;
                }
                foreach ($value as $f_key => $f_value) {
                    if ($f_key == 'options') {
                        $formfield_class->options = array_merge($formfield_class->options, (array) $f_value);
                    } else {
                        $formfield_class->{$f_key} = $f_value;
                    }
                }

                if ($formfield_class->isValid()) {
                    $this->formfield = $formfield_class;
                }
            } else {
                $this->{$key} = $value;
            }
        }
    }

    public function jsonSerialize()
    {
        return [
            'group'     => $this->group,
            'key'       => $this->key,
            'title'     => $this->title,
            'value'     => $this->value,
            'formfield' => $this->formfield,
        ];
    }
}
