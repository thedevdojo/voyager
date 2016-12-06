<?php

namespace TCG\Voyager\Traits;

trait Seedable
{
    public function seed($class)
    {
        if (!class_exists($class)) {
            require_once $this->seedersPath.$class.'.php';
        }

        with(new $class())->run();

        //$process = new Process('php artisan db:seed --class='.$class);
        //$process->setWorkingDirectory(base_path())->run();
    }
}
