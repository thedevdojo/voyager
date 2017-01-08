<?php

namespace TCG\Voyager;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\ImageServiceProvider;
use TCG\Voyager\Facades\Voyager as VoyagerFacade;
use TCG\Voyager\Http\Middleware\VoyagerAdminMiddleware;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\User;

class VoyagerServiceProvider extends ServiceProvider
{
    /**
     * List of Voyager publishable resources.
     *
     * @return array
     */
    public static function publishableResources($group = null) {
        $publishablePath = dirname(__DIR__) . '/publishable';

        $publishable = [
            'voyager_assets' => [
                "$publishablePath/assets" => public_path('vendor/tcg/voyager/assets'),
            ],
            'migrations' => [
                "$publishablePath/database/migrations/" => database_path('migrations/voyager'),
            ],
            'seeds' => [
                "$publishablePath/database/seeds/" => database_path('seeds/voyager'),
            ],
            'demo_content' => [
                "$publishablePath/demo_content/" => storage_path('app/public'),
            ],
            'config' => [
                "$publishablePath/config/voyager.php" => config_path('voyager.php'),
            ],
            'views' => [
                "$publishablePath/views/" => resource_path('views/vendor/voyager'),
            ],
        ];

        if( is_null($group) ) {
            return $publishable;
        }

        return $publishable[$group];
    }

    /**
     * The paths of published resources.
     *   (if resource group has many paths, only the first one is returned).
     *
     * @param mixed $groups Resource groups
     *
     * @return string | array
     */
    public static function publishedPaths($groups) {
        $publishable = static::publishableResources();
        
        if( is_array($groups) ) {
            $paths = [];

            foreach ($groups as $group) {
                $paths[$group] = current($publishable[$group]);
            }

            return $paths;
        }

        return current($publishable[$groups]);
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->register(ImageServiceProvider::class);

        $loader = AliasLoader::getInstance();
        $loader->alias('Menu', Menu::class);
        $loader->alias('Voyager', VoyagerFacade::class);

        $this->app->singleton('voyager', function () {
            return new Voyager();
        });

        if ($this->app->runningInConsole()) {
            $this->registerPublishableResources();
            $this->registerConsoleCommands();
        } else {
            $this->registerAppCommands();
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
                if (is_null($user->role_id)) {
                    User::findOrFail($user->id)
                        ->setRole(config('voyager.user.default_role'))
                        ->save();
                }
            });
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'voyager');

        $this->loadMigrationsFrom(static::publishedPaths('migrations'));

        $router->middleware('admin.user', VoyagerAdminMiddleware::class);
    }

    /**
     * Register the publishable files.
     */
    private function registerPublishableResources()
    {
        foreach (static::publishableResources() as $group => $paths) {
            $this->publishes($paths, $group);
        }
    }

    /**
     * Register the commands accessible from the Console.
     */
    private function registerConsoleCommands()
    {
        $this->commands(Commands\ControllersCommand::class);
        $this->commands(Commands\AdminCommand::class);

        $this->commands(Commands\Installation\InstallCommand::class);
        $this->commands(Commands\Installation\UninstallCommand::class);
    }

    /**
     * Register the commands accessible from the App.
     */
    private function registerAppCommands()
    {
        $this->commands(Commands\MakeModelCommand::class);
    }
}
