<?php

namespace TCG\Voyager\Plugins;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use TCG\Voyager\Plugins\Interfaces\AuthenticationInterface;

class AuthenticationPlugin implements AuthenticationInterface
{
    public function user(): ?object
    {
        return Auth::user();
    }

    public function name(): ?string
    {
        return Auth::user()->name;
    }

    public function guard(): string
    {
        return 'web';
    }

    public function authenticate(Request $request) {
        $credentials = $request->only('email', 'password');
        if (!$credentials['email'] || !$credentials['password']) {
            return redirect()->back()->with([
                'error' => __('voyager::auth.error_field_empty'),
            ])->withInput($credentials);
        }

        // TODO: Throttle attempts
        if (Auth::attempt($credentials, $request->has('remember'))) {
            return redirect()->intended($this->redirectTo());
        }

        return redirect()->back()->with([
            'error' => __('voyager::auth.login_failed'),
        ]);
     }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('voyager.login');
    }

    public function redirectTo()
    {
        return route('voyager.dashboard');
    }

    public function forgotPassword(Request $request)
    {
        // TODO: Throttle attempts
        $email = $request->get('email');
        // TODO: Validate Email, check if it exists, send mail


        return redirect()->back()->with([
            'success' => __('voyager::auth.forgot_password_conf'),
        ]);
    }

    public function handleRequest(Request $request, Closure $next)
    {
        auth()->setDefaultDriver($this->guard());

        if ($this->user() && !Auth::guest()) {
            return $next($request);
        }
    
        return redirect()->guest(route('voyager.login'));
        return $next($request);
    }

    public function loginView(): ?View
    {
        return null; // Return null will show the default form
    }

    public function forgotPasswordView(): ?View
    {
        return view('voyager::auth.forgot_password');
    }

    public function registerProtectedRoutes()
    {
        //
    }

    public function registerPublicRoutes()
    {
        //
    }

    public function getSettingsView(): ?View
    {
        return null;
    }

    public function getInstructionsView(): ?View
    {
        return null;
    }
}
