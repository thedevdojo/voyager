<?php

namespace TCG\Voyager\Formfields;

class BaseFormfield implements \JsonSerializable
{
    public $type;
    public $name;
    public $column;
    public $options = [
        'width'        => '1/2',
        'title'        => '',
        'description'  => '',
        'translatable' => false,
    ];
    public $rules = [];

    public $translatable = true; // If the formfield can be translatable
    public $lists = true; // If this formfield can be used in Lists
    public $views = true; // If this formfield can be used in Views
    public $settings = true; // If this formfield can be used as a setting

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
     * Do something AFTER the model was updated.
     *
     * @param Model  $model The Model instance
     * @param object $data  The request data object
     */
    public function updated($model, $request_data)
    {
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
     * Do something AFTER the model was stored.
     *
     * @param Model  $model The Model instance
     * @param object $data  The request data object
     */
    public function stored($model, $data)
    {
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
            'type'         => $this->type,
            'column'       => $this->column,
            'options'      => $this->options,
            'rules'        => $this->rules,
            'translatable' => $this->translatable,
            'lists'        => $this->lists,
            'views'        => $this->views,
            'settings'     => $this->settings,
        ];
    }
}
