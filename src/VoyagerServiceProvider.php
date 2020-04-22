<?php

namespace TCG\Voyager;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use TCG\Voyager\Commands\InstallCommand;
use TCG\Voyager\Facades\Bread as BreadFacade;
use TCG\Voyager\Facades\Plugins as PluginsFacade;
use TCG\Voyager\Facades\Settings as SettingsFacade;
use TCG\Voyager\Facades\Voyager as VoyagerFacade;
use TCG\Voyager\Http\Middleware\VoyagerAdminMiddleware;
use TCG\Voyager\Plugins\AuthenticationPlugin;
use TCG\Voyager\Policies\BasePolicy;

class VoyagerServiceProvider extends ServiceProvider
{
    protected $policies = [];

    /**
     * Bootstrap the application services.
     *
     * @param \Illuminate\Routing\Router $router
     */
    public function boot(Router $router)
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'voyager');
        $this->loadTranslationsFrom(realpath(__DIR__.'/../resources/lang'), 'voyager');

        BreadFacade::addFormfield(\TCG\Voyager\Formfields\Number::class);
        //BreadFacade::addFormfield(\TCG\Voyager\Formfields\Repeater::class);
        BreadFacade::addFormfield(\TCG\Voyager\Formfields\Text::class);

        // Register Policies
        BreadFacade::getBreads()->each(function ($bread) {
            $policy = BasePolicy::class;

            if (!empty($bread->policy) && class_exists($bread->policy)) {
                $policy = $bread->policy;
            }

            $this->policies[$bread->model.'::class'] = $policy;
        });
        $this->registerPolicies();

        $router->aliasMiddleware('voyager.admin', VoyagerAdminMiddleware::class);

        View::share('authentication', PluginsFacade::getPluginByType('authentication', AuthenticationPlugin::class));
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $loader = AliasLoader::getInstance();

        $loader->alias('Bread', BreadFacade::class);
        $loader->alias('VoyagerPlugins', PluginsFacade::class);
        $loader->alias('VoyagerSettings', SettingsFacade::class);
        $loader->alias('Voyager', VoyagerFacade::class);

        $this->app->singleton('bread', function () {
            return new Bread();
        });
        $this->app->singleton('plugins', function () {
            return new Plugins();
        });
        $this->app->singleton('settings', function () {
            return new Settings();
        });
        $this->app->singleton('voyager', function () {
            return new Voyager();
        });

        $this->loadBreadsFrom(storage_path('voyager/breads'));
        $this->loadSettingsFrom(Str::finish(storage_path('voyager'), '/').'settings.json');
        $this->loadPluginsFrom(Str::finish(storage_path('voyager'), '/').'plugins.json');

        $this->commands(InstallCommand::class);
    }

    public function loadBreadsFrom($path)
    {
        BreadFacade::breadPath($path);
    }

    public function loadSettingsFrom($path)
    {
        SettingsFacade::settingsPath($path);
    }

    public function loadPluginsFrom($path)
    {
        PluginsFacade::pluginsPath($path);
    }
}
