<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Role;

class VoyagerUserController extends VoyagerBreadController
{
    public function edit(Request $request, $id)
    {
        $slug = $request->segment(2);
        $dataType = DataType::where('slug', '=', $slug)->first();
        $dataTypeContent = (strlen($dataType->model_name) != 0)
            ? call_user_func([$dataType->model_name, 'find'], $id)
            : DB::table($dataType->name)->where('id', $id)->first(); // If Model doest exist, get data from table name

        $roles = Role::all();

        $view = 'voyager::bread.edit-add';

        if (view()->exists("admin.$slug.edit-add")) {
            $view = "admin.$slug.edit-add";
        } elseif (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        return view($view, compact('dataType', 'dataTypeContent', 'roles'));
    }

    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        $slug = $request->segment(2);
        $dataType = DataType::where('slug', '=', $slug)->first();
        $data = call_user_func([$dataType->model_name, 'find'], $id);
        $this->insertUpdateData($request, $slug, $dataType->editRows, $data);


        return redirect()
            ->route("{$dataType->slug}.index")
            ->with([
                'message'    => "Successfully Updated {$dataType->display_name_singular}",
                'alert-type' => 'success',
            ]);
    }

    public function create(Request $request)
    {
        $slug = $request->segment(2);
        $dataType = DataType::where('slug', '=', $slug)->first();

        $roles = Role::all();

        $view = 'voyager::bread.edit-add';

        if (view()->exists("admin.$slug.edit-add")) {
            $view = "admin.$slug.edit-add";
        } elseif (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        return view($view, compact('dataType', 'roles'));
    }

    // POST BRE(A)D
    public function store(Request $request)
    {
        $slug = $request->segment(2);
        $dataType = DataType::where('slug', '=', $slug)->first();

        if (function_exists('voyager_add_post')) {
            $url = $request->url();
            voyager_add_post($request);
        }

        $data = new $dataType->model_name();
        $this->insertUpdateData($request, $slug, $dataType->addRows, $data);

        return redirect()
            ->route("{$dataType->slug}.index")
            ->with([
                'message'    => "Successfully Added New {$dataType->display_name_singular}",
                'alert-type' => 'success',
            ]);
    }
}