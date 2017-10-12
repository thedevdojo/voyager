<?php

namespace TCG\Voyager\Tests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use TCG\Voyager\Models\Role;
use Illuminate\Support\Facades\Event;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Events\BreadAdded;
use TCG\Voyager\Events\BreadUpdated;
use TCG\Voyager\Events\BreadDeleted;
use TCG\Voyager\Events\TableAdded;
use TCG\Voyager\Events\TableUpdated;
use TCG\Voyager\Events\TableDeleted;

class EventTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->install();
    }

    public function testBreadAddedEvent()
    {
        Event::fake();
        Auth::loginUsingId(1);

        $this->post('/admin/database/bread', [
            'name' => 'Toast',
            'slug' => 'toast',
            'display_name_singular' => 'toast',
            'display_name_plural' => 'toasts',
            'icon' => 'fa fa-toast',
            'description' => 'This is a toast',
        ]);

        Event::assertDispatched(BreadAdded::class, function ($event) {
            return $event->dataType->name === 'Toast'
                || $event->dataType->slug === 'toast'
                || $event->dataType->display_name_singular === 'toast'
                || $event->dataType->display_name_plural === 'toasts'
                || $event->dataType->icon === 'fa fa-toast'
                || $event->dataType->description === 'This is a toast';
        });
    }

    public function testBreadUpdatedEvent()
    {
        Event::fake();
        Auth::loginUsingId(1);

        $this->post('/admin/database/bread', [
            'name' => 'Toast',
            'slug' => 'toast',
            'display_name_singular' => 'toast',
            'display_name_plural' => 'toasts',
            'icon' => 'fa fa-toast',
            'description' => 'This is a toast',
        ]);

        Event::assertNotDispatched(BreadUpdated::class);
        $dataType = DataType::where('slug', 'toast')->firstOrFail();

        $this->put('/admin/database/bread/'.$dataType->id, [
            'name' => 'Test',
            'slug' => 'test',
            'display_name_singular' => 'test',
            'display_name_plural' => 'tests',
            'icon' => 'fa fa-test',
            'description' => 'This is a test',
        ]);

        Event::assertDispatched(BreadUpdated::class, function ($event) {
            return $event->dataType->name === 'Test'
                || $event->dataType->slug === 'test'
                || $event->dataType->display_name_singular === 'test'
                || $event->dataType->display_name_plural === 'tests'
                || $event->dataType->icon === 'fa fa-test'
                || $event->dataType->description === 'This is a test';
        });
    }

    public function testBreadDeletedEvent()
    {
        Event::fake();
        Auth::loginUsingId(1);

        $this->post('/admin/database/bread', [
            'name' => 'Toast',
            'slug' => 'toast',
            'display_name_singular' => 'toast',
            'display_name_plural' => 'toasts',
            'icon' => 'fa fa-toast',
            'description' => 'This is a toast',
        ]);

        Event::assertNotDispatched(BreadDeleted::class);
        $dataType = DataType::where('slug', 'toast')->firstOrFail();

        $this->delete('/admin/database/bread/'.$dataType->id);

        Event::assertDispatched(BreadDeleted::class);
    }

    public function testBreadDataAddedEvent() {}
    public function testBreadDataUpdatedEvent() {}
    public function testBreadDataDeletedEvent() {}
    public function testBreadDataChangedEvent() {}
    public function testBreadImagesDeletedEvent() {}
    public function testFileDeletedEvent() {}

    public function testTableAddedEvent()
    {
        Event::fake();
        Auth::loginUsingId(1);

        $this->post('/admin/database', [
            'table' => [
                'name' => 'test',
                'columns' => [
                    [
                        'name' => 'id',
                        'type' => [
                            'name' => 'integer',
                        ],
                    ],
                ],
                'indexes' => [],
                'foreignKeys' => [],
                'options' => [],
            ],
        ]);

        Event::assertDispatched(TableAdded::class);
    }

    public function testTableUpdatedEvent()
    {
        Event::fake();
        Auth::loginUsingId(1);

        $this->post('/admin/database', [
            'table' => [
                'name' => 'test',
                'columns' => [
                    [
                        'name' => 'id',
                        'type' => [
                            'name' => 'integer',
                        ],
                    ],
                ],
                'indexes' => [],
                'foreignKeys' => [],
                'options' => [],
            ],
        ]);

        Event::assertNotDispatched(TableUpdated::class);

        $this->put('/admin/database/test', [
            'table' => json_encode([
                'name' => 'test',
                'oldName' => 'test',
                'columns' => [
                    [
                        'name' => 'id',
                        'oldName' => 'id',
                        'type' => [
                            'name' => 'integer',
                        ],
                    ],
                ],
                'indexes' => [],
                'foreignKeys' => [],
                'options' => [],
            ]),
        ]);

        Event::assertDispatched(TableUpdated::class);
    }

    public function testTableDeletedEvent()
    {
        Event::fake();
        Auth::loginUsingId(1);

        $this->post('/admin/database', [
            'table' => [
                'name' => 'test',
                'columns' => [
                    [
                        'name' => 'id',
                        'type' => [
                            'name' => 'integer',
                        ],
                    ],
                ],
                'indexes' => [],
                'foreignKeys' => [],
                'options' => [],
            ],
        ]);

        Event::assertNotDispatched(TableDeleted::class);

        $this->delete('/admin/database/test');

        Event::assertDispatched(TableDeleted::class);
    }
}
