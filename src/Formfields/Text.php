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
        return 'Text';
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

    public function browse($input)
    {
        return $input;
    }

    public function read($input)
    {
        return $input;
    }

    public function edit($input)
    {
        return $input;
    }

    public function update($input, $old)
    {
        return $input;
    }

    public function add()
    {
        return $input;
    }

    public function store($input)
    {
        return $input;
    }
}
