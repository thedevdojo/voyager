<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Foundation\Auth\ConfirmsPasswords;

class VoyagerPasswordConfirmController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Confirm Password Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password confirmations and
    | uses a simple trait to include the behavior. You're free to explore
    | this trait and override any functions that require customization.
    |
    */

    use ConfirmsPasswords;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /*
     * Where to redirect users when the intended url fails.
     */
    public function redirectTo()
    {
        return route('voyager.login');
    }

    /**
     * Display the password confirmation view.
     *
     * @return \Illuminate\View\View
     */
    public function showConfirmForm()
    {
        return view('voyager::auth.passwords.confirm');
    }
}
