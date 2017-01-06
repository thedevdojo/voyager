<?php

namespace TCG\Voyager\Commands;

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
        if( ! InstallCommand::checkExistingInstallation() ) {
            return $this->error('Voyager is not installed!');
        }

        if( $this->confirm('This will erase your current data. Are you sure you want to continue?') ) {

            $this->info('Uninstalling Voyager...');

            $this->info('Deleting assets...');
            $this->deleteAssets($filesystem);

            $this->info('Reset the migrations...');
            $this->call('migrate:reset');

            $this->info('Deleting routes...');
            $this->deleteRoutes();

            $this->info('Deleting VoyagerServiceProvider...');
            // ......

            $this->info('Successfully uninstalled Voyager!\n');
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
        // this deletes only files. directories like migrations etc... won't be deleted
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
        $this->strReplaceFile(InstallCommand::$routes, '', base_path('routes/web.php'));
    }

    /**
     * Helper function: Replaces a string in a file.
     *
     * @return void
     */
    protected function strReplaceFile($search, $replace, $file) {

        file_put_contents(
            $file,
            str_replace($search, $replace, file_get_contents($file))
        );
    }
}
