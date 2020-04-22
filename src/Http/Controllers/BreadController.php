<?php

namespace TCG\Voyager\Http\Controllers;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager as VoyagerFacade;

class BreadController extends Controller
{
    public function data(Request $request)
    {
        $start = microtime(true);
        $bread = $this->getBread($request);
        $layout = $this->getLayoutForAction($bread, 'browse');

        list(
            'page'        => $page,
            'perpage'     => $perpage,
            'query'       => $global,
            'filters'     => $filters,
            'order'       => $order,
            'direction'   => $direction,
            'softdeleted' => $softdeleted,
            'locale'      => $locale,
        ) = $request->all();

        $model = $bread->getModel();

        $query = $model->select('*');

        // Soft-deletes
        $uses_soft_deletes = $bread->usesSoftDeletes();
        if (!isset($layout->options->soft_deletes) || !$layout->options->soft_deletes) {
            $uses_soft_deletes = false;
        }
        if ($uses_soft_deletes) {
            if ($softdeleted == 'show') {
                $query = $query->withTrashed();
            } else if ($softdeleted == 'only') {
                $query = $query->onlyTrashed();
            }
        }

        $total = $query->count();

        // Global search ($global)
        if (!empty($global)) {
            $query->where(function ($query) use ($global, $layout, $locale) {
                $layout->searchableFormfields()->each(function ($formfield) use (&$query, $global, $locale) {
                    $column_type = $formfield->column->type;
                    $column = $formfield->column->column;

                    if ($column_type == 'computed') {
                        // TODO
                    } else if ($column_type == 'relationship') {
                        // TODO
                    } else if ($formfield->translatable ?? false) {
                        $query->orWhere(DB::raw('lower('.$column.'->"'.$locale.'")'), 'LIKE', '%'.strtolower($global).'%');
                    } else {
                        $query->orWhere(DB::raw('lower('.$column.')'), 'LIKE', '%'.strtolower($global).'%');
                    }
                });
            });
        }

        // Field search ($filters)
        foreach (array_filter($filters) as $column => $filter) {
            $formfield = $layout->getFormfieldByColumn($column);
            $column_type = $formfield->column->type;

            if ($column_type == 'computed') {
                // TODO
            } else if ($column_type == 'relationship') {
                // TODO
            } else if ($formfield->translatable ?? false) {
                $query->where(DB::raw('lower('.$column.'->"$.'.$locale.'")'), 'LIKE', '%'.strtolower($filter).'%');
            } else {
                $query->where(DB::raw('lower('.$column.')'), 'LIKE', '%'.strtolower($filter).'%');
            }
        }

        // Ordering ($order and $direction)
        if (!empty($direction) && !empty($order)) {
            $formfield = $layout->getFormfieldByColumn($order);
            $column_type = $formfield->column->type;

            if ($column_type == 'computed') {
                // TODO
            } else if ($column_type == 'relationship') {
                // TODO
            } else if ($formfield->translatable ?? false) {
                if ($direction == 'desc') {
                    $query = $query->orderByDesc(DB::raw('lower('.$order.'->"$.'.$locale.'")'));
                } else {
                    $query = $query->orderBy(DB::raw('lower('.$order.'->"$.'.$locale.'")'));
                }
            } else {
                if ($direction == 'desc') {
                    $query = $query->orderByDesc($order);
                } else {
                    $query = $query->orderBy($order);
                }
            }
        }
        
        $query = $query->get();
        $filtered = $query->count();

        // Pagination ($page and $perpage)
        $query = $query->slice(($page - 1) * $perpage)->take($perpage);

        // Load accessors
        $accessors = $layout->getFormfieldsByColumnType('computed')->pluck('column.column')->toArray();
        $query = $query->each(function ($item) use ($accessors) {
            $item->append($accessors);
        });

        // Transform results
        $query = $query->transform(function ($item) use ($uses_soft_deletes, $layout) {
            $item->translate = false;
            // Add soft-deleted property
            $item->is_soft_deleted = false;
            if ($uses_soft_deletes && !empty($item->deleted_at)) {
                $item->is_soft_deleted = $item->trashed();
            }
            // TODO: Pass each result through formfields browse() method
            $layout->formfields->each(function ($formfield) use (&$item) {
                $item->{$formfield->column->column} = $formfield->browse($item->{$formfield->column->column});
            });

            return $item;
        });

        return [
            'results'           => $query->values(),
            'filtered'          => $filtered,
            'total'             => $total,
            'layout'            => $layout,
            'execution'         => number_format(((microtime(true) - $start) * 1000)),
            'uses_soft_deletes' => $uses_soft_deletes,
            'primary'           => $query->get(0) ? $query->get(0)->getKeyName() : 'id'
        ];
    }

    public function add(Request $request)
    {
        $bread = $this->getBread($request);
        $layout = $this->getLayoutForAction($bread, 'add');
        $new = true;

        return view('voyager::bread.edit-add', compact('bread', 'layout', 'new'));
    }

    public function store(Request $request)
    {
        $bread = $this->getBread($request);
        $layout = $this->getLayoutForAction($bread, 'add');

        $model = new $bread->model();
        $data = $request->get('data', []);

        if ($bread->usesTranslatableTrait()) {
            $model->dontTranslate();
        }

        // Validate Data
        $validation_errors = $this->getValidationErrors($layout, $data);
        if (count($validation_errors) > 0) {
            return response()->json($validation_errors, 422);
        }

        $layout->formfields->each(function ($formfield) use ($data, &$model) {
            $value = $data[$formfield->column->column] ?? '';
            $value = $formfield->store($value);

            if ($formfield->translatable ?? false) {
                $value = json_encode($value);
            }

            if ($formfield->column->type == 'column') {
                $model->{$formfield->column->column} = $value;
            } elseif ($formfield->column->type == 'computed') {
                if (method_exists($model, 'set'.Str::camel($formfield->column->column).'Attribute')) {
                    $model->{$formfield->column->column} = $value;
                }
            } elseif ($formfield->column->type == 'relationship') {
                // 
            }
        });

        if ($model->save()) {
            return response(500);
        } else {
            return response(200, $model->getKey());
        }
    }

    public function read(Request $request, $id)
    {
        $bread = $this->getBread($request);
        $layout = $this->getLayoutForAction($bread, 'read');
        $data = $bread->getModel()->findOrFail($id);

        return view('voyager::bread.read', compact('bread', 'data', 'layout'));
    }

    public function edit(Request $request, $id)
    {
        $bread = $this->getBread($request);
        $layout = $this->getLayoutForAction($bread, 'add');
        $new = false;
        $data = $bread->getModel()->findOrFail($id);

        return view('voyager::bread.edit-add', compact('bread', 'layout', 'new', 'data'));
    }

    public function update(Request $request, $id)
    {
        $bread = $this->getBread($request);
        $layout = $this->getLayoutForAction($bread, 'add');

        $model = $bread->getModel()->findOrFail($id);
        $data = $request->get('data', []);

        if ($bread->usesTranslatableTrait()) {
            $model->dontTranslate();
        }

        // Validate Data
        $validation_errors = $this->getValidationErrors($layout, $data);
        if (count($validation_errors) > 0) {
            return response()->json($validation_errors, 422);
        }

        $layout->formfields->each(function ($formfield) use ($data, &$model) {
            $value = $data[$formfield->column->column] ?? '';
            $value = $formfield->update($value, $model->{$formfield->column->column});

            if ($formfield->translatable ?? false) {
                $value = json_encode($value);
            }

            if ($formfield->column->type == 'column') {
                $model->{$formfield->column->column} = $value;
            } elseif ($formfield->column->type == 'computed') {
                // 
            } elseif ($formfield->column->type == 'relationship') {
                // 
            }
        });

        if ($model->save()) {
            return response(500);
        } else {
            return response(200, $model->getKey());
        }
    }

    public function delete(Request $request)
    {
        $bread = $this->getBread($request);
        $model = $bread->getModel();
        if ($bread->usesSoftDeletes()) {
            $model = $model->withTrashed();
        }

        $deleted = 0;

        $force = $request->get('force', 'false');
        if ($request->has('ids')) {
            $ids = $request->get('ids');
            if (!is_array($ids)) {
                $ids = [$ids];
            }
            $model->find($ids)->each(function ($entry) use ($bread, $force, &$deleted) {
                if ($force == 'true' && $bread->usesSoftDeletes()) {
                    // TODO: Check if layout allows usage of soft-deletes
                    if ($entry->trashed()) {
                        $this->authorize('force-delete', $entry);
                        $entry->forceDelete();
                        $deleted++;
                    }
                } else {
                    $this->authorize('delete', $entry);
                    $entry->delete();
                    $deleted++;
                }
            });
        }

        return $deleted;
    }

    public function restore(Request $request)
    {
        // TODO: Check if layout allows usage of soft-deletes
        $bread = $this->getBread($request);
        if (!$bread->usesSoftDeletes()) {
            return;
        }

        $restored = 0;

        $model = $bread->getModel()->withTrashed();

        if ($request->has('ids')) {
            $ids = $request->get('ids');
            if (!is_array($ids)) {
                $ids = [$ids];
            }

            $model->find($ids)->each(function ($entry) use ($bread, &$restored) {
                if ($entry->trashed()) {
                    $this->authorize('restore', $entry);
                    $entry->restore();
                    $restored++;
                }
            });
        }

        return $restored;
    }

    private function getBread(Request $request)
    {
        return $request->route()->getAction()['bread'] ?? abort(404);
    }

    private function getLayoutForAction($bread, $action)
    {
        if ($action == 'browse') {
            return $bread->layouts->where('type', 'list')->first();
        }

        return $bread->layouts->where('type', 'view')->first();
    }

    private function getValidationErrors($layout, $data): array
    {
        $errors = [];

        $layout->formfields->each(function ($formfield) use (&$errors, $layout, $data) {
            $value = $data[$formfield->column->column] ?? '';
            if ($formfield->translatable && is_array($value)) {
                // TODO: We could validate ALL locales here. But mostly, this doesn't make sense (Let user select?)
                $value = $value[VoyagerFacade::getLocale()] ?? $value[VoyagerFacade::getFallbackLocale()];
            }
            foreach ($formfield->validation as $rule) {
                $validator = Validator::make(['col' => $value], ['col' => $rule->rule]);

                if ($validator->fails()) {
                    $message = $rule->message;
                    if (is_object($message)) {
                        $message = $message->{VoyagerFacade::getLocale()} ?? $message->{VoyagerFacade::getFallbackLocale()} ?? '';
                    }
                    $errors[$formfield->column->column][] = $message;
                }
            }
        });

        return $errors;
    }
}
