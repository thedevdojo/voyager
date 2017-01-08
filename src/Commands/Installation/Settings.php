<?php

namespace TCG\Voyager\Commands\Installation;

use Symfony\Component\Process\Process;
use TCG\Voyager\VoyagerServiceProvider;

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

    /**
     * Path to seeds folder.
     *
     * @return string
     */
    public static function seedersPath() {
        return VoyagerServiceProvider::publishedPaths('seeds');
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
     * @param string $cmd Composer command to execute.
     *
     * @return string Command output.
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
     * Replaces a string within a file.
     *
     * @param string $search The string to replace.
     * @param string $replace The string to replace with.
     * @param string $file The file to replace its content.
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
