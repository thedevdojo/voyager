<?php

namespace TCG\Voyager\Formfields;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SimpleRelationship extends BaseFormfield
{
    public $type = 'simple-relationship';
    public $lists = false; // Don't show this formfield for lists
    public $settings = false;

    public function __construct()
    {
        $this->name = __('voyager::bread.formfield.simple_relationship');
        $this->options['placeholder'] = '';
        $this->options['column'] = '';
        $this->options['max'] = 0;
        $this->options['allow_null'] = false;
        $this->options['allow_tagging'] = false;
        $this->options['allow_edit'] = true;
    }

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
}
