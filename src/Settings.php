<?php

namespace TCG\Voyager;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use TCG\Voyager\Classes\Setting;

class Settings
{
    protected $path;
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
            $this->path = $path;
        }

        return $this->path;
    }

    public function getSettings()
    {
        return $this->settings;
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

        File::put($this->path, $content);
    }

    public function loadSettings()
    {
        $folder = dirname($this->path);
        if (!File::isDirectory($folder)) {
            File::makeDirectory($folder, 0755, true);
        }
        if (!File::exists($this->path)) {
            File::put($this->path, '[]');
        }

        $this->settings = collect(json_decode(File::get($this->path)));
    }
}
