<?php

namespace TCG\Voyager\Formfields;

use TCG\Voyager\Contracts\Bread\Formfield;

class Text extends Formfield
{
    public function type(): string
    {
        return 'text';
    }

    public function name(): string
    {
        return __('voyager::formfields.text.name');
    }

    public function listOptions(): array
    {
        return [
            'display_length'    => 150,
        ];
    }

    public function viewOptions(): array
    {
        return [
            'label'         => '',
            'description'   => '',
            'placeholder'   => '',
            'default_value' => '',
            'rows'          => 1,
        ];
    }
}
