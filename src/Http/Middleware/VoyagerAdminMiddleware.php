<?php

namespace TCG\Voyager\Http\Middleware;

use Closure;

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
        if (!app('VoyagerAuth')->guest()) {

            // Set default guard to voyager admin
            if (!empty($guard = config('voyager.guard'))) {
                auth()->setDefaultDriver($guard);
            } elseif (app('VoyagerAuth') instanceof \Illuminate\Auth\SessionGuard) {
                // Extract guard name from unique identifier for the auth session value
                preg_match('/(?<=_).*(?=_)/', app('VoyagerAuth')->getName(), $matches);
                auth()->setDefaultDriver($matches[0]);
            }

            $user = app('VoyagerAuth')->user();
            app()->setLocale($user->locale ?? app()->getLocale());

            return $user->hasPermission('browse_admin') ? $next($request) : redirect('/');
        }

        $urlLogin = route('voyager.login');

        return redirect()->guest($urlLogin);
    }
}
