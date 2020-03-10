<?php

namespace TCG\Voyager\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager as VoyagerFacade;
use TCG\Voyager\Plugins\AuthenticationPlugin;

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
    public function handle(Request $request, Closure $next)
    {
        return VoyagerFacade::getPluginByType('authentication', AuthenticationPlugin::class)->handleRequest($request, $next);
    }
}
