<?php

if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        return TCG\Voyager\Facades\Voyager::setting($key, $default);
    }
}

if (!function_exists('menu')) {
    function menu($menuName, $type = null, array $options = [])
    {
        return TCG\Voyager\Facades\Voyager::model('Menu')->display($menuName, $type, $options);
    }
}

if (!function_exists('voyager_asset')) {
    function voyager_asset($path, $secure = null)
    {
        return asset(config('voyager.assets_path').'/'.$path, $secure);
    }
}



// support mutli language on frontend 
// usage {{voyagerLink('url-to-cool-thing')}}

if (!function_exists('voyagerLink')) {
    function voyagerLink($path)
    {
        $def= config('app.locale');;
        $lang= \App::getLocale();
        if($lang!=$def){
          $prefix = $lang."/";
        }else{
          $prefix = "";
        }

        if($path==""){
          if($prefix)
          return   '/'.$prefix.'/';
          else
          return '/';
         }

        return   '/'.$prefix.$path."/";
    }
}
