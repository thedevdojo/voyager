<?php

namespace TCG\Voyager\Formfields;

use TCG\Voyager\Contracts\Bread\Formfield;

class Number extends Formfield
{
    public function type(): string
    {
        return 'number';
    }

    public function name(): string
    {
        return __('voyager::formfields.number.name');
    }

    public function listOptions(): array
    {
        return [
            'decimals'      => 0,
            'dec_point'     => '.',
            'thousands_sep' => ',',
        ];
    }

    public function viewOptions(): array
    {
        return [
            'label'         => '',
            'description'   => '',
            'placeholder'   => '',
            'min'           => 0,
            'max'           => 0,
            'step'          => 1,
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
