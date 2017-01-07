<?php

namespace TCG\Voyager\Commands\Installation;

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

    /**
     * Installation options in case there is an existing installation in place.
     *
     * @var array
     */
    protected $installOptions = [
        'uninstall' => 'This will uninstall Voyager.',
        're-install' => 'This will uninstall Voyager and install it again.',
        'cancel' => 'Cancel the installation.',
    ];

    /**
     * Execute the console command.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     *
     * @return void
     */
    public function fire(Filesystem $filesystem) {
        // if this is the first time to install Voyager, just install it normally
        if( ! Settings::checkExistingInstallation() ) {
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
                if( ! Settings::checkExistingInstallation() ) {
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

        $this->info('Adding Voyager routes to routes/web.php');
        $filesystem->append(base_path('routes/web.php'), Settings::routes());

        $this->info('Dumping the autoloaded files and reloading all new files');

        $composer = Settings::findComposer();

        $process = new Process($composer.' dump-autoload');
        $process->setWorkingDirectory(base_path())->run();
        
        $this->info('Seeding data into the database');
        $this->seed('VoyagerDatabaseSeeder');

        if ($this->option('with-dummy')) {
            $this->seed('VoyagerDummyDatabaseSeeder');
        }

        $this->info('Adding the storage symlink to your public folder');
        $this->call('storage:link');

        $this->info('Successfully installed Voyager! Enjoy 🎉');
    }
}
