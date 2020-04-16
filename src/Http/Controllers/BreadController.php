<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager as VoyagerFacade;

class BreadController extends Controller
{
    public function data(Request $request)
    {
        $bread = $this->getBread($request);
        $layout = $this->getLayoutForAction($bread, 'browse');

        list(
            'page'      => $page,
            'perpage'   => $perpage,
            'query'     => $global,
            'filters'   => $filters,
            'order'     => $order,
            'direction' => $direction,
        ) = $request->all();

        $model = $bread->getModel();

        $query = $model->select('*');

        // Soft-deletes
        $uses_soft_deletes = $bread->usesSoftDeletes();
        if ($uses_soft_deletes) {
            // TODO: This needs to be configurable
            $query = $query->withTrashed();
        }

        $total = $query->count();

        // Global search ($global)
        if (!empty($global)) {
            $query->where(function ($query) use ($global, $layout) {
                $layout->get('formfields')->each(function ($formfield) use (&$query, $global) {
                    if ($formfield->get('searchable')) {
                        $column_type = $formfield->get('column')->get('type');
                        $column = $formfield->get('column')->get('column');
    
                        if ($column_type == 'computed') {
                            // TODO
                        } else if ($column_type == 'relationship') {
                            // TODO
                        } else {
                            $query->orWhere($column, 'LIKE', '%'.$global.'%');
                        }
                    }
                });
            });
        }

        // Field search ($filters)
        foreach (array_filter($filters) as $column => $filter) {
            $column_type = $layout->get('formfields')->where('column.column', $column)->first()->get('column')->get('type');

            if ($column_type == 'computed') {
                // TODO
            } else if ($column_type == 'relationship') {
                // TODO
            } else {
                $query->where($column, 'LIKE', '%'.$filter.'%');
            }
        }

        // Ordering ($order and $direction)
        if (!empty($direction) && !empty($order)) {
            $column_type = $layout->get('formfields')->where('column.column', $order)->first()->get('column')->get('type');

            if ($column_type == 'computed') {
                // TODO
            } else if ($column_type == 'relationship') {
                // TODO
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
        $accessors = $layout->get('formfields')->where('column.type', 'computed')->pluck('column.column')->toArray();
        $query = $query->each(function ($item) use ($accessors) {
            $item->append($accessors);
        });

        // Transform results
        $query = $query->transform(function ($item) use ($uses_soft_deletes) {
            // Add soft-deleted property
            $item->uses_soft_deletes = $uses_soft_deletes;
            $item->is_soft_deleted = false;
            if ($uses_soft_deletes && !empty($item->deleted_at)) {
                $item->is_soft_deleted = $item->trashed();
            }

            return $item;
        });

        return [
            'results'   => $query->values(),
            'filtered'  => $filtered,
            'total'     => $total,
            'layout'    => $layout,
            'primary'   => $query->get(0) ? $query->get(0)->getKeyName() : 'id'
        ];
    }

    public function add(Request $request)
    {
        
    }

    public function store(Request $request)
    {
        
    }

    public function read(Request $request, $id)
    {
        
    }

    public function edit(Request $request, $id)
    {
        
    }

    public function update(Request $request, $id)
    {
        
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
        return $bread->layouts()->where('type', 'list')->first();
    }
}
