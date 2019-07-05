<?php

namespace TCG\Voyager;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use TCG\Voyager\Facades\Voyager as VoyagerFacade;

class VoyagerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param \Illuminate\Routing\Router $router
     */
    public function boot(Router $router, Dispatcher $event)
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'voyager');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $loader = AliasLoader::getInstance();

        $loader->alias('Voyager', VoyagerFacade::class);
        $this->app->singleton('voyager', function () {
            return new Voyager();
        });
    }
}
