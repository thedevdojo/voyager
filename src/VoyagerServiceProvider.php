<?php

namespace TCG\Voyager;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Router;
use Illuminate\Foundation\AliasLoader;
use TCG\Voyager\Models\User;
use TCG\Voyager\Middleware\VoyagerAdminMiddleware;

class VoyagerServiceProvider extends ServiceProvider
{

    private $routeNamespace = 'TCG\\Voyager\\Controllers';

    /**
     * Bootstrap the application services.
     *
     * @param Router $router
     */
    public function boot(Router $router)
    {
        $router->middleware('admin.user', VoyagerAdminMiddleware::class);

        if (config('voyager.user.add_default_role_on_register')) {
            $app_user = config('voyager.user.namespace');
            $app_user::created(function ($user) {
                $voyager_user = User::find($user->id);
                $voyager_user->addRole(config('voyager.user.default_role'));
            });
        }

        $this->loadViewsFrom(__DIR__ . '/views', 'voyager');

        $router->group(['prefix' => config('voyager.routes.prefix', 'admin'), 'namespace' => $this->routeNamespace],
            function () {
                if (!$this->app->routesAreCached()) {
                    require __DIR__ . '/routes.php';
                }
            });

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\Intervention\Image\ImageServiceProvider::class);
        $this->registerResources();

        /*
         * These calls are not needed.
         * All Laravel-based classes (ie: Models and Controllers) are bootstrapped by default
         * Remove these in the next iteration
         * */

        /*$this->app->make('TCG\Voyager\Models\User');
        $this->app->make('TCG\Voyager\Models\Role');
        $this->app->make('TCG\Voyager\Models\DataType');
        $this->app->make('TCG\Voyager\Models\DataRow');

        $this->app->make('TCG\Voyager\Controllers\VoyagerController');
        $this->app->make('TCG\Voyager\Controllers\VoyagerMediaController');
        $this->app->make('TCG\Voyager\Controllers\VoyagerBreadController');
        $this->app->make('TCG\Voyager\Controllers\VoyagerSettingsController');

        $this->app->make('TCG\Voyager\Controllers\VoyagerAuthController');
        $this->app->make('TCG\Voyager\Controllers\VoyagerDatabaseController');*/

        $this->app->booting(function () {
            $loader = AliasLoader::getInstance();
            $loader->alias('Menu', 'TCG\Voyager\Models\Menu');
            $loader->alias('Voyager', 'TCG\Voyager\Voyager');
        });

        $this->app['command.voyager'] = $this->app->share(function ($app) {
            return new VoyagerCommand;
        });
        $this->commands('command.voyager');
    }

    protected function registerResources()
    {

        // Publish the assets to the Public folder
        $this->publishes([
            __DIR__ . '/../publishable/assets' => public_path('vendor/tcg/voyager/assets'),
        ], 'voyager_assets');

        // Publish the migrations to the migrations folder
        $this->publishes([
            __DIR__ . '/../publishable/database/migrations/' => database_path('migrations')
        ], 'migrations');

        // Publish the seeds to the seeds folder
        $this->publishes([
            __DIR__ . '/../publishable/database/seeds/' => database_path('seeds')
        ], 'seeds');

        // Publish the content/uploads content to the migrations folder
        $this->publishes([
            __DIR__ . '/../publishable/demo_content/' => storage_path('app/public')
        ], 'demo_content');

        // Publish the content/uploads content to the migrations folder
        $this->publishes([
            __DIR__ . '/../publishable/config/voyager.php' => config_path('voyager.php')
        ], 'config');

        // Publish the post view
        $this->publishes([
            __DIR__ . '/../publishable/views/' => resource_path('views')
        ], 'views');
    }

}
