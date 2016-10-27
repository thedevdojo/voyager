<?php

namespace TCG\Voyager;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class VoyagerCommand extends Command
{
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

    /**
     * Create a new command instance.
     *
     * @return \Orangehill\Iseed\IseedCommand
     */
    public function __construct()
    {
        parent::__construct();
    }


    protected function getOptions()
    {
        return array(
            array('existing', null, InputOption::VALUE_NONE, 'install on existing laravel application', null),
        );
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {

        if(!$this->option('existing')){
            $this->info("Generating the default authentication scaffolding");
            Artisan::call('make:auth');
        }

        $this->info("Publishing the Voyager assets, database, and config files");
        Artisan::call('vendor:publish');

        $this->info("Migrating the database tables into your application");
        Artisan::call('migrate');

        $this->info("Clearing the application cache");
        Artisan::call('cache:clear');

        $this->info("Dumping the autoloaded files and reloading all new files");
        exec('composer dump-autoload');

        while( !file_exists ( __DIR__ . '/../../../../database/seeds/VoyagerDatabaseSeeder.php' ) ){
            exec('composer dump-autoload');
        }
        $this->info("Seeding data into the database");
        Artisan::call('db:seed', ['--class'=>'VoyagerDatabaseSeeder']);

        $this->info("Adding the storage symlink to your public folder");
        Artisan::call('storage:link');

        $this->info("Successfully installed Voyager! Enjoy :)");
        return;
        
    }

}
