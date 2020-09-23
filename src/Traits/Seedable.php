<?php

namespace TCG\Voyager\Traits;

trait Seedable
{
    public function seed($class)
    {
        if (!class_exists($class)) {
            require_once $this->seedersPath.$class.'.php';

            if (class_exists('Database\\Seeders\\'.$class)) {
                $class = 'Database\\Seeders\\'.$class;
            }
        }

        with(new $class())->run();
    }
}
