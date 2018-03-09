<?php

namespace Tests;

use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use VoyagerDatabaseSeeder;

abstract class VoyagerTestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        Artisan::call('db:seed', ['--class' => VoyagerDatabaseSeeder::class]);

        app(RouteServiceProvider::class, ['app' => app(Application::class)])->boot();
    }
}
