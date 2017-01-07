<?php

namespace TCG\Voyager\Commands\Installation;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use TCG\Voyager\VoyagerServiceProvider;

class BackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'voyager:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the Voyager Admin package';

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
    }
}
