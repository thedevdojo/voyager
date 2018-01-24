<?php

namespace TCG\Voyager\Http\Middleware;

use Closure;
use TCG\Voyager\Facades\Voyager;

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
        if (!voyager_auth()->guest()) {
            return voyager_auth()->user()->hasPermission('browse_admin') ? $next($request) : redirect('/');
        }

        $urlLogin = route('voyager.login');
        $urlIntended = $request->url();
        if ($urlIntended == $urlLogin) {
            $urlIntended = null;
        }

        return redirect($urlLogin)->with('url.intended', $urlIntended);
    }
}
