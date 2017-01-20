<?php

namespace TCG\Voyager\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallRoutesCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'voyager:routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Voyager routes';

    /**
     * Execute the console command.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     *
     * @return void
     */
    public function fire(Filesystem $filesystem)
    {
        $filepath = base_path('routes/web.php');
        $contents = $filesystem->get($filepath);

        if (!$this->routesAlreadyInstalled($contents)) {
            $this->info('Adding Voyager routes to routes/web.php');
            $filesystem->append(
                $filepath,
                "\n\nRoute::group(['prefix' => 'admin'], function () {\n    Voyager::routes();\n});\n"
            );
        }
    }

    /**
     * @param string $contents - String contents of routes file
     *
     * @return bool
     */
    private function routesAlreadyInstalled($contents)
    {
        return (bool) preg_match('/Voyager::routes/m', $contents);
    }

}
