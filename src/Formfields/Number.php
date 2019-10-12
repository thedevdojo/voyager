<?php

namespace TCG\Voyager\Formfields;

class Number extends BaseFormfield
{
    public $type = 'number';

    public function __construct()
    {
        $this->name = __('voyager::bread.formfield_number');
        $this->options['placeholder'] = '';
        $this->options['default_value'] = '';
        $this->options['disabled'] = false;
        $this->options['min'] = false;
        $this->options['max'] = false;
        $this->options['step'] = false;
    }
}
