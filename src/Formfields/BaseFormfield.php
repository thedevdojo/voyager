<?php

namespace TCG\Voyager\Formfields;

class BaseFormfield implements \JsonSerializable
{
    public $type;
    public $name;
    public $options = [
        'width'       => '1/2',
        'title'       => '',
        'description' => '',
    ];
    public $rules = [];

    /**
     * Transform data to be stored in the database after updating.
     *
     * @param mixed $data The input-data
     *
     * @return mixed The processed data
     */
    public function update($data)
    {
        return $data;
    }

    /**
     * Transform data to be stored in the database after adding.
     *
     * @param mixed $data The input-data
     *
     * @return mixed The processed data
     */
    public function store($data)
    {
        return $data;
    }

    /**
     * Check if the formfield is valid.
     *
     * @return bool Is the formfield valid
     */
    public function isValid()
    {
        return true;
    }

    public function jsonSerialize()
    {
        return [
            'type'    => $this->type,
            'options' => $this->options,
            'rules'   => $this->rules,
        ];
    }
}
