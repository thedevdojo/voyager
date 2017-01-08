<?php

namespace TCG\Voyager\Commands\Installation;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
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
     * @return bool
     */
    public function fire(Filesystem $filesystem) {
        if( ! Settings::checkExistingInstallation() ) {
            return $this->error('Voyager is not installed!');
        }

        if( $this->confirm("You are about to uninstall Voyager. This will erase your current data.\nAre you sure you want to continue?") ) {

            $this->info('Uninstalling Voyager...');

            // TODO: find a way to reset only Voyager related migrations and tables
            // IDEA: drop voyager related table
            //       delete from migrations table using a query
            $this->info('Reset the migrations...');
            $this->call('migrate:reset');

            $this->info('Deleting published resources...');
            $this->deletePublishedResources($filesystem);

            $this->info('Deleting routes...');
            $this->deleteRoutes();

            // check if Voyager still exists
            if( Settings::checkExistingInstallation() ) {
                return $this->error("Error: Voyager was not uninstalled properly");
            }

            $this->info("Successfully uninstalled Voyager!\n");

            $this->info("To completely remove Voyager, follow these steps:");
            $this->info("1. composer remove tcg/voyager");
            $this->info("2. remove TCG\\Voyager\\VoyagerServiceProvider::class from config/app.php\n");
        }
    }

    /**
     * Deletes Voyager assets.
     *
     * @return void
     */
    protected function deletePublishedResources(Filesystem $filesystem) {
        // resource folders to delete
        $resourceFolders = VoyagerServiceProvider::publishedPaths([
            'voyager_assets',
            'migrations',
            'seeds',
            'views',
        ]);

        foreach ($resourceFolders as $folder) {
            $filesystem->deleteDirectory($folder);
        }

        // resource files to delete
        $resourceFiles = VoyagerServiceProvider::publishedPaths([
            'config',
        ]);

        $filesystem->delete($resourceFiles);

        // only thing left is demo_content
        // idea: get file names from publishable/demo_content, and delete them from store/app/public
    }

    /**
     * Deletes Voyager routes from /routes/web.php.
     *
     * @return void
     */
    protected function deleteRoutes() {
        Settings::strReplaceFile(Settings::routes(), '', base_path('routes/web.php'));
    }
}
