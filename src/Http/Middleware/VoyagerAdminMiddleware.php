<?php

namespace TCG\Voyager\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Models\User;

class VoyagerAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::guest()) {
            return redirect()->route('voyager.login');
        }

        /**
         * Get the Voyager User Object.
         *
         * @var \TCG\Voyager\Models\User
         */
        $user = User::find(Auth::id());

        return $user->hasRole('admin') ? $next($request) : redirect('/');
    }
}
