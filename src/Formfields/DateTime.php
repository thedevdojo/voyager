<?php

namespace TCG\Voyager\Formfields;

class DateTime extends BaseFormfield
{
    public $type = 'date-time';

    public function __construct()
    {
        $this->name = __('voyager::bread.formfield_date_time');
        $this->options['placeholder'] = '';
        $this->options['default_value'] = '';
        $this->options['disabled'] = false;
        $this->options['min'] = false;
        $this->options['max'] = false;
        $this->options['step'] = false;
    }
}
