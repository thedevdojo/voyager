<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Foundation\Auth\ResetsPasswords;

class VoyagerResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    public function __construct()
    {
        $this->middleware('guest');
    }
}