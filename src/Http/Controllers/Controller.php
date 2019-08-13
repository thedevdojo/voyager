<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
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
        $relationships = $layout->formfields->filter(function ($formfield) {
            return Str::contains($formfield->options['field'], '.');
        })->transform(function ($formfield) {
            return Str::before($formfield->options['field'], '.');
        })->unique()->toArray();

        $query->with($relationships);
    }

    protected function searchQuery(&$query, $filters)
    {
        foreach ($filters as $field => $filter) {
            // TODO: Search translatable
            if (Str::contains($field, '.')) {
                $relationship = Str::before($field, '.');
                $field = Str::after($field, '.');
                $query = $query->whereHas($relationship, function ($query) use ($field, $filter) {
                    $query->where($field, 'like', '%'.$filter.'%');
                });
            } else {
                $query = $query->where($field, 'like', '%'.$filter.'%');
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
        } elseif ($query instanceof \Illuminate\Support\Collection) {
            $query->each(function ($item) use ($bread) {
                $item->append($bread->getComputedProperties());
            });
        }
    }

    protected function prepareData(&$data, $bread, $layout, $method = 'store')
    {
        $data = $data->transform(function ($value, $field) use ($layout, $method, $bread) {
            $formfield = $layout->formfields->where('options.field', $field)->first();
            if ($formfield) {
                return $formfield->{$method}($value);
            }
        })->filter(function ($value) {
            return $value !== null;
        });
    }
}
