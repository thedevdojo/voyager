<?php

namespace TCG\Voyager\Formfields;

use TCG\Voyager\Contracts\Bread\Formfield;

class Tags extends Formfield
{
    public function type(): string
    {
        return 'tags';
    }

    public function name(): string
    {
        return __('voyager::formfields.tags.name');
    }

    public function listOptions(): array
    {
        return [];
    }

    public function viewOptions(): array
    {
        return [
            'label'       => '',
            'description' => '',
            'min'         => 0,
            'max'         => 0,
        ];
    }

    public function browse($input)
    {
        return json_decode($input);
    }

    public function read($input)
    {
        return json_decode($input);
    }

    public function edit($input)
    {
        return json_decode($input);
    }

    public function update($input, $old)
    {
        return json_encode($input);
    }

    public function add()
    {
        return [];
    }

    public function store($input)
    {
        return json_encode($input);
    }

    public function browseDataAsArray()
    {
        return true;
    }
}
