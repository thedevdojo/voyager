<?php

namespace TCG\Voyager\Commands\Installation;

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
