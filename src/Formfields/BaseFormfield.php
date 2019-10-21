<?php

namespace TCG\Voyager\Formfields;

class BaseFormfield implements \JsonSerializable
{
    public $type;
    public $name;
    public $column;
    public $options = [
        'width'       => '1/2',
        'title'       => '',
        'description' => '',
    ];
    public $rules = [];

    /**
     * Transform data to be shown when browsing.
     *
     * @param mixed  $data         The input-data
     * @param object $request_data the whole request data object
     *
     * @return mixed The processed data
     */
    public function browse($data, $model)
    {
        return [
            $this->column => $data,
        ];
    }

    /**
     * Transform data to be shown when showing.
     *
     * @param mixed $data  The input-data
     * @param Model $model The Model instance
     *
     * @return mixed The processed data
     */
    public function show($data, $model)
    {
        return [
            $this->column => $data,
        ];
    }

    /**
     * Transform data to be shown when editing.
     *
     * @param mixed $data  The input-data
     * @param Model $model The Model instance
     *
     * @return mixed The processed data
     */
    public function edit($data, $model)
    {
        return [
            $this->column => $data,
        ];
    }

    /**
     * Transform data to be stored in the database after updating.
     *
     * @param mixed  $data         The input-data
     * @param mixed  $old          The old data
     * @param Model  $model        The Model instance
     * @param object $request_data the whole request data object
     *
     * @return mixed The processed data
     */
    public function update($data, $old, $model, $request_data)
    {
        return [
            $this->column => $data,
        ];
    }

    /**
     * Transform data to be stored in the database after adding.
     *
     * @param mixed  $data         The input-data
     * @param mixed  $old          The old data (null)
     * @param Model  $model        The Model instance
     * @param object $request_data the whole request data object
     *
     * @return mixed The processed data
     */
    public function store($data, $old, $model, $request_data)
    {
        return [
            $this->column => $data,
        ];
    }

    /**
     * Query a formfield.
     *
     * @param Illuminate\Database\Eloquent\Builder $query  The query-builder
     * @param string                               $column The column-name
     * @param mixed                                $filter The filter-value
     *
     * @return mixed The processed data
     */
    public function query($query, $column, $filter)
    {
        return $query->where($column, 'like', '%'.$filter.'%');
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
            'column'  => $this->column,
            'options' => $this->options,
            'rules'   => $this->rules,
        ];
    }
}
