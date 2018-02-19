<?php

namespace TCG\Voyager;

use Arrilot\Widgets\Facade as Widget;
use Arrilot\Widgets\ServiceProvider as WidgetServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Intervention\Image\ImageServiceProvider;
use Larapack\DoctrineSupport\DoctrineSupportServiceProvider;
use Larapack\VoyagerHooks\VoyagerHooksServiceProvider;
use TCG\Voyager\Events\FormFieldsRegistered;
use TCG\Voyager\Facades\Voyager as VoyagerFacade;
use TCG\Voyager\FormFields\After\DescriptionHandler;
use TCG\Voyager\Http\Middleware\VoyagerAdminMiddleware;
use TCG\Voyager\Models\MenuItem;
use TCG\Voyager\Models\Setting;
use TCG\Voyager\Policies\BasePolicy;
use TCG\Voyager\Policies\MenuItemPolicy;
use TCG\Voyager\Policies\SettingPolicy;
use TCG\Voyager\Providers\VoyagerEventServiceProvider;
use TCG\Voyager\Translator\Collection as TranslatorCollection;

class VoyagerServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Setting::class  => SettingPolicy::class,
        MenuItem::class => MenuItemPolicy::class,
    ];

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->register(VoyagerEventServiceProvider::class);
        $this->app->register(ImageServiceProvider::class);
        $this->app->register(WidgetServiceProvider::class);
        $this->app->register(VoyagerHooksServiceProvider::class);
        $this->app->register(DoctrineSupportServiceProvider::class);

        $loader = AliasLoader::getInstance();
        $loader->alias('Voyager', VoyagerFacade::class);

        $this->app->singleton('voyager', function () {
            return new Voyager();
        });

        $this->loadHelpers();

        $this->registerAlertComponents();
        $this->registerFormFields();
        $this->registerWidgets();

        $this->registerConfigs();

        if ($this->app->runningInConsole()) {
            $this->registerPublishableResources();
            $this->registerConsoleCommands();
        }

        if (!$this->app->runningInConsole() || config('app.env') == 'testing') {
            $this->registerAppCommands();
        }
    }

    /**
     * Bootstrap the application services.
     *
     * @param \Illuminate\Routing\Router $router
     */
    public function boot(Router $router, Dispatcher $event)
    {
        if (config('voyager.user.add_default_role_on_register')) {
            $app_user = config('voyager.user.namespace');
            $app_user::created(function ($user) {
                if (is_null($user->role_id)) {
                    VoyagerFacade::model('User')->findOrFail($user->id)
                        ->setRole(config('voyager.user.default_role'))
                        ->save();
                }
            });
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'voyager');

        if (app()->version() >= 5.4) {
            $router->aliasMiddleware('admin.user', VoyagerAdminMiddleware::class);

            if (config('app.env') == 'testing') {
                $this->loadMigrationsFrom(realpath(__DIR__.'/migrations'));
            }
        } else {
            $router->middleware('admin.user', VoyagerAdminMiddleware::class);
        }

        $this->registerGates();

        $this->registerViewComposers();

        $event->listen('voyager.alerts.collecting', function () {
            $this->addStorageSymlinkAlert();
        });

        $this->bootTranslatorCollectionMacros();
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

    /**
     * Register view composers.
     */
    protected function registerViewComposers()
    {
        // Register alerts
        View::composer('voyager::*', function ($view) {
            $view->with('alerts', VoyagerFacade::alerts());
        });
    }

    /**
     * Add storage symlink alert.
     */
    protected function addStorageSymlinkAlert()
    {
        if (app('router')->current() !== null) {
            $currentRouteAction = app('router')->current()->getAction();
        } else {
            $currentRouteAction = null;
        }
        $routeName = is_array($currentRouteAction) ? array_get($currentRouteAction, 'as') : null;

        if ($routeName != 'voyager.dashboard') {
            return;
        }

        $storage_disk = (!empty(config('voyager.storage.disk'))) ? config('voyager.storage.disk') : 'public';

        if (request()->has('fix-missing-storage-symlink')) {
            if (file_exists(public_path('storage'))) {
                if (readlink(public_path('storage')) == public_path('storage')) {
                    rename(public_path('storage'), 'storage_old');
                }
            }

            if (!file_exists(public_path('storage'))) {
                $this->fixMissingStorageSymlink();
            }
        } elseif ($storage_disk == 'public') {
            if (!file_exists(public_path('storage')) ||
               (file_exists(public_path('storage')) && readlink(public_path('storage')) == public_path('storage'))) {
                $alert = (new Alert('missing-storage-symlink', 'warning'))
                    ->title(__('voyager.error.symlink_missing_title'))
                    ->text(__('voyager.error.symlink_missing_text'))
                    ->button(__('voyager.error.symlink_missing_button'), '?fix-missing-storage-symlink=1');
                VoyagerFacade::addAlert($alert);
            }
        }
    }

    protected function fixMissingStorageSymlink()
    {
        app('files')->link(storage_path('app/public'), public_path('storage'));

        if (file_exists(public_path('storage'))) {
            $alert = (new Alert('fixed-missing-storage-symlink', 'success'))
                ->title(__('voyager.error.symlink_created_title'))
                ->text(__('voyager.error.symlink_created_text'));
        } else {
            $alert = (new Alert('failed-fixing-missing-storage-symlink', 'danger'))
                ->title(__('voyager.error.symlink_failed_title'))
                ->text(__('voyager.error.symlink_failed_text'));
        }

        VoyagerFacade::addAlert($alert);
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

    protected function bootTranslatorCollectionMacros()
    {
        Collection::macro('translate', function () {
            $transtors = [];

            foreach ($this->all() as $item) {
                $transtors[] = call_user_func_array([$item, 'translate'], func_get_args());
            }

            return new TranslatorCollection($transtors);
        });
    }

    /**
     * Register widget.
     */
    protected function registerWidgets()
    {
        $default_widgets = ['TCG\\Voyager\\Widgets\\UserDimmer', 'TCG\\Voyager\\Widgets\\PostDimmer', 'TCG\\Voyager\\Widgets\\PageDimmer'];
        $widgets = config('voyager.dashboard.widgets', $default_widgets);

        foreach ($widgets as $widget) {
            Widget::group('voyager::dimmers')->addWidget($widget);
        }
    }

    /**
     * Register the publishable files.
     */
    private function registerPublishableResources()
    {
        $publishablePath = dirname(__DIR__).'/publishable';

        $publishable = [
            'voyager_assets' => [
                "{$publishablePath}/assets/" => public_path(config('voyager.assets_path')),
            ],
            'migrations' => [
                "{$publishablePath}/database/migrations/" => database_path('migrations'),
            ],
            'seeds' => [
                "{$publishablePath}/database/seeds/" => database_path('seeds'),
            ],
            'demo_content' => [
                "{$publishablePath}/demo_content/" => storage_path('app/public'),
            ],
            'config' => [
                "{$publishablePath}/config/voyager.php" => config_path('voyager.php'),
            ],
            'lang' => [
                "{$publishablePath}/lang/" => base_path('resources/lang/'),
            ],
        ];

        foreach ($publishable as $group => $paths) {
            $this->publishes($paths, $group);
        }
    }

    public function registerConfigs()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/publishable/config/voyager.php', 'voyager'
        );
    }

    public function registerGates()
    {
        // This try catch is necessary for the Package Auto-discovery
        // otherwise it will throw an error because no database
        // connection has been made yet.
        try {
            if (Schema::hasTable('data_types')) {
                $dataType = VoyagerFacade::model('DataType');
                $dataTypes = $dataType->select('policy_name', 'model_name')->get();

                foreach ($dataTypes as $dataType) {
                    $policyClass = BasePolicy::class;
                    if (isset($dataType->policy_name) && $dataType->policy_name !== ''
                        && class_exists($dataType->policy_name)) {
                        $policyClass = $dataType->policy_name;
                    }

                    $this->policies[$dataType->model_name] = $policyClass;
                }

                $this->registerPolicies();
            }
        } catch (\PDOException $e) {
            Log::error('No Database connection yet in VoyagerServiceProvider registerGates()');
        }
    }

    protected function registerFormFields()
    {
        $formFields = [
            'checkbox',
            'color',
            'date',
            'file',
            'image',
            'multiple_images',
            'number',
            'password',
            'radio_btn',
            'rich_text_box',
            'code_editor',
            'markdown_editor',
            'select_dropdown',
            'select_multiple',
            'text',
            'text_area',
            'timestamp',
            'hidden',
            'coordinates',
        ];

        foreach ($formFields as $formField) {
            $class = studly_case("{$formField}_handler");

            VoyagerFacade::addFormField("TCG\\Voyager\\FormFields\\{$class}");
        }

        VoyagerFacade::addAfterFormField(DescriptionHandler::class);

        event(new FormFieldsRegistered($formFields));
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
