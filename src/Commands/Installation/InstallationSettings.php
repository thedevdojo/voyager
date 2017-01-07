<?php

namespace TCG\Voyager\Commands\Installation;

class InstallationSettings
{
	public static routes() {
		return "\n\nRoute::group(['prefix' => 'admin'], function () {\n    Voyager::routes();\n});\n";
	}
}
