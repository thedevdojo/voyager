<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jocic\GoogleAuthenticator\Account;
use Jocic\GoogleAuthenticator\Secret;
use Jocic\GoogleAuthenticator\Qr\Remote\GoogleQr;
use TCG\Voyager\Facades\Voyager;

class VoyagerUserController extends VoyagerBaseController
{
    public function profile(Request $request)
    {
        $mfa = '';
        $route = '';
        $dataType = Voyager::model('DataType')->where('model_name', Auth::guard(app('VoyagerGuard'))->getProvider()->getModel())->first();
        
        if (isset(Auth::user()->mfa)) {
            $mfa = (new GoogleQr(200))->getUrl(new Account(Voyager::setting('admin.title'),
                Auth::user()->name, Auth::user()->mfa->secret));
        }

        if (!$dataType && app('VoyagerGuard') == 'web') {
            $route = route('voyager.users.edit', Auth::user()->getKey());
        } elseif ($dataType) {
            $route = route('voyager.'.$dataType->slug.'.edit', Auth::user()->getKey());
        }

        return Voyager::view('voyager::profile', compact('route', 'mfa'));
    }

    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        $model = Auth::guard(app('VoyagerGuard'))->getProvider()->getModel();
        $user = call_user_func($model.'::find', $id);
        $mfa  = null;

        if (Auth::user()->getKey() == $id) {
            $request->merge([
                'role_id'                              => Auth::user()->role_id,
                'user_belongstomany_role_relationship' => Auth::user()->roles->pluck('id')->toArray(),
            ]);
        }

        if ($request->input('mfa') != null) {
            $mfa = [
                'type' => 'google',
                'secret' => isset($user->mfa->secret) ?
                    $user->mfa->secret : (new Secret())->getValue()
            ];
        }
        $request->merge(['mfa' => $mfa]);

        return parent::update($request, $id);
    }
}
