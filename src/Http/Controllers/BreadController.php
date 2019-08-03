<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use TCG\Voyager\Facades\Voyager;

class BreadController extends Controller
{
    public function index(Request $request)
    {
        $bread = $this->getBread($request);
        $layout = $bread->getLayoutFor('browse');
        //$this->authorize('browse', app($bread->model_name));

        if ($request->ajax()) {
            if (Session::token() != $request->get('_token')) {
                throw new \Illuminate\Session\TokenMismatchException;
            }
            $records = 0;
            $rows = [];
            $model = $bread->getModel();
            $query = $model->select('*');
            $perPage = $request->perPage ?? 10;
            $records = $query->count();
            $this->searchQuery($query, array_filter((array) json_decode($request->filter) ?? []));
            $this->orderQuery($query, $request->orderField, ($request->orderDir ?? 'asc'));

            $query = $query->slice((($request->page ?? 1) - 1) * $perPage)->take($perPage);
            $rows = $query->values()->toArray();

            return response()->json([
                'records' => $records,
                'rows'    => $rows,
                'primary' => $model->getKeyName(),
            ]);
        }
        $actions = Voyager::getActionsForBread($bread);

        return view('voyager::bread.browse', compact('bread', 'layout', 'actions'));
    }

    public function create(Request $request)
    {
        $bread = $this->getBread($request);
        // TODO: Authorize
    }

    public function store(Request $request)
    {
        $bread = $this->getBread($request);
        // TODO: Authorize
    }

    public function show(Request $request, $id)
    {
        $bread = $this->getBread($request);
        // TODO: Authorize
    }

    public function edit(Request $request, $id)
    {
        $bread = $this->getBread($request);
        // TODO: Authorize
    }

    public function update(Request $request, $id)
    {
        $bread = $this->getBread($request);
        // TODO: Authorize
    }

    public function destroy(Request $request, $id)
    {
        $bread = $this->getBread($request);
        // TODO: Authorize
    }
}
