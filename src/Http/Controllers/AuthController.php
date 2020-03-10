<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login()
    {
        if ($this->getAuthenticationPlugin()->user()) {
            return redirect($this->getAuthenticationPlugin()->redirectTo());
        }

        return view('voyager::auth.login');
    }

    public function processLogin(Request $request)
    {
        return $this->getAuthenticationPlugin()->authenticate($request);
    }

    public function logout()
    {
        return $this->getAuthenticationPlugin()->logout();
    }

    public function forgotPassword(Request $request)
    {
        return $this->getAuthenticationPlugin()->forgotPassword($request);
    }
}
