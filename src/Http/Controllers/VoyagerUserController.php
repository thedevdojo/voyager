<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth
use TCG\Voyager\Facades\Voyager;

class VoyagerUserController extends VoyagerBaseController
{

    public function profile(Request $request)
    {
        return Voyager::view('voyager::profile');
    }

    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('edit', Voyager::model('User')) && $id == Auth::user()->id) {
            // If can't edit users can still edit its own profile but can't change roles
            // we need to preserve roles already set
            $roles = Auth::user()->roles->pluck('id')->toArray();

            $params = $request->all();
            $params['role_id'] = Auth::user()->role_id;
            $params['user_belongstomany_role_relationship'] = $roles;

            $request->replace($params);
        }
        return parent::update($request, $id);
    }

}
