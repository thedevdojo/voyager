<?php

namespace TCG\Voyager\Http\Middleware;

use Closure;

class VoyagerModelMiddleware
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
        $action = $request->route()->getAction();
        $action_name = 'admin.'.$action['as'];
        $model_permission = \Config::get('voyager.model_permission');
        if ($model_permission($action_name)) {
            return $next($request);
        } else {
            return redirect(route('voyager.dashboard'))->with([
                'message'    => 'Sorry you don\'t have permission to perform this action',
                'alert-type' => 'error',
            ]);
        }

        return redirect(route('voyager.login'));
    }
}
