<?php

namespace TCG\Voyager\Database\Platforms;

use Illuminate\Support\Collection;

abstract class Platform
{
    // abstract public static function getTypes(Collection $typeMapping);

    // abstract public static function registerCustomTypeOptions();

    public static function getPlatform($platformName)
    {
        $platform = __NAMESPACE__.'\\'.ucfirst($platformName);

        if (!class_exists($platform)) {
            throw new \Exception("Platform {$platformName} doesn't exist");
        }

        return $platform;
    }

    public static function getPlatformTypes($platformName, Collection $typeMapping)
    {
        $platform = static::getPlatform($platformName);

        return $platform::getTypes($typeMapping);
    }

    public static function registerPlatformCustomTypeOptions($platformName)
    {
        $platform = static::getPlatform($platformName);

        return $platform::registerCustomTypeOptions();
    }
}
