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

    // POST BR(E)AD
    public function update(Request $request)
    {
        $slug = 'users';

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $id = $request->user()->id;

        $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);

        // Check permission
        $this->authorize('edit', $data);

        //Verify if user tries to modify his own role
        if ($request->has('role_id')) {
            $request->merge(['role_id' => $request->user()->role->id]);
        }

        if($request->has('user_belongstomany_role_relationship')){
            $request->offsetUnset('user_belongstomany_role_relationship');
        }

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->editRows, $slug, $id);

        if ($val->fails()) {
            return response()->json(['errors' => $val->messages()]);
        }

        if (!$request->ajax()) {
            $this->insertUpdateData($request, $slug, $dataType->editRows, $data);

            event(new BreadDataUpdated($dataType, $data));
            
            $route = $request->user()->can('browse', $data) ? 'voyager.users.index' : 'voyager.profile';

            return redirect()
                ->route($route)
                ->with([
                    'message'    => __('voyager::generic.successfully_updated')." {$dataType->display_name_singular}",
                    'alert-type' => 'success',
                ]);
        }
    }
}
