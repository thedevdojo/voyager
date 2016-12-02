<?php

namespace TCG\Voyager\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use TCG\Voyager\VoyagerServiceProvider;
use Symfony\Component\Console\Input\InputOption;
use Intervention\Image\ImageServiceProviderLaravel5;

class InstallCommand extends Command
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
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function getOptions()
    {
        return [
            ['with-dummy', null, InputOption::VALUE_NONE, 'Install with dummy data', null],
        ];
    }

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
     * @return void
     */
    public function fire()
    {
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

        $this->seed([
            'DataTypesTableSeeder',
            'DataRowsTableSeeder',
            'RolesTableSeeder',
            'MenusTableSeeder',
            'MenuItemsTableSeeder',
        ]);

        if ($this->option('with-dummy')) {
            $this->seed([
                'CategoriesTableSeeder',
                'UsersTableSeeder',
                'PostsTableSeeder',
                'PagesTableSeeder',
                'SettingsTableSeeder',
            ]);
        }

        $this->info('Adding the storage symlink to your public folder');
        $this->call('storage:link');

        $this->info('Successfully installed Voyager! Enjoy 🎉');
    }

    /**
     * Run database seeders.
     *
     * @param array $seeders
     */
    protected function seed(array $seeders)
    {
        foreach ($seeders as $seeder) {
            $this->call('db:seed', ['--class' => $seeder]);
        }
    }
}
