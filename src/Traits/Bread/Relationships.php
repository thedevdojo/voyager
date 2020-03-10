<?php

namespace TCG\Voyager\Traits\Bread;

use Illuminate\Database\Eloquent\Relations\BelongsToMany as BTM;
use Illuminate\Database\Eloquent\Relations\HasMany as HM;
use Illuminate\Database\Eloquent\Relations\HasManyThrough as HMT;
use Illuminate\Http\Request;

trait Relationships
{
    public function relationshipData(Request $request)
    {
        $bread = $this->getBread($request);
        $multiple = false;
        $selected = [];
        $options = [];
        $count = 0;
        $relationship_name = $request->get('relationship');
        $new_key = $request->get('newkey', null); // The primary key of a newly generated related-entry which wasn't assigned yet

        $relationship = $bread->getModel()->{$relationship_name}();

        if ($relationship instanceof BTM || $relationship instanceof HM || $relationship instanceof HMT) {
            $multiple = true;
        }

        $order = $request->get('options')['order_column'] ?? null;

        if ($primary = $request->get('primary', null)) {
            $selected = $bread->getModel()->findOrFail($primary)->{$relationship_name};

            if ($multiple) {
                if ($new_key) {
                    $selected->push($relationship->getRelated()->findOrFail($new_key));
                }
                if ($order) {
                    $selected = $selected->sortBy($order);
                }
                $selected = $selected->pluck($relationship->getRelated()->getKeyName());
                $count = $selected->count();
            } elseif ($selected) {
                if ($new_key) {
                    $selected = $new_key;
                } else {
                    $selected = $selected->{$relationship->getRelated()->getKeyName()};
                }
                $count = 1;
            }
        }

        $options = $relationship->getRelated()->all();
        if ($order && $multiple) {
            $options = $options->sortBy($order)->values();
        }

        return [
            'multiple' => $multiple,
            'selected' => $selected,
            'options'  => $options,
            'primary'  => $relationship->getRelated()->getKeyName(),
            'count'    => $count,
        ];
    }
}