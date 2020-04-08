<?php

namespace TCG\Voyager;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use TCG\Voyager\Classes\Setting;

class Settings
{
    protected $settingsPath;
    protected $settings = null;

    /**
     * Sets the path where the settings-file is stored.
     *
     * @param string $path
     *
     * @return string the current path
     */
    public function settingsPath($path = null)
    {
        if ($path) {
            $this->settingsPath = $path;
        }

        return $this->settingsPath;
    }

    public function setting($key = null, $translate = true, $default = null)
    {
        
    }

    public function saveSettings($content)
    {
        $this->loadSettings(); // Load settings so the file is available
        if (!is_string($content)) {
            $content = json_encode($content, JSON_PRETTY_PRINT);
        }

        File::put($this->settingsPath, $content);
    }

    public function loadSettings($input = null)
    {
        
    }
}
