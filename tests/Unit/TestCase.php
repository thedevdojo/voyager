<?php

namespace TCG\Voyager\Tests\Unit;

use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\VoyagerServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
        /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $db_dir = realpath($this->getBasePath().'/database');
        if (!file_exists($db_dir.'/database.sqlite')) {
            copy($db_dir.'/database.sqlite.example', $db_dir.'/database.sqlite');
        }

        $route_dir = realpath($this->getBasePath());
        if (!is_dir($route_dir.'/routes')) {
            @mkdir($route_dir.'/routes');
        }
        if (!file_exists($route_dir.'/routes/web.php')) {
            file_put_contents($route_dir.'/routes/web.php', "<?php\n");
        }

        $this->loadLaravelMigrations(config('database.default'));

        $this->setupVoyager();
    }

    /**
     * Setup Voyager.
     */
    protected function setupVoyager(): void
    {
        $this->artisan('voyager:install');
    }

    protected function getPackageProviders($app)
    {
        return [
            VoyagerServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['router']->prefix('admin')->group(function (\Illuminate\Routing\Router $router) {
            Voyager::routes($router);
        });
    }

    /**
    * Make sure all integration tests use the same Laravel "skeleton" files.
    * This avoids duplicate classes during migrations.
    *
    * Overrides \Orchestra\Testbench\Dusk\TestCase::getBasePath
    *       and \Orchestra\Testbench\Concerns\CreatesApplication::getBasePath
    *
    * @return string
    */
    protected function getBasePath()
    {
        // Adjust this path depending on where your override is located.
        return __DIR__.'/../../vendor/orchestra/testbench-dusk/laravel'; 
    }
}
