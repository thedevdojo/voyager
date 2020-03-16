<?php

namespace TCG\Voyager\Plugins\Interfaces;

use Closure;
use Illuminate\Http\Request;
use Illuminate\View\View;

interface AuthenticationInterface extends GenericInterface
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
