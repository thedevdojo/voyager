<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;

abstract class Controller extends BaseController
{
    use AuthorizesRequests;

    public function getBread(Request $request)
    {
        $slug = explode('.', $request->route()->getName())[1];

        return Voyager::getBreadBySlug($slug);
    }

    protected function loadRelationships(&$query, $layout)
    {
        $layout->formfields->filter(function ($formfield) {
            return Str::contains($formfield->field, '.');
        })->transform(function ($formfield) {
            return Str::before($formfield->field, '.');
        })->unique()->each(function ($relationship) use ($query) {
            $query->with([$relationship => function ($query) {
                //
            }]);
        });
    }

    protected function searchQuery(&$query, $layout, $filters, $global)
    {
        if ($global != '') {
            $fields = $layout->getSearchableFields()->pluck('field');
            $query->where(function ($query) use ($fields, $global) {
                $fields->each(function ($field) use (&$query, $global) {
                    $query->orWhere($field, 'LIKE', '%'.$global.'%');
                });
            });
        }

        foreach ($filters as $field => $filter) {
            // TODO: Search translatable
            if (Str::contains($field, '.')) {
                $relationship = Str::before($field, '.');
                $field = Str::after($field, '.');
                $query = $query->whereHas($relationship, function ($query) use ($field, $filter) {
                    $query->where($field, 'like', '%'.$filter.'%');
                });
            } else {
                $formfield = $layout->formfields->where('field', $field)->first();
                if ($formfield) {
                    $query = $formfield->query($query, $field, $filter);
                }
            }
        }
    }

    protected function orderQuery(&$query, $field, $direction)
    {
        // TODO: Order by translatable
        $query = $query->orderBy($field, $direction)->get();
    }

    protected function loadAccessors(&$query, $bread)
    {
        if ($query instanceof \Illuminate\Database\Eloquent\Model) {
            $query->append($bread->getComputedProperties());
        } elseif ($query instanceof Collection) {
            $query->each(function ($item) use ($bread) {
                $item->append($bread->getComputedProperties());
            });
        }
    }

    protected function prepareData(Collection $data, $old, $bread, $layout, $method = 'store'): Collection
    {
        $layout->formfields->each(function ($formfield) use (&$data, $old, $bread, $method) {
            $field = $formfield->field;
            $value = $data->get($field, null);
            $old_value = $old->{$field} ?? null;
            $new_value = collect($formfield->{$method}($value, $old_value, $data, $bread->getFieldType($field)));
            $new_value->transform(function ($value, $field) use ($bread, $method) {
                if ($bread->isFieldTranslatable($field)) {
                    // TODO: We need to test for casts here
                    if ($method == 'update' || $method == 'store') {
                        return json_encode($value);
                    }

                    return $value;
                }

                return $value;
            });
            $data = $data->merge($new_value);
        });

        return $data;
    }

    protected function getValidator($layout, $data)
    {
        $rules = [];
        $messages = [];

        $layout->formfields->each(function ($formfield) use (&$rules, &$messages) {
            $formfield_rules = [];
            collect($formfield->rules)->each(function ($rule_object) use ($formfield, &$formfield_rules, &$messages) {
                // TODO: Add translation validation
                $formfield_rules[] = $rule_object->rule;
                $message_ident = $formfield->field.'.'.Str::before($rule_object->rule, ':');
                $message = $rule_object->message;
                if (is_object($message)) {
                    $message = $message->{Voyager::getLocale()} ?? $message->{Voyager::getFallbackLocale()} ?? '';
                }
                $messages[$message_ident] = $message;
            });
            $rules[$formfield->field] = $formfield_rules;
        });

        $validator = Validator::make($data->toArray(), $rules, $messages);

        return $validator;
    }
}
