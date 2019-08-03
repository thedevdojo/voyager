<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;

class BreadController extends Controller
{
    public function index(Request $request)
    {
        $bread = $this->getBread($request);
        $layout = $bread->getLayoutFor('browse');
        //$this->authorize('browse', app($bread->model_name));

        if ($request->ajax()) {
            
        }

        // TODO: Add Actions
        return view('voyager::bread.browse', compact('bread', 'layout'));
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
