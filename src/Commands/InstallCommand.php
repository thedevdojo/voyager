<?php

namespace TCG\Voyager\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
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

    protected $routes = "\n\nRoute::group(['prefix' => 'admin'], function () {\n    Voyager::routes();\n});\n";

    /**
     * Installation options.
     *
     * @var array
     */
    protected $installOptions = [
        'cancel' => 'Cancel the installation.',
        're-install' => 'This will uninstall Voyager and install it again. Please backup your data before doing this.',
        'uninstall' => 'This will uninstall Voyager. Please backup your data before doing this.'
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
     * Execute the console command.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     *
     * @return void
     */
    public function fire(Filesystem $filesystem) {
        // if this is the first time to install Voyager, just install it normally
        if( ! $this->checkExistingInstallation() ) {
            return $this->install($filesystem);
        }

        // warn user and ask him what to do in case there is an existing installation
        $this->error('Warning: there is already an existing installation of Voyager');
        $todo = $this->choice('What do you want to do?', $this->installOptions, 'cancel');

        switch ($todo) {
            case 're-install':
                if( $this->uninstall($filesystem) ) {
                    $this->install($filesystem);
                }
                break;

            case 'uninstall':
                $this->uninstall($filesystem);
                break;
        }
    }

    /**
     * Checks if there is an existing installation of Voyager.
     *
     * @return bool
     */
    protected function checkExistingInstallation() {
        return file_exists(config_path('voyager.php'));
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

        $this->info('Adding Voyager routes to routes/web.php');
        $filesystem->append(base_path('routes/web.php'), $this->routes);

        $this->info('Seeding data into the database');
        $this->seed('VoyagerDatabaseSeeder');

        if ($this->option('with-dummy')) {
            $this->seed('VoyagerDummyDatabaseSeeder');
        }

        $this->info('Adding the storage symlink to your public folder');
        $this->call('storage:link');

        $this->info('Successfully installed Voyager! Enjoy 🎉');
    }

    /**
     * Unpublishes the assets for a tag.
     *
     * @param  string  $tag
     * @return mixed
     */
    protected function deleteAssets(Filesystem $filesystem) {
        $voyagerAssets = ServiceProvider::pathsToPublish(VoyagerServiceProvider::class);

        // currently, it's only safe to remove the files
        // TODO: copy asset directories to a specific voyager path to delete them easily
        $filesystem->delete($voyagerAssets);
    }

    /**
     * Removes a line from a file.
     *
     * @return void
     */
    protected function removeLineFromFile($line, $file) {

        file_put_contents(
            $file,
            str_replace($line, '', file_get_contents($file))
        );
    }

    /**
     * Removes Voyager routes from routes file
     *
     * @return void
     */
    protected function deleteRoutes() {
        $this->removeLineFromFile($this->routes, base_path('routes/web.php'));
    }

    /**
     * Performs Voyager uninstallation.
     *
     * @return bool
     */
    protected function uninstall(Filesystem $filesystem) {
        // todo: move this to its own Command
        // later just call it
        
        if( $this->confirm('This will erase your current data. Are you sure you want to continue?') ) {

            $this->info('Uninstalling Voyager...');

            $this->info('Deleting assets...');
            $this->deleteAssets($filesystem);

            $this->info('Reset the migrations...');
            $this->call('migrate:reset');

            $this->info('Deleting routes...');
            $this->deleteRoutes();

            $this->info('Successfully uninstalled Voyager!');

            return true;
        }

        return false;
    }
}
