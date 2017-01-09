<?php

namespace TCG\Voyager\Commands\Installation;

use DB;
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

            $this->info('Rolling back Voyager migrations...');
            $this->rollbackMigrations($filesystem);

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
     * Rollback Voyager migrations.
     *
     * @return void
     */
    protected function rollbackMigrations(Filesystem $filesystem) {
        // since there is no easy way to rollback only Voyager specific migrations ATM, we will use a trick
        // 1. get voyager migrations
        // 2. get max of migrations.batch
        // 3. update voyager migrations: set their batch to maxBatch*25 to ensure they have the highest batch number
        // 4. migrate:rollback will rollback only voyager migrations because they have the highest batch number

        // 1. Get Voyager migrations
        $migrationFiles = $filesystem->allFiles(VoyagerServiceProvider::publishedPaths('migrations'));
        $migrations = [];
        foreach ($migrationFiles as $migrationFile) {
            $migrations[] = pathinfo($migrationFile->getFilename(), PATHINFO_FILENAME);
        }

        // 2. Get max batch number and multiply it by 25 to ensure that it's the highest
        $batch = DB::table('migrations')->max('batch') * 25;

        // 3. update Voyager migrations and set their batch to the highest one
        foreach ($migrations as $migration) {
            DB::table('migrations')
                ->where('migration', $migration)
                ->update(['batch' => $batch]);
        }

        // 4.1 Quick check to ensure that the batch was updated successfully
        if( DB::table('migrations')->max('batch') == $batch ) {
            // 4.2 Now since Voyager migrations have the highest batch number, only they will be rolled back
            return $this->call('migrate:rollback');
        }

        // 5. in case something wrong happened...
        $this->error('Error: could not rollback Voyager migrations');
    }

    /**
     * Deletes Voyager published resources.
     *
     * @return void
     */
    protected function deletePublishedResources(Filesystem $filesystem) {
        // published folders to delete
        $publishedFolders = VoyagerServiceProvider::publishedPaths([
            'voyager_assets',
            'migrations',
            'seeds',
            'views',
        ]);

        // delete folders
        foreach ($publishedFolders as $folder) {
            if( ! $filesystem->deleteDirectory($folder) ) {
                $this->error("Error: could not delete {$folder} folder");
            }
        }

        // published files to delete
        $publishedFiles = VoyagerServiceProvider::publishedPaths([
            'config',
        ]);

        // adding demo_content files to delete
        // idea: use this method to delete all files individually without worrying about folders?
        //       add a helper method to get all voyager files in published paths?
        $demoContentPath = key(VoyagerServiceProvider::publishableResources('demo_content'));
        $demoContentPublishedPath = VoyagerServiceProvider::publishedPaths('demo_content');

        foreach ($filesystem->allFiles($demoContentPath) as $file) {
            $publishedFiles[] = $demoContentPublishedPath . '/' . $file->getRelativePathname();
        }

        // delete files
        if( ! $filesystem->delete($publishedFiles) ) {
            $this->error('Error: some files were not deleted successfully');
        }
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
