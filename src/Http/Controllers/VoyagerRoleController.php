<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;

class VoyagerRoleController extends VoyagerBreadController
{
    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        Voyager::canOrFail('edit_roles');

        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);
        $this->insertUpdateData($request, $slug, $dataType->editRows, $data);

        $data->permissions()->sync($request->input('permissions', []));

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
        Voyager::canOrFail('add_roles');

        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $data = new $dataType->model_name();
        $this->insertUpdateData($request, $slug, $dataType->addRows, $data);

        $data->permissions()->sync($request->input('permissions', []));

        return redirect()
            ->route("voyager.{$dataType->slug}.index")
            ->with([
                'message'    => "Successfully Added New {$dataType->display_name_singular}",
                'alert-type' => 'success',
            ]);
    }
}
