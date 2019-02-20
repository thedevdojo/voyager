<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        if (app('VoyagerUser')->getKey() == $id) {
            $request->merge([
                'role_id'                              => app('VoyagerUser')->role_id,
                'user_belongstomany_role_relationship' => app('VoyagerUser')->roles->pluck('id')->toArray(),
            ]);
        }

        return parent::update($request, $id);
    }
}
