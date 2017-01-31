<?php

namespace TCG\Voyager\Tests;

use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Exceptions\Handler;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use TCG\Voyager\Models\User;
use TCG\Voyager\VoyagerServiceProvider;

class TestCase extends OrchestraTestCase
{
    protected $withDummy = true;

    public function setUp()
    {
        parent::setUp();

        $this->loadMigrationsFrom([
            '--realpath' => realpath(__DIR__.'/migrations'),
        ]);

        if (!is_dir(base_path('routes'))) {
            mkdir(base_path('routes'));
        }

        if (!file_exists(base_path('routes/web.php'))) {
            file_put_contents(
                base_path('routes/web.php'),
                "<?php Route::get('/', function () {return view('welcome');});"
            );
        }

        $this->app->make('Illuminate\Contracts\Http\Kernel')->pushMiddleware('Illuminate\Session\Middleware\StartSession');
        $this->app->make('Illuminate\Contracts\Http\Kernel')->pushMiddleware('Illuminate\View\Middleware\ShareErrorsFromSession');
    }

    protected function getPackageProviders($app)
    {
        return [
            VoyagerServiceProvider::class,
        ];
    }

    public function tearDown()
    {
        //parent::tearDown();

        //$this->artisan('migrate:reset');
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $driver = env('DB_DRIVER', 'sqlite');
        $method = camel_case("append_database_{$driver}_environment");

        // Setup default database
        $this->$method($app);

        // Setup Voyager configuration
        $app['config']->set('voyager.user.namespace', User::class);

        // Setup Authentication configuration
        $app['config']->set('auth.providers.users.model', User::class);
    }

    protected function appendDatabaseSqliteEnvironment(&$app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function appendDatabaseMysqlEnvironment(&$app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'port'      => '3306',
            'database'  => 'test',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
            'engine'    => null,
        ]);
    }

    protected function appendDatabasePostgresEnvironment(&$app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'pgsql',
            'host'     => 'localhost',
            'port'     => '5432',
            'database' => 'test',
            'username' => 'postgres',
            'password' => '',
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
            'sslmode'  => 'prefer',
        ]);
    }

    protected function install()
    {
        $this->artisan('voyager:install', ['--with-dummy' => $this->withDummy]);

        config()->set(
            'voyager',
            require __DIR__.'/../publishable/config/voyager.php'
        );
    }

    public function disableExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, new DisabledTestException());
    }
}

class DisabledTestException extends Handler
{
    public function __construct()
    {
    }

    public function report(Exception $e)
    {
    }

    public function render($request, Exception $e)
    {
        throw $e;
    }
}
