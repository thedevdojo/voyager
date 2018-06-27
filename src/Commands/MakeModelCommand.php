<?php

namespace TCG\Voyager\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeModelCommand extends ModelMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'voyager:make:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Voyager model class';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../stubs/model.stub';
    }

    /**
     * Build the class with the given name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->addSoftDelete($stub)->addTraitTranslatable($stub)->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    /**
     * Add SoftDelete to the given stub.
     *
     * @param string $stub
     *
     * @return $this
     */
    protected function addSoftDelete(&$stub)
    {
        $traitIncl = $trait = '';

        if ($this->option('softdelete')) {
            $traitIncl = 'use Illuminate\Database\Eloquent\SoftDeletes;';
            $trait = 'use SoftDeletes;';
        }

        $stub = str_replace('//DummySDTraitInclude', $traitIncl, $stub);
        $stub = str_replace('//DummySDTrait', $trait, $stub);

        return $this;
    }

    /**
     * Add Translatable to the given stub.
     *
     * @param string $stub
     *
     * @return $this
     */
    protected function addTraitTranslatable(&$stub)
    {
        $traitIncl = $trait = $translatablefields = '';

        if ($this->option('traitTranslatable')) {
            $traitIncl = 'use TCG\Voyager\Traits\Translatable;';
            $trait = 'use Translatable;';
            $translatablefields = 'protected $translatable = ["'.implode($this->option('traitTranslatable'), '", "').'"]';
        }

        $stub = str_replace('//DummyTranslatableTraitInclude', $traitIncl, $stub);
        $stub = str_replace('//DummyTranslatableTrait', $trait, $stub);
        $stub = str_replace('//DummyTranslatableFields', $translatablefields, $stub);

        return $this;
    }
    
    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = [
            ['softdelete', 'd', InputOption::VALUE_NONE, 'Add soft-delete field to Model'],
            ['traitTranslatable', null, InputOption::VALUE_NONE, 'Add Translatable trait to Model.']
        ];

        return array_merge($options, parent::getOptions());
    }
}
