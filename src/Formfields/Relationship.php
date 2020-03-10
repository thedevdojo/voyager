<?php

namespace TCG\Voyager\Formfields;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Relationship extends BaseFormfield
{
    public $type = 'relationship';
    public $translatable = false;
    public $lists = false; // Don't show this formfield for lists
    public $settings = false;

    private $data;

    public function __construct()
    {
        $this->name = __('voyager::bread.formfield.relationship');
        $this->options['column'] = '';
        $this->options['order_column'] = '';
        $this->options['add_view'] = null;
    }

    /*public function edit($data, $model)
    {
        return [
            $this->column => $data,
        ];
    }*/

    public function update($data, $old, $model, $request_data)
    {
        // Sync $data with $model
        $relationship = $model->{$this->column}();
        if ($relationship instanceof BelongsTo) {
            $relationship->associate($data);
        } elseif ($relationship instanceof BelongsToMany) {
            $relationship->sync($data);
        } elseif ($relationship instanceof HasMany || $relationship instanceof HasOne) {
            $foreign_key = $relationship->getForeignKeyName();
            // Detach all which have $model->getKey() as foreign-key
            $relationship->getRelated()->where($foreign_key, $model->getKey())->update($foreign_key, '');
            // Attach $model->getKey() to $data
            if ($relationship instanceof HasMany) {
                $relationship->getRelated()->find($data)->update([$foreign_key => $model->getKey()]);
            } else {
                foreach ($data as $key) {
                    $relationship->getRelated()->find($key)->update([$foreign_key => $model->getKey()]);
                }
            }
        }

        return [];
    }

    public function store($data, $old, $model, $request_data)
    {
        $this->data = $data;
        // Do nothing. Relationships can only be changed once the model WAS stored.
        return [];
    }

    public function stored($model, $data)
    {
        $this->update($this->data, null, $model, null);
    }
}
