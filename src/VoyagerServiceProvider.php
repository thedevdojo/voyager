<?php

namespace TCG\Voyager;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class VoyagerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(\Illuminate\Routing\Router $router, \Illuminate\Contracts\Http\Kernel $kernel)
    {

        if( config('voyager.user.add_default_role_on_register') ){
            $app_user = config('voyager.user.namespace');
            $app_user::created(function ($user) {
                $voyager_user = \TCG\Voyager\Models\User::find($user->id);
                $voyager_user->addRole( config('voyager.user.default_role') );
            });
        }

        $router->middleware('admin.user', 'TCG\Voyager\Middleware\VoyagerAdminMiddleware');

        $this->loadViewsFrom(__DIR__.'/views', 'voyager');

        // Publish the assets to the Public folder
        $this->publishes([
            __DIR__.'/../assets' => public_path('vendor/tcg/voyager/assets'),
        ], 'public');

        // Publish the migrations to the migrations folder
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'public');

        // Publish the seeds to the seeds folder
        $this->publishes([
            __DIR__.'/../database/seeds/' => database_path('seeds')
        ], 'public');

        // Publish the content/uploads content to the migrations folder
        $this->publishes([
            __DIR__.'/../demo_content/' => storage_path('app/public')
        ], 'public');

        // Publish the content/uploads content to the migrations folder
        $this->publishes([
            __DIR__.'/../config/voyager.php' => config_path('voyager.php')
        ], 'public');

        //include __DIR__.'/Traits/VoyagerUser.php';
        include __DIR__.'/routes.php';
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->make('TCG\Voyager\Models\User');
        $this->app->make('TCG\Voyager\Models\Role');
        $this->app->make('TCG\Voyager\Models\DataType');
        $this->app->make('TCG\Voyager\Models\DataRow');

        $this->app->make('TCG\Voyager\Controllers\VoyagerController');
        $this->app->make('TCG\Voyager\Controllers\VoyagerMediaController');
        $this->app->make('TCG\Voyager\Controllers\VoyagerBreadController');
        $this->app->make('TCG\Voyager\Controllers\VoyagerSettingsController');

        $this->app->make('TCG\Voyager\Controllers\VoyagerAuthController');
        $this->app->make('TCG\Voyager\Controllers\VoyagerDatabaseController');

       // $this->app->make('TCG\Voyager\Traits\VoyagerUser');
    }

}
