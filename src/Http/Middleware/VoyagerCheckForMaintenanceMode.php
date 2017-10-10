<?php

namespace TCG\Voyager\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;

class VoyagerCheckForMaintenanceMode
{
    
    protected $whitelist = [
        "voyager.dashboard",
        "voyager.compass.index",
        "voyager.compass.post"
    ];
    /**
     * The application implementation.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function handle($request, Closure $next)
    {

        
        if ($this->app->isDownForMaintenance()) {
            
            if(!in_array($request->route()->getName(), $this->whitelist)){
                $data = json_decode(file_get_contents($this->app->storagePath().'/framework/down'), true);

                throw new MaintenanceModeException($data['time'], $data['retry'], $data['message']);
            }

        }

        return $next($request);
    }
}
