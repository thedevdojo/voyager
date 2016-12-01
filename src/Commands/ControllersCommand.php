<?php

namespace TCG\Voyager\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Console\AppNamespaceDetectorTrait;

class ControllersCommand extends Command
{
    use AppNamespaceDetectorTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'voyager:controllers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish all the controllers from Voyager.';

    /**
     * The Filesystem instance.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Filename of stub-file.
     *
     * @var string
     */
    protected $stub = 'controller.stub';

    /**
     * Create a new command instance.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $stub = $this->getStub();
        $files = $this->filesystem->files(base_path('vendor/tcg/voyager/src/Http/Controllers'));
        $location = $this->option('location');

        if (!$this->filesystem->isDirectory(base_path("app/{$location}"))) {
            $this->filesystem->makeDirectory(base_path("app/{$location}"));
        }

        foreach ($files as $file) {
            $parts = explode('/', $file);
            $filename = end($parts);

            if ($filename == 'Controller.php') {
                continue;
            }

            $path = base_path("app/{$location}/{$filename}");

            if (!$this->filesystem->exists($path) OR $this->option('force')) {
                $classname = substr($filename, 0, strpos($filename, '.'));
                $content = $this->generateContent($stub, $classname);
                $this->filesystem->put($path, $content);
            }
        }

        $this->info('Published Voyager controllers!');
    }

    /**
     * Get stub content.
     *
     * @return string
     */
    public function getStub()
    {
        return $this->filesystem->get(base_path('/vendor/tcg/voyager/stubs/'.$this->stub));
    }

    /**
     * Generate real content from stub.
     *
     * @param $stub
     * @param $classname
     * @return mixed
     */
    protected function generateContent($stub, $classname)
    {
        $content = str_replace(
            'DummyNamespace',
            $this->getAppNamespace().$this->getLocationNamespace(),
            $stub
        );

        $content = str_replace(
            'FullBaseDummyClass',
            'TCG\\Voyager\\Http\\Controllers\\'.$classname,
            $content
        );

        $content = str_replace(
            'BaseDummyClass',
            'Base'.$classname,
            $content
        );

        $content = str_replace(
            'DummyClass',
            $classname,
            $content
        );

        return $content;
    }

    /**
     * Get location based namespace.
     *
     * @return string
     */
    protected function getLocationNamespace()
    {
        return str_replace('/', '\\', $this->option('location'));
    }

    /**
     * Get command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['location', 'l', InputOption::VALUE_OPTIONAL, 'The application location for controller', 'Http/Controllers/Admin'],

            ['force', 'f', InputOption::VALUE_NONE, 'Overwrite existing controller files'],
        ];
    }
}
