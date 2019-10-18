<?php

namespace TCG\Voyager\Formfields;

class Text extends BaseFormfield
{
    public $type = 'text';

    public function __construct()
    {
        $this->name = __('voyager::bread.formfield_text');
        $this->options['placeholder'] = '';
        $this->options['default_value'] = '';
        $this->options['disabled'] = false;
        $this->options['rows'] = 1;
        $this->options['max_characters'] = 50;
    }
}
