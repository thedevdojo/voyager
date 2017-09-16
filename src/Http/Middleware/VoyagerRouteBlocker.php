<?php

namespace TCG\Voyager\Http\Middleware;

use Closure;
use TCG\Voyager\Facades\Voyager;

class VoyagerRouteBlocker
{
    public function handle($request, Closure $next)
    {
        $enable = config('voyager.blocker.enable');

        if ($enable) {
            $allow = config('voyager.blocker.ip_white_list');

            $ip    = $request->getClientIp();

            $allowed = explode(',', $allow);

            if (in_array($ip, $allowed)) {
                return $next($request);
            }

            return abort(404);
        }

        return $next($request);
    }
}
