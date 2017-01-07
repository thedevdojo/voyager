<?php

namespace TCG\Voyager\Commands\Installation;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use TCG\Voyager\VoyagerServiceProvider;

class UninstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'voyager:uninstall';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uninstall the Voyager Admin package';

    /**
     * Execute the console command.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     *
     * @return void
     */
    public function fire(Filesystem $filesystem) {
        if( ! Settings::checkExistingInstallation() ) {
            return $this->error('Voyager is not installed!');
        }

        if( $this->confirm("You are about to uninstall Voyager. This will erase your current data.\nAre you sure you want to continue?") ) {

            $this->info('Uninstalling Voyager...');

            $this->info('Deleting assets...');
            $this->deleteAssets($filesystem);

            // TODO: find a way to reset only Voyager related migrations and tables
            $this->info('Reset the migrations...');
            $this->call('migrate:reset');

            $this->info('Deleting routes...');
            $this->deleteRoutes();

            $this->info("Successfully uninstalled Voyager!\n");
            // add instrocutions to completely delete Voyager composer package
            // and delete VoyagerServiceProvider
        }
    }

    /**
     * Deletes Voyager assets.
     *
     * @return void
     */
    protected function deleteAssets(Filesystem $filesystem) {
        // get Voyager assets list
        $voyagerAssets = ServiceProvider::pathsToPublish(VoyagerServiceProvider::class);

        
        // currently, it's only safe to delete single Voyager asset files
        // this deletes only files
        // directories like migrations etc... won't be deleted
        $filesystem->delete($voyagerAssets);

        // TODO:
        // copy asset directories to a specific voyager path to delete them easily later
    }

    /**
     * Deletes Voyager routes.
     *
     * @return void
     */
    protected function deleteRoutes() {
        Settings::strReplaceFile(Settings::routes(), '', base_path('routes/web.php'));
    }
}
