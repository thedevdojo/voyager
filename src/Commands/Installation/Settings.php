<?php

namespace TCG\Voyager\Commands\Installation;

use Symfony\Component\Process\Process;

class Settings
{
	/* Core installation settings */

	/**
     * Voyager routes to be added to /routes/web.php
     *
     * @return string
     */
	public static function routes() {
		return "\n\nRoute::group(['prefix' => 'admin'], function () {\n    Voyager::routes();\n});\n";
	}


	/* Helper Functions */

	/**
     * Checks if there is an existing installation of Voyager.
     *
     * @return bool
     */
    public static function checkExistingInstallation() {
        return file_exists(config_path('voyager.php'));
    }

    /**
     * Executes a Composer command.
     *
     * @param $cmd string
     *
     * @return string - command output
     */
    public static function composer($cmd) {
        $process = new Process(static::findComposer() . " {$cmd}");
        $process->setWorkingDirectory(base_path())->run();

        return $process->getOutput() . $process->getErrorOutput();
    }


    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    public static function findComposer()
    {
        if (file_exists(getcwd().'/composer.phar')) {
            return '"'.PHP_BINARY.'" '.getcwd().'/composer.phar';
        }

        return 'composer';
    }

    /**
     * Replaces a string in a file.
     *
     * @return void
     */
    public static function strReplaceFile($search, $replace, $file) {

        file_put_contents(
            $file,
            str_replace($search, $replace, file_get_contents($file))
        );
    }
}
