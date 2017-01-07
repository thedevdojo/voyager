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
	public static routes() {
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
}
