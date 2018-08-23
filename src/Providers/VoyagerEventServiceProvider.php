<?php

namespace TCG\Voyager\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use TCG\Voyager\Events\SettingUpdated;
use TCG\Voyager\Listeners\ClearCachedSettingValue;

class VoyagerEventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'TCG\Voyager\Events\BreadAdded' => [
            'TCG\Voyager\Listeners\AddBreadMenuItem',
            'TCG\Voyager\Listeners\AddBreadPermission',
        ],
        SettingUpdated::class => [
            ClearCachedSettingValue::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
