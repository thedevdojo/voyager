<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\Role;

class VoyagerUserController extends VoyagerBreadController
{
    public function edit(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        Voyager::canOrFail('edit_'.$dataType->name);

        $relationships = $this->getRelationships($dataType);

        $dataTypeContent = (strlen($dataType->model_name) != 0)
            ? app($dataType->model_name)->with($relationships)->findOrFail($id)
            : DB::table($dataType->name)->where('id', $id)->first(); // If Model doest exist, get data from table name

        if (Auth::user()->role_id == Role::where('name', '=', 'user')->first()->id && $dataTypeContent->role_id == Role::where('name', '=', 'admin')->first()->id) {
            return redirect()
                ->route('voyager.users.index')
                ->with($this->alertError("You cannot edit administrator user."));
        }

        // Check if BREAD is Translatable
        $isModelTranslatable = isBreadTranslatable($dataTypeContent);

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        return view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable'));
    }
    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        Voyager::canOrFail('edit_users');

        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);

        if (Auth::user()->role_id == Role::where('name', '=', 'user')->first()->id && $data->role_id == Role::where('name', '=', 'admin')->first()->id) {
            return redirect()
                ->route('voyager.users.index')
                ->with($this->alertError("You cannot edit administrator user."));
        }

        if (Auth::user()->role_id == Role::where('name', '=', 'user')->first()->id && empty($data->role_id)) {
            $data->role_id = Role::where('name', '=', 'user')->first()->id;
        }

        $this->insertUpdateData($request, $slug, $dataType->editRows, $data);

        return redirect()
            ->route("voyager.{$dataType->slug}.index")
            ->with([
                'message'    => "Successfully Updated {$dataType->display_name_singular}",
                'alert-type' => 'success',
            ]);
    }

    // POST BRE(A)D
    public function store(Request $request)
    {
        Voyager::canOrFail('add_users');

        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $data = new $dataType->model_name();

        if (Auth::user()->role_id == Role::where('name', '=', 'user')->first()->id && empty($data->role_id)) {
            $data->role_id = Role::where('name', '=', 'user')->first()->id;
        }

        $data = $this->insertUpdateData($request, $slug, $dataType->addRows, $data);

        return redirect()
            ->route("voyager.{$dataType->slug}.index")
            ->with([
                'message'    => "Successfully Added New {$dataType->display_name_singular}",
                'alert-type' => 'success',
            ]);
    }
}
