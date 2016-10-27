<?php

namespace TCG\Voyager\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Models\User;

class VoyagerAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!Auth::guest()){ // || (!Auth::guest() && empty(Auth::user()->authx_id)) ) {
            // Get the Voyager User Object
            $user = User::find(Auth::user()->id);
            if($user->hasRole('admin')){
                return $next($request);
            } else {
                return redirect('/');
            }
        }
        return redirect('/admin/login'); 
        
    }
}
