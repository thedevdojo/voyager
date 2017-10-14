<?php

namespace TCG\Voyager\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;

class BreadGenerator extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'voyager:bread {name}
                            {--migration : creates a new migration for this bread}
                            {--model : creates a new model for this bread}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Bread';

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        $seederName = Str::studly(Str::plural($this->argument('name')).'BreadSeeder');
        $this->info('Making BREAD');

        parent::handle();

        if ($this->option('migration')) {
            $this->createMigration();
        }

        if ($this->option('model')) {
            $this->createModel();
        }

        $this->info('You are almost ready: ');
        $this->info("1. Once you finish the seed configuration, you need to run : php artisan db:seed --class={$seederName}");
        $this->info('2. Optionally you may want to re-generate the permissions with: php artisan db:seed --class=PermissionRoleTableSeeder');
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());
        return $this->replacePlaceholders($stub)->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    /**
     * Replace placeholder text in the stub file
     *
     * @param  string &$stub
     * @return $this
     */
    public function replacePlaceholders(&$stub)
    {
        $name = $this->argument('name');
        $replacements = collect([
            'DummyStudlyCaseSingular' => Str::studly($name),
            'DummyStudlyCasePlural' => Str::plural(Str::studly($name)),
            'DummySnakeCaseSingular' => Str::snake($name),
            'DummySnakeCasePlural' => Str::plural(Str::snake($name))
        ]);
        foreach ($replacements as $placeholder => $replacement) {
            $stub = str_replace($placeholder, $replacement, $stub);
        }

        return $this;
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return  base_path('database/seeds').'/'.str_replace('\\', '/', Str::plural($name)).'BreadSeeder.php';
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return trim(studly_case($this->argument('name')));
    }

    /**
     * Create a migration file for the model.
     *
     * @return void
     */
    protected function createMigration()
    {
        $table = Str::plural(Str::snake(class_basename($this->argument('name'))));

        $this->call('make:migration', [
            'name' => "create_{$table}_table",
            '--create' => $table,
        ]);
    }

    /**
     * Create a model.
     *
     * @return void
     */
    protected function createModel()
    {
        $table = studly_case($this->argument('name'));
        $this->call('make:model', [
            'name' => $table
        ]);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return base_path('vendor/tcg/voyager/stubs/bread.stub');
    }
}
