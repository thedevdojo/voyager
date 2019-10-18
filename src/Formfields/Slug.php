<?php

namespace TCG\Voyager\Formfields;

class Slug extends BaseFormfield
{
    public $type = 'slug';

    public function __construct()
    {
        $this->name = __('voyager::bread.formfield_slug');
        $this->options['placeholder'] = '';
        $this->options['default_value'] = '';
        $this->options['disabled'] = false;
        $this->options['from'] = '';
        $this->options['char'] = '-';
        $this->options['lower'] = true;
    }
}
