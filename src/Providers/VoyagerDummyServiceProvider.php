<?php

namespace TCG\Voyager\Providers;

use Arrilot\Widgets\Facade as Widget;
use Arrilot\Widgets\ServiceProvider as WidgetServiceProvider;
use Illuminate\Support\ServiceProvider;

class VoyagerDummyServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->register(WidgetServiceProvider::class);

        $this->registerWidgets();
        $this->registerConfigs();

        if ($this->app->runningInConsole()) {
            $this->registerPublishableResources();
        }
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
        $publishablePath = dirname(__DIR__).'/../publishable';

        $publishable = [
            'dummy_seeds' => [
                "{$publishablePath}/database/dummy_seeds/" => database_path('seeds'),
            ],
            'dummy_content' => [
                "{$publishablePath}/dummy_content/" => storage_path('app/public'),
            ],
            'dummy_config' => [
                "{$publishablePath}/config/voyager_dummy.php" => config_path('voyager.php'),
            ],
            'dummy_migrations' => [
                "{$publishablePath}/database/migrations/" => database_path('migrations'),
            ],

        ];

        foreach ($publishable as $group => $paths) {
            $this->publishes($paths, $group);
        }
    }

    public function registerConfigs()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/../publishable/config/voyager_dummy.php', 'voyager'
        );
    }
}
