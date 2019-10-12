<?php

namespace TCG\Voyager;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use TCG\Voyager\Commands\InstallCommand;
use TCG\Voyager\Facades\Voyager as VoyagerFacade;
use TCG\Voyager\Http\Middleware\VoyagerAdminMiddleware;
use TCG\Voyager\Policies\BasePolicy;

class VoyagerServiceProvider extends ServiceProvider
{
    protected $policies = [];

    /**
     * Bootstrap the application services.
     *
     * @param \Illuminate\Routing\Router $router
     */
    public function boot(Router $router, Dispatcher $event)
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'voyager');
        $this->loadTranslationsFrom(realpath(__DIR__.'/../resources/lang'), 'voyager');

        VoyagerFacade::addFormfield(\TCG\Voyager\Formfields\Color::class);
        VoyagerFacade::addFormfield(\TCG\Voyager\Formfields\DateTime::class);
        VoyagerFacade::addFormfield(\TCG\Voyager\Formfields\Number::class);
        VoyagerFacade::addFormfield(\TCG\Voyager\Formfields\Password::class);
        VoyagerFacade::addFormfield(\TCG\Voyager\Formfields\Text::class);

        VoyagerFacade::addAction(\TCG\Voyager\Actions\ReadAction::class);
        VoyagerFacade::addAction(\TCG\Voyager\Actions\EditAction::class);
        VoyagerFacade::addAction(\TCG\Voyager\Actions\DeleteAction::class);
        VoyagerFacade::addAction(\TCG\Voyager\Actions\RestoreAction::class);
        VoyagerFacade::addAction(\TCG\Voyager\Actions\ForceDeleteAction::class);
        VoyagerFacade::addAction(\TCG\Voyager\Actions\BulkDeleteAction::class);
        VoyagerFacade::addAction(\TCG\Voyager\Actions\BulkRestoreAction::class);

        // Register Policies
        VoyagerFacade::getBreads()->each(function ($bread) {
            $policy = BasePolicy::class;

            if (!empty($bread->policy) && class_exists($bread->policy)) {
                $policy = $bread->policy;
            }

            $this->policies[$bread->model.'::class'] = $policy;
        });
        $this->registerPolicies();

        $router->aliasMiddleware('voyager.admin', VoyagerAdminMiddleware::class);
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

        $this->loadHelpers();
        $this->loadBreadsFrom(storage_path('bread'));

        $this->commands(InstallCommand::class);
    }

    public function loadBreadsFrom($path)
    {
        VoyagerFacade::breadPath($path);
    }

    /**
     * Load helpers.
     */
    protected function loadHelpers()
    {
        foreach (glob(__DIR__.'/Helpers/*.php') as $filename) {
            require_once $filename;
        }
    }
}
