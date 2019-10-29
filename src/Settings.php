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
        // setting('key'); returns a setting without group and this key OR a whole group with this name
        // setting('group.key') returns a setting in this group and with this key
        // setting() returns all settings
        $this->loadSettings();
        $this->settings->each(function ($setting) use ($translate) {
            $setting->translate = $translate;
        });

        if ($key) {
            if (!Str::contains($key, '.')) {
                // Return a setting without group and this key OR a whole group with this name
                $setting = $this->settings->filter(function ($setting) use ($key) {
                    return !$setting->group && $setting->key == $key;
                })->first();
                if ($setting) {
                    return $setting->value ?? $default;
                }

                return $this->settings->filter(function ($setting) use ($key, $default) {
                    return $setting->group == $key;
                })->mapWithKeys(function ($setting) use ($default) {
                    return [$setting->key => $setting->value ?? $default];
                })->toArray();
            } else {
                list($group, $key) = explode('.', $key);
                $settings = $this->settings->where('group', $group)->where('key', $key);

                return $settings->first()->value ?? $default;
            }
        } elseif (is_null($key)) {
            // Return all settings with key-value pairs
            $settings = [];
            $this->settings->each(function ($setting) use (&$settings, $default) {
                if ($setting->group) {
                    $settings[$setting->group][$setting->key] = $setting->value ?? $default;
                } else {
                    $settings[$setting->key] = $setting->value ?? $default;
                }
            });

            return $settings;
        }

        return $this->settings;
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
        if (!$this->settings || $input) {
            $json = [];
            if (!$input) {
                $folder = dirname($this->settingsPath);
                if (!File::isDirectory($folder)) {
                    File::makeDirectory($folder);
                }
                if (!File::exists($this->settingsPath)) {
                    File::put($this->settingsPath, '{}');
                }

                $json = @json_decode(File::get($this->settingsPath));
            } else {
                $json = $input;
            }
            $this->settings = collect();

            if (json_last_error() == JSON_ERROR_NONE) {
                foreach ($json as $setting) {
                    $this->settings->push(new Setting($setting));
                }
            }
        }
    }
}
