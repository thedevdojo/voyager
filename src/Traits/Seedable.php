<?php

namespace TCG\Voyager\Traits;

trait Seedable
{
    public function seed($class)
    {
        if (class_exists('Database\\Seeders\\'.$class)) {
            $class = 'Database\\Seeders\\'.$class;
        } elseif (!class_exists($class)) {
            require_once $this->seedersPath.$class.'.php';
        }

        with(new $class())->run();
    }
}
