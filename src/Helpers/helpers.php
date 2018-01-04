<?php

declare(strict_types=1);

if (!\function_exists('setting')) {
    function setting($key, $default = null)
    {
        $setting = Cache::rememberForever('settings.'.$key, function () {
            return TCG\Voyager\Facades\Voyager::setting($key, null);
        });

        if ($setting === null) {
            return $default;
        }

        return $setting;
    }
}

if (!\function_exists('menu')) {
    function menu($menuName, $type = null, array $options = [])
    {
        return TCG\Voyager\Facades\Voyager::model('Menu')->display($menuName, $type, $options);
    }
}

if (!\function_exists('voyager_asset')) {
    function voyager_asset($path, $secure = null)
    {
        return asset(config('voyager.assets_path').'/'.$path, $secure);
    }
}
