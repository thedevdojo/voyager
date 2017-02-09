<?php

namespace TCG\Voyager\Database\Types;

use TCG\Voyager\Database\Schema\SchemaManager;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type as DoctrineType;
use Illuminate\Support\Collection;

abstract class Type
{
    protected static $customTypesRegistered = false;
    protected static $platformTypeMapping = [];
    protected static $platformTypes = [];
    protected static $customTypeOptions = [];

    const NOT_SUPPORTED = 'notSupported';
    const NOT_SUPPORT_INDEX = 'notSupportIndex';

    // todo: go through laravel supported types in grammars
    // add ones that are missing from doctrine
    //    the dbtype name must match doctrine type name
    // Note: length, precision and scale need default values manually

    // make sure all types are correct.. test..
    // Next: sql server > postgres > sqlite
    public static function toArray(DoctrineType $type)
    {
        $customTypeOptions = isset($type->customOptions) ? $type->customOptions : [];

        return array_merge([
            'name' => $type->getName(),
        ], $customTypeOptions);
    }

    public static function getPlatformTypes()
    {
        if (static::$platformTypes) {
            return static::$platformTypes;
        }

        if (!static::$customTypesRegistered) {
            static::registerCustomPlatformTypes();
        }
        
        $platform = SchemaManager::getDatabasePlatform();
        $platformName = ucfirst($platform->getName());
        $getPlatformTypes = "get{$platformName}Types";

        static::$platformTypes = static::$getPlatformTypes(static::getPlatformTypeMapping($platform));

        static::$platformTypes = static::$platformTypes->map(function($type) {
            return static::toArray(DoctrineType::getType($type));
        })->groupBy('category');

        return static::$platformTypes;
    }

    public static function getPlatformTypeMapping(AbstractPlatform $platform)
    {
        if (static::$platformTypeMapping) {
            return static::$platformTypeMapping;
        }

        static::$platformTypeMapping = collect(
            get_protected_property($platform, 'doctrineTypeMapping')
        );

        return static::$platformTypeMapping;
    }

    protected static function getMysqlTypes(Collection $typeMapping)
    {
        $typeMapping->forget([
            'real',    // same as double
            'float',   // same as double
            'int',     // same as integer
            'string',  // same as varchar
            'decimal', // same as numeric
        ]);

        return $typeMapping;
    }

    protected static function registerMysqlCustomTypeOptions()
    {
        static::registerCustomOption(static::NOT_SUPPORTED, true, [
            'enum',
            'set',
        ]);

        static::registerCustomOption(static::NOT_SUPPORT_INDEX, true, [
            'tinytext',
            'text',
            'mediumtext',
            'longtext',
            'tinyblob',
            'blob',
            'mediumblob',
            'longblob',
        ]);
    }

    public static function registerCustomPlatformTypes()
    {
        if (static::$customTypesRegistered) {
            return;
        }

        $platform = SchemaManager::getDatabasePlatform();
        $platformName = ucfirst($platform->getName());
        $namespace = __NAMESPACE__.'\\'.$platformName.'\\';

        foreach (static::getPlatformCustomTypes($platformName) as $type) {
            $class = $namespace.$type;
            $name = $class::NAME;

            if (DoctrineType::hasType($name)) {
                DoctrineType::overrideType($name, $class);
            } else {
                DoctrineType::addType($name, $class);
            }

            $platform->registerDoctrineTypeMapping($name, $name);
        }

        static::registerCustomTypeOptions($platformName);

        static::$customTypesRegistered = true;
    }

    protected static function registerCustomTypeOptions($platformName)
    {
        static::registerCommonCustomTypeOptions();

        $registerPlatformCustomTypeOptions = "register{$platformName}CustomTypeOptions";
        if (method_exists(static::class, $registerPlatformCustomTypeOptions)) {
            static::$registerPlatformCustomTypeOptions();
        }
        
        // Add the custom options to the types
        foreach (static::$customTypeOptions as $option) {
            foreach ($option['types'] as $type) {
                if (DoctrineType::hasType($type)) {
                    DoctrineType::getType($type)->customOptions[$option['name']] = $option['value'];
                }
            }
        }
    }

    protected static function getPlatformCustomTypes($platformName)
    {
        $typesPath = __DIR__.DIRECTORY_SEPARATOR.$platformName.DIRECTORY_SEPARATOR;
        $types = [];
        
        foreach (glob($typesPath.'*.php') as $classFile) {
             $types[] = str_replace(
                '.php',
                '',
                str_replace($typesPath, '', $classFile)
            );
        }

        return $types;
    }

    protected static function registerCustomOption($name, $value, $types)
    {
        $types = is_array($types) ? $types : [$types];

        static::$customTypeOptions[] = [
            'name'  => $name,
            'value' => $value,
            'types' => $types,
        ];
    }

    protected static function registerCommonCustomTypeOptions()
    {
        static::registerTypeCategories();
    }

    protected static function registerTypeCategories()
    {
        static::registerCustomOption('category', 'Numbers', [
            'boolean',
            'tinyint',
            'smallint',
            'mediumint',
            'integer',
            'int',
            'bigint',
            'decimal',
            'numeric',
            'float',
            'double',
        ]);

        static::registerCustomOption('category', 'Strings', [
            'char',
            'varchar',
            'string',
            'guid',
            'tinytext',
            'text',
            'mediumtext',
            'longtext',
        ]);

        static::registerCustomOption('category', 'Date and Time', [
            'date',
            'datetime',
            'year',
            'time',
            'timestamp',
            'datetimetz',
            'dateinterval',
        ]);

        static::registerCustomOption('category', 'Lists', [
            'enum',
            'set',
            'simple_array',
            'array',
            'json',
            'json_array',
        ]);

        static::registerCustomOption('category', 'Binary', [
            'binary',
            'varbinary',
            'tinyblob',
            'blob',
            'mediumblob',
            'longblob',
        ]);

        static::registerCustomOption('category', 'Objects', [
            'object',
        ]);
    }
}
