<?php

namespace TCG\Voyager\Formfields;

class DynamicDropdown extends BaseFormfield
{
    public $type = 'dynamic-dropdown';
    public $settings = false;

    public function __construct()
    {
        $this->name = __('voyager::bread.formfield.dynamic_dropdown');
        $this->options['controller'] = '';
        $this->options['method'] = '';
        $this->options['store'] = 'key';
        $this->options['size'] = 1;
    }
}
