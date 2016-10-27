<?php

namespace TCG\Voyager;

use TCG\Voyager\Models\Setting;
use \Storage;

class Voyager
{

	/**
     *  Singleton Voyager Class
     */

	private static $instance;
    
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        
        return static::$instance;
    }

    protected function __construct(){}
    private function __clone(){}
    private function __wakeup(){}

    /**
     *  End Singleton operators
     */

    public static function setting($key, $default = NULL){
    	$setting = Setting::where('key', '=', $key)->first();
    	if(isset($setting->id)){
    		return $setting->value;
    	}
    	return $default;
    }

    public static function image($file, $default = ''){
    	if( !empty($file) && Storage::exists(config('voyager.storage.subfolder') . $file) ){
	      return Storage::url(config('voyager.storage.subfolder') . $file);
	    }
	   	
	   	return $default;
    }

}