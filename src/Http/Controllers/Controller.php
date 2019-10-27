<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Bread as BreadFacade;
use TCG\Voyager\Facades\Voyager as VoyagerFacade;

abstract class Controller extends BaseController
{
    use AuthorizesRequests;

    public function getBread(Request $request)
    {
        $slug = explode('.', $request->route()->getName())[1];

        return BreadFacade::getBreadBySlug($slug);
    }

    protected function searchQuery(&$query, $layout, $filters, $global)
    {
        if ($global != '') {
            $columns = $layout->getSearchableColumns()->pluck('column')->filter(function ($column) {
                return !Str::contains($column, '.');
                // TODO: Also search for relationships?
            });
            $query->where(function ($query) use ($columns, $global) {
                $columns->each(function ($column) use (&$query, $global) {
                    $query->orWhere($column, 'LIKE', '%'.$global.'%');
                });
            });
        }

        foreach ($filters as $column => $filter) {
            // TODO: Search translatable
            if (Str::contains($column, '.')) {
                $relationship = Str::before($column, '.');
                $column = Str::after($column, '.');
                if (Str::contains($column, 'pivot.')) {
                    // TODO: Unfortunately we can't use wherePivot() here.
                } else {
                    $query = $query->whereHas($relationship, function ($query) use ($column, $filter) {
                        $query->where($column, 'like', '%'.$filter.'%');
                    });
                }
            } else {
                $formfield = $layout->formfields->where('column', $column)->first();
                if ($formfield) {
                    $query = $formfield->query($query, $column, $filter);
                }
            }
        }
    }

    protected function orderQuery(&$query, $bread, $column, $direction)
    {
        if ($bread->isColumnTranslatable($column)) {
            // TODO: Order by translatable
            $query = $query->orderBy($column, $direction);
        } else {
            $query = $query->orderBy($column, $direction);
        }
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

    // Manipulate data to be shown when browsing, showing or editing
    protected function prepareDataForEditing(Model $model, $bread, $layout): Model
    {
        return $this->prepareDataForBrowsing($model, $bread, $layout, 'edit');
    }

    protected function prepareDataForShowing(Model $model, $bread, $layout): Model
    {
        return $this->prepareDataForBrowsing($model, $bread, $layout, 'show');
    }

    protected function prepareDataForBrowsing(Model $model, $bread, $layout, $method = 'browse'): Model
    {
        $layout->formfields->each(function ($formfield) use (&$model, $bread, $method) {
            $column = $formfield->column;
            $value = '';
            if (array_key_exists($formfield->column, $model->getAttributes())) {
                $value = $model->{$formfield->column};
            } elseif (Str::contains($column, '.')) {
                $rl_column = Str::after($column, '.');
                $rl_name = Str::before($column, '.');
                $model->with($rl_name);
                if ($model->{$rl_name} instanceof Collection) {
                    $value = $model->{$rl_name}->pluck($rl_column);
                } elseif ($model->{$rl_name}) {
                    $value = $model->{$rl_name}->{$rl_column};
                }
            }

            $new_value = $formfield->{$method}($value, $model);

            // Merge columns in $new_value back into the model
            foreach ($new_value as $key => $value) {
                $model->{$key} = $value;
            }
        });
        $model->primary = $model->getKey();

        return $model;
    }

    // Manipulate data to be stored in the database when updating
    protected function prepareDataForUpdating($data, Model $model, $bread, $layout): Model
    {
        return $this->prepareDataForStoring($data, $model, $bread, $layout, 'update');
    }

    // Manipulate data to be stored in the database when creating
    protected function prepareDataForStoring($data, Model $model, $bread, $layout, $method = 'store'): Model
    {
        $columns = VoyagerFacade::getColumns($model->getTable());
        $layout->formfields->each(function ($formfield) use ($data, &$model, $bread, $method, $columns) {
            debug($formfield->column);
            $value = $data->get($formfield->column, null);
            $old = null;
            //if (array_key_exists($formfield->column, $model->getAttributes())) {
                $old = $model->{$formfield->column};
            //}
            $new_value = collect($formfield->{$method}($value, $old, $model, $data));
            $new_value->transform(function ($value, $column) use ($bread, $method) {
                if ($bread->isColumnTranslatable($column)) {
                    // TODO: We need to test for casts here
                    return json_encode($value);
                }

                return $value;
            });

            // Merge columns in $new_value back into the model
            foreach ($new_value as $column => $value) {
                if (in_array($formfield->column, $columns)) {
                    $model->{$column} = $value;
                }
            }
        });

        return $model;
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
                $message_ident = $formfield->column.'.'.Str::before($rule_object->rule, ':');
                $message = $rule_object->message;
                if (is_object($message)) {
                    $message = $message->{VoyagerFacade::getLocale()} ?? $message->{VoyagerFacade::getFallbackLocale()} ?? '';
                }
                $messages[$message_ident] = $message;
            });
            $rules[$formfield->column] = $formfield_rules;
        });

        $validator = Validator::make($data->toArray(), $rules, $messages);

        return $validator;
    }
}
