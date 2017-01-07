<?php

namespace TCG\Voyager;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\ImageServiceProvider;
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
        $this->app->register(ImageServiceProvider::class);

        $loader = AliasLoader::getInstance();
        $loader->alias('Menu', Menu::class);
        $loader->alias('Voyager', VoyagerFacade::class);

        $this->app->singleton('voyager', function () {
            return new Voyager();
        });

        $this->registerViewComposers();
        $this->registerAlertComponents();

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

        $router->middleware('admin.user', VoyagerAdminMiddleware::class);

        $this->addStorageSymlinkAlert();
    }

    /**
     * Register view composers.
     */
    protected function registerViewComposers()
    {
        // Register alerts
        View::composer('voyager::index', function ($view) {
            $view->with('alerts', VoyagerFacade::alerts());
        });
    }

    /**
     * Add storage symlink alert.
     */
    protected function addStorageSymlinkAlert()
    {
        if (!is_link(public_path('storage-off'))) {
            $alert = (new Alert('missing-storage-symlink', 'warning'))
                ->title('Missing storage symlink')
                ->text('We could not find a storage symlink. This could cause problems with loading media files from the browser.')
                ->button('Fix it', '?fix-missing-storage-symlink');

            VoyagerFacade::addAlert($alert);
        }
    }

    /**
     * Register alert components.
     */
    protected function registerAlertComponents()
    {
        $components = ['title', 'text', 'button'];

        foreach ($components as $component) {
            $class = 'TCG\\Voyager\\Alert\\Components\\'.ucfirst(camel_case($component)).'Component';

            $this->app->bind("voyager.alert.components.{$component}", $class);
        }
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
     * Register the commands accessible from the Console.
     */
    private function registerConsoleCommands()
    {
        $this->commands(Commands\InstallCommand::class);
        $this->commands(Commands\ControllersCommand::class);
        $this->commands(Commands\AdminCommand::class);
    }

    /**
     * Register the commands accessible from the App.
     */
    private function registerAppCommands()
    {
        $this->commands(Commands\MakeModelCommand::class);
    }
}
