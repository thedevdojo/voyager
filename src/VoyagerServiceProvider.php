<?php

namespace TCG\Voyager;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use TCG\Voyager\Facades\Voyager as VoyagerFacade;
use TCG\Voyager\Http\Middleware\VoyagerAdminMiddleware;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\User;

class VoyagerServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->register(\Intervention\Image\ImageServiceProvider::class);

        $loader = AliasLoader::getInstance();
        $loader->alias('Menu', Menu::class);
        $loader->alias('Voyager', VoyagerFacade::class);

        $this->app->singleton('voyager', function () {
            return Voyager::getInstance();
        });

        if ($this->app->runningInConsole()) {
            $this->registerPublishableResources();
            $this->registerCommands();
        }
    }

    /**
     * Bootstrap the application services.
     *
     * @param \Illuminate\Routing\Router $router
     */
    public function boot(Router $router)
    {
        if (config('voyager.user.add_default_role_on_register')) {
            $app_user = config('voyager.user.namespace');
            $app_user::created(function ($user) {
                $voyager_user = User::find($user->id);
                $voyager_user->setRole(config('voyager.user.default_role'));
            });
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'voyager');

        $router->middleware('admin.user', VoyagerAdminMiddleware::class);
    }

    /**
     * Register the publishable files.
     */
    private function registerPublishableResources()
    {
        $basePath = dirname(__DIR__);
        $publishable = [
            'voyager_assets' => [
                "$basePath/publishable/assets" => public_path('vendor/tcg/voyager/assets'),
            ],
            'migrations' => [
                "$basePath/publishable/database/migrations/" => database_path('migrations'),
            ],
            'seeds' => [
                "$basePath/publishable/database/seeds/" => database_path('seeds'),
            ],
            'demo_content' => [
                "$basePath/publishable/demo_content/" => storage_path('app/public'),
            ],
            'config' => [
                "$basePath/publishable/config/voyager.php" => config_path('voyager.php'),
            ],
            'views' => [
                "$basePath/publishable/views/" => resource_path('views/vendor/voyager'),
            ],
        ];

        foreach ($publishable as $group => $paths) {
            $this->publishes($paths, $group);
        }
    }

    /**
     * Register the console commands.
     */
    private function registerCommands()
    {
        $this->commands(Commands\InstallCommand::class);
        $this->commands(Commands\ControllersCommand::class);
        $this->commands(Commands\AdminCommand::class);
    }
}
