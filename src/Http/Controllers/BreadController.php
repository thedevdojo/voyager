<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use TCG\Voyager\Facades\Voyager;

class BreadController extends Controller
{
    public function index(Request $request)
    {
        $bread = $this->getBread($request);
        $layout = $bread->getLayoutFor('browse');
        //$this->authorize('browse', app($bread->model));

        $actions = Voyager::getActionsForBread($bread);

        return view('voyager::bread.browse', compact('bread', 'layout', 'actions'));
    }

    public function data(Request $request)
    {
        $bread = $this->getBread($request);
        $layout = $bread->getLayoutFor('browse');
        //$this->authorize('browse', app($bread->model));
        $model = $bread->getModel();

        $records = 0;
        $rows = [];
        $query = $model->select('*');

        if ($bread->usesSoftDeletes()) {
            if ($bread->soft_deletes == 'show') {
                $query = $model->withTrashed()->select('*');
            } elseif ($bread->soft_deletes == 'only') {
                $query = $model->onlyTrashed()->select('*');
            } elseif ($bread->soft_deletes == 'select') {
                if ($request->softDeletes == 'show') {
                    $query = $model->withTrashed()->select('*');
                } else if ($request->softDeletes == 'only') {
                    $query = $model->onlyTrashed()->select('*');
                }
            }
        }

        $perPage = $request->perPage ?? 10;
        $records = $query->count();
        $this->loadRelationships($query, $layout);
        $this->searchQuery($query, $layout, array_filter((array) $request->filter ?? []), $request->globalSearch);
        $this->orderQuery($query, $request->orderField ?? $layout->getDefaultSortField(), ($request->orderDir ?? 'asc'));
        $filtered = $query->count();
        $query = $query->slice((($request->page ?? 1) - 1) * $perPage)->take($perPage);
        $this->loadAccessors($query, $bread);
        $rows = $query->transform(function ($row) use ($bread) {
            $row['actions'] = Voyager::getActionsForEntry($bread, $row)->toArray();

            return $row;
        })->values();

        return response()->json([
            'records'      => $records,
            'filtered'     => $filtered,
            'rows'         => $rows,
            'primary'      => $model->getKeyName(),
        ]);
    }

    public function create(Request $request)
    {
        $bread = $this->getBread($request);
        $layout = $bread->getLayoutFor('add');
        $this->loadAccessors($data, $bread);
        $data = new \stdClass;
        //$this->authorize('add', app($bread->model));

        return view('voyager::bread.edit-add', compact('bread', 'layout', 'data'));
    }

    public function store(Request $request)
    {
        $bread = $this->getBread($request);
        $layout = $bread->getLayoutFor('add');
        $model = new $bread->model;
        $data = collect(json_decode($request->get('data') ?? '{}'));
        $validator = $this->getValidator($layout, $data);
        if ($validator->fails()) {
            $errors = $validator->errors()->getMessages();

            return view('voyager::bread.edit-add', compact('bread', 'layout', 'data', 'errors'));
        }
        $this->prepareData($data, null, $bread, $layout, 'store');

        $data->each(function ($value, $key) use ($model) {
            $model->{$key} = $value;
        });

        $model->save();

        if ($request->has('_redirect')) {
            if ($request->get('_redirect') == 'back') {
                return redirect($request->get('prev-url'));
            } elseif ($request->get('_redirect') == 'new') {
                return redirect(route('voyager.'.$bread->slug.'.create'));
            }
        }

        return redirect(route('voyager.'.$bread->slug.'.edit', $model->getKey()));
    }

    public function show(Request $request, $id)
    {
        $bread = $this->getBread($request);
        $layout = $bread->getLayoutFor('read');
        // Model-binding
        $id = $id instanceof Model ? $id->getKey() : $id;
        // TODO: Add ->withTrashed()
        $data = $bread->getModel()->findOrFail($id);
        $this->loadAccessors($data, $bread);
        //$this->authorize('read', app($bread->model));

        return view('voyager::bread.read', compact('bread', 'layout', 'data', 'id'));
    }

    public function edit(Request $request, $id)
    {
        $bread = $this->getBread($request);
        $layout = $bread->getLayoutFor('edit');
        // Model-binding
        $id = $id instanceof Model ? $id->getKey() : $id;
        // TODO: Add ->withTrashed()
        $data = $bread->getModel()->findOrFail($id);
        $this->loadAccessors($data, $bread);
        //$this->authorize('browse', app($bread->model));

        return view('voyager::bread.edit-add', compact('bread', 'layout', 'data', 'id'));
    }

    public function update(Request $request, $id)
    {
        $bread = $this->getBread($request);
        $layout = $bread->getLayoutFor('edit');
        $model = $bread->getModel()->findOrFail($id);
        $data = collect(json_decode($request->get('data') ?? '{}'));
        $this->prepareData($data, $model, $bread, $layout, 'update');

        $data->each(function ($value, $key) use ($model) {
            $model->{$key} = $value;
        });

        $model->save();

        if ($request->has('_redirect')) {
            if ($request->get('_redirect') == 'back') {
                return redirect($request->get('prev-url'));
            } elseif ($request->get('_redirect') == 'new') {
                return redirect(route('voyager.'.$bread->slug.'.create'));
            }
        }

        return redirect(route('voyager.'.$bread->slug.'.edit', $id));
    }

    public function destroy(Request $request, $id)
    {
        $bread = $this->getBread($request);
        // TODO: Authorize
        $ids = is_array($request->keys) ? $request->keys : [$id];
        $action = 'delete';
        if ($request->get('restore', false)) {
            $action = 'restore';
        } elseif ($request->get('force', false)) {
            $action = 'force';
        }
        $model = $bread->getModel();

        foreach ($ids as $id) {
            // Validate all IDs exist
            $data = $bread->usesSoftDeletes() ? $model->withTrashed()->find($id): $model->find($id);
            if (!$data) {
                return [
                    'success' => false,
                    'message' => __('voyager::bread.entry_not_found', [
                        'type' => $bread->name_singular,
                        'name' => $model->getKeyName(),
                        'key'  => $id,
                    ]),
                ];
            }

            if ($action == 'delete' || ($action == 'force' && $bread->usesSoftDeletes())) {
                // TODO: Clean up (delete images etc.)

                $success = ($action == 'delete' ? $data->delete() : $data->forceDelete());
                if (!$success) {
                    return [
                        'success' => false,
                        'message' => __('voyager::bread.error_deleting_type', [
                            'type' => $bread->name_singular,
                            'name' => $model->getKeyName(),
                            'key'  => $id,
                        ]),
                    ];
                }
            } elseif ($bread->usesSoftDeletes()) {
                // Restore entry
                if (!$data->restore()) {
                    return [
                        'success' => false,
                        'message' => __('voyager::bread.error_restoring_type', [
                            'type' => $bread->name_singular,
                            'name' => $model->getKeyName(),
                            'key'  => $id,
                        ]),
                    ];
                }
            }
        }

        if (count($ids) == 1) {
            return [
                'success' => true,
                'message' => __('voyager::bread.success_'.($action == 'restore' ? 'restored' : 'deleted').'_type', [
                    'type' => $bread->name_singular,
                ]),
            ];
        }

        return [
            'success' => true,
            'message' => __('voyager::bread.success_'.($action == 'restore' ? 'restored' : 'deleted').'_types', [
                'type' => $bread->name_singular,
                'num'  => count($ids),
            ]),
        ];
    }
}
