<?php

namespace TCG\Voyager\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class VoyagerEventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'TCG\Voyager\Events\BreadAdded'   => [
            'TCG\Voyager\Listeners\AddBreadMenuItem',
            'TCG\Voyager\Listeners\AddBreadPermission',
            'TCG\Voyager\Listeners\CreateBreadAddMigration'

        ],
        'TCG\Voyager\Events\BreadUpdated' => [
            'TCG\Voyager\Listeners\CreateBreadUpdateMigration'
        ],
        'TCG\Voyager\Events\BreadDeleted' => [
            'TCG\Voyager\Listeners\CreateBreadDeleteMigration'
        ],
        'TCG\Voyager\Events\TableAdded'   => [
            'TCG\Voyager\Listeners\CreateTableAddMigration'
        ],
        'TCG\Voyager\Events\TableUpdated' => [
            'TCG\Voyager\Listeners\CreateTableUpdateMigration'
        ],
        'TCG\Voyager\Events\TableDeleted' => [
            'TCG\Voyager\Listeners\CreateTableDeleteMigration'
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
