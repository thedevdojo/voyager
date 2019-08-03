<?php

namespace TCG\Voyager\Formfields;

class Text extends BaseFormfield
{
    public $type = 'text';
    public $name = 'Text';

    public function __construct()
    {
        $this->options['placeholder'] = '';
        $this->options['default_value'] = '';
        $this->options['disabled'] = false;
    }
}
