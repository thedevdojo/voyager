<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Facades\Voyager;

class VoyagerProfileController extends Controller
{
    public function index()
    {
        return Voyager::view('voyager::profile');
    }

     //***************************************
    //                ______
    //               |  ____|
    //               | |__
    //               |  __|
    //               | |____
    //               |______|
    //
    //  Edit an item of our Data Type BR(E)AD
    //
    //****************************************

    public function edit(Request $request)
    {
        $slug = 'users';

        $id = $request->user()->id;

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $dataTypeContent = (strlen($dataType->model_name) != 0)
            ? app($dataType->model_name)->findOrFail($id)
            : DB::table($dataType->name)->where('id', $id)->first(); // If Model doest exist, get data from table name

        foreach ($dataType->editRows as $key => $row) {
            $details = json_decode($row->details);
            $dataType->editRows[$key]['col_width'] = isset($details->width) ? $details->width : 100;
        }

        // Check permission
        $this->authorize('edit', $dataTypeContent);

        $view = "voyager::{$slug}.edit-add";

        if (view()->exists("voyager::profile.edit-add")) {
            $view = "voyager::profile.edit-add";
        }

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable'));
    }

    // POST BR(E)AD
    public function update(Request $request)
    {
        $slug = 'users';

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $id = $request->user()->id;

        $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);

        // Check permission
        $this->authorize('edit', $data);

        //Remove from Request the Guarded Attributes
        foreach ($data->profileGuarded as $guarded) {
            $request->offsetUnset($guarded);
        }

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->editRows, $slug, $id);

        if ($val->fails()) {
            return response()->json(['errors' => $val->messages()]);
        }

        if (!$request->ajax()) {
            $this->insertUpdateData($request, $slug, $dataType->editRows, $data);

            event(new BreadDataUpdated($dataType, $data));

            return redirect()
                ->route('voyager.profile')
                ->with([
                    'message'    => __('voyager::generic.successfully_updated')." {$dataType->display_name_singular}",
                    'alert-type' => 'success',
                ]);
        }
    }
}
