<?php

namespace TCG\Voyager;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use TCG\Voyager\Classes\Setting;
use TCG\Voyager\Facades\Voyager as VoyagerFacade;

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

    public function setting($key = null, $default = null, $translate = true)
    {
        $this->loadSettings();
        $settings = $this->settings;

        if (Str::contains($key, '.')) {
            // We are looking for a setting in a group
            list($group, $key) = explode('.', $key);
            $settings = $settings->where('group', $group)->where('key', $key);
        } else if ($key !== '' && $key !== null) {
            // We are looking for a setting without a group OR all group-settings
            $group = $settings->where('group', null)->where('key', $key);

            if ($group->count() == 0) {
                $settings = $settings->where('group', $key);
            } else {
                $settings = $group;
            }
        }

        // Modify collection and only include key/value pairs
        $settings = $settings->mapWithKeys(function ($setting) use ($translate, $default) {
            $key = $setting->key;
            if ($setting->group !== null && $setting->group !== '') {
                $key = implode('.', [$setting->group, $setting->key]);
            }
            if ($translate) {
                return [$key => VoyagerFacade::translate($setting->value, app()->getLocale(), config('app.fallback_locale')) ?? $default];
            }

            return [$key => $setting->value ?? $default];
        });

        if ($settings->count() == 0) {
            return $default;
        } else if ($settings->count() == 1) {
            $settings = $settings->first();
        }

        return $settings;
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
