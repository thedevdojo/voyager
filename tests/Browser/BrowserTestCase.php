<?php

namespace TCG\Voyager\Tests\Browser;

use Orchestra\Testbench\Dusk\TestCase;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\VoyagerServiceProvider;

class BrowserTestCase extends TestCase
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

        $this->setUpTheBrowserEnvironment();
        $this->registerShutdownFunction();

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

    /**
     * Setup Voyager.
     */
    protected function createUserTable(): void
    {
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
}
