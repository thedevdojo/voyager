<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login()
    {
        return view('voyager::auth.login');
    }

    public function processLogin(Request $request)
    {
    }

    public function logout()
    {
    }
}
