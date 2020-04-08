<?php

namespace TCG\Voyager\Contracts\Plugins;

use Closure;
use Illuminate\Http\Request;
use Illuminate\View\View;

interface IsAuthenticationPlugin extends IsGenericPlugin
{
    public function user(): ?object;

    public function name(): ?string;

    public function guard(): string;

    public function authenticate(Request $request);

    public function logout();

    public function redirectTo();

    public function forgotPassword(Request $request);

    public function handleRequest(Request $request, Closure $next);

    public function loginView(): ?View;

    public function forgotPasswordView(): ?View;
}
