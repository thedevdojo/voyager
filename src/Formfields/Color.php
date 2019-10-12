<?php

namespace TCG\Voyager\Formfields;

class Color extends BaseFormfield
{
    public $type = 'color';

    public function __construct()
    {
        $this->name = __('voyager::bread.formfield_color');
        $this->options['default_value'] = '';
        $this->options['colors'] = 'basic';
    }
}
