<?php

namespace TCG\Voyager\Formfields;

use TCG\Voyager\Contracts\Bread\Formfield;

class Repeater extends Formfield
{
    public function type(): string
    {
        return 'repeater';
    }

    public function name(): string
    {
        return __('voyager::formfields.repeater.name');
    }

    public function listOptions(): array
    {
        return [];
    }

    public function viewOptions(): array
    {
        return [
            'min'         => 0,
            'max'         => 0,
            'add_text'    => __('voyager::generic.add'),
            'remove_text' => __('voyager::generic.remove'),
            'formfields'  => [],
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