<?php

namespace TCG\Voyager\Http\Middleware;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;

class VoyagerCheckForMaintenanceMode extends CheckForMaintenanceMode
{
    protected $whitelist = [
        'voyager.dashboard',
        'voyager.compass.index',
        'voyager.compass.post',
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
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if ($this->app->isDownForMaintenance()) {
            if (!in_array($request->route()->getName(), $this->whitelist)) {
                return parent::handle($request, $next);
            }
        }

        return $response;
    }
}
