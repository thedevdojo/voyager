<?php

namespace TCG\Voyager\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Intervention\Image\ImageServiceProviderLaravel5;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;
use TCG\Voyager\Traits\Seedable;
use TCG\Voyager\VoyagerServiceProvider;

class InstallCommand extends Command
{
    use Seedable;

    protected $seedersPath = __DIR__.'/../../publishable/database/seeds/';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'voyager:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Voyager Admin package';

    protected function getOptions()
    {
        return [
            ['with-dummy', null, InputOption::VALUE_NONE, 'Install with dummy data', null],
        ];
    }

    public static $voyagerRoutes = "\n\nRoute::group(['prefix' => 'admin'], function () {\n    Voyager::routes();\n});\n";

    public static $voyagerServiceProvider = "\nTCG\\Voyager\\VoyagerServiceProvider::class,\n";

    /**
     * Installation options in case there is an existing installation in place.
     *
     * @var array
     */
    protected $installOptions = [
        'cancel' => 'Cancel the installation.',
        'uninstall' => 'This will uninstall Voyager. Please backup your data before doing this.',
        're-install' => 'This will uninstall Voyager and install it again. Please backup your data before doing this.'
    ];

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer()
    {
        if (file_exists(getcwd().'/composer.phar')) {
            return '"'.PHP_BINARY.'" '.getcwd().'/composer.phar';
        }

        return 'composer';
    }

    /**
     * Checks if there is an existing installation of Voyager.
     *
     * @return bool
     */
    public static function checkExistingInstallation() {
        return file_exists(config_path('voyager.php'));
    }

    /**
     * Checks if there is an existing installation of Voyager.
     *
     * @return bool
     */
    protected function addVoyagerServiceProvider() {
        $packageProviders = "/*\n         * Package Service Providers...\n         */";

        $appConfigFile = config_path('app.php');
        $appConfigContents = file_get_contents($appConfigFile);
        
        if( strpos($appConfigContents, $packageProviders) === false )
        {
            $this->error("Could not add VoyagerServiceProvider automatically.\nPlease add it manually to /config/app.php providers array in Package Service  Providers:\n" . static::$voyagerServiceProvider . "\n");
        }

        file_put_contents(
            $appConfigFile,
            str_replace(
                $packageProviders,
                $packageProviders . static::$voyagerServiceProvider,
                $appConfigContents
            )
        );
    }

    /**
     * Execute the console command.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     *
     * @return void
     */
    public function fire(Filesystem $filesystem) {
        // if this is the first time to install Voyager, just install it normally
        if( ! static::checkExistingInstallation() ) {
            return $this->install($filesystem);
        }

        // warn user and ask him what to do in case there is an existing installation
        $this->error('Warning: there is already an existing installation of Voyager');
        
        $todo = $this->choice(
            'What do you want to do?',
            $this->installOptions,
            'cancel'
        );

        switch ($todo) {
            case 'uninstall':
                $this->call('voyager:uninstall');
                break;

            case 're-install':
                $this->call('voyager:uninstall');
                
                // check if Voyager was uninstalled properly
                if( ! static::checkExistingInstallation() ) {
                    $this->install($filesystem);
                }
                break;
        }
    }

    /**
     * Performs Voyager installation.
     *
     * @return void
     */
    protected function install(Filesystem $filesystem) {
        $this->info('Installing Voyager...');
        $this->info('Publishing the Voyager assets, database, and config files');
        $this->call('vendor:publish', ['--provider' => VoyagerServiceProvider::class]);
        $this->call('vendor:publish', ['--provider' => ImageServiceProviderLaravel5::class]);

        $this->info('Migrating the database tables into your application');
        $this->call('migrate');

        $this->info('Dumping the autoloaded files and reloading all new files');

        $composer = $this->findComposer();

        $process = new Process($composer.' dump-autoload');
        $process->setWorkingDirectory(base_path())->run();

        
        $this->info('Seeding data into the database');
        $this->seed('VoyagerDatabaseSeeder');

        if ($this->option('with-dummy')) {
            $this->seed('VoyagerDummyDatabaseSeeder');
        }

        $this->info('Adding the storage symlink to your public folder');
        $this->call('storage:link');

        $this->info('Adding Voyager routes to routes/web.php');
        $filesystem->append(base_path('routes/web.php'), static::$voyagerRoutes);

        $this->info('Adding VoyagerServiceProvider...');
        $this->addVoyagerServiceProvider();

        $this->info('Successfully installed Voyager! Enjoy 🎉');
    }
}
