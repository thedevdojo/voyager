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
    // the goal is to make it simple in the Vue component
    //    the dbtype name must match doctrine type name
    //   we just need to choose the type and not worry about length, fixed etc...


    // Note: length, precision and scale need default values manually

    // todo: categorize the types: maybe create a category for all types?

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

        $platform = SchemaManager::getDatabasePlatform();

        if (!static::$customTypesRegistered) {
            static::registerCustomPlatformTypes();
        }
        
        $platformName = ucfirst($platform->getName());
        $getPlatformTypes = "get{$platformName}Types";

        static::$platformTypes = static::$getPlatformTypes(static::getPlatformTypeMapping($platform));

        static::$platformTypes = static::$platformTypes->map(function($type) {
            return static::toArray(DoctrineType::getType($type));
        })->values();

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

        dd(static::getPlatformTypes());

        // Set custom type options
        // todo: move this to registerCustomTypeOptions
        static::registerCommonCustomTypeOptions();

        $registerPlatformCustomTypeOptions = "register{$platformName}CustomTypeOptions";
        if (method_exists(static::class, $registerPlatformCustomTypeOptions)) {
            static::$registerPlatformCustomTypeOptions();
        }
        
        foreach (static::$customTypeOptions as $option) {
            foreach ($option['types'] as $type) {
                if (DoctrineType::hasType($type)) {
                    DoctrineType::getType($type)->customOptions[$option['name']] = $option['value'];
                }
            }
        }

        static::$customTypesRegistered = true;
    }

    public static function getPlatformCustomTypes($platformName)
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

    protected static function getMysqlCustomTypes()
    {
        return [
            'TinyInt',
            'MediumInt',
            'Double',
            'Numeric',
            'Char',
            'VarChar',
            'TinyText',
            'Text',
            'MediumText',
            'LongText',
            'Year',
            'Timestamp',
            'Binary',
            'VarBinary',
            'TinyBlob',
            'Blob',
            'MediumBlob',
            'LongBlob',
            'Json',
            'Enum',
            'Set',
        ];
    }

    protected static function registerCustomOption($name, $value, $types)
    {
        $types = is_array($types) ? $types : [$types];

        // todo: what if tje $value is a callback???

        static::$customTypeOptions[] = [
            'name'  => $name,
            'value' => $value,
            'types' => $types,
        ];
    }

    protected static function registerCommonCustomTypeOptions()
    {
        static::registerCustomOption(static::NOT_SUPPORTED, true, [
            'enum',
            'set',
        ]);

        static::registerTypeCategories();
    }

    protected static function registerMysqlCustomTypeOptions()
    {
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

        static::registerCustomOption('allowed', [], [
            'enum',
            'set',
        ]);
    }

    protected static function registerTypeCategories()
    {
        static::registerCustomOption('category', 'Numbers', [
            'boolean',
            'smallint',
            'integer',
            'bigint',
            'decimal',
            'float',
        ]);

        static::registerCustomOption('category', 'Strings', [
            'string',
            'text',
            'guid',
        ]);

        static::registerCustomOption('category', 'Date and Time', [
            'date',
            'datetime',
            'time',
            'datetimetz',
            'dateinterval',
        ]);

        static::registerCustomOption('category', 'Lists', [
            'enum',
            'simple_array',
            'array',
            'json',
            'json_array',
        ]);

        static::registerCustomOption('category', 'Binary', [
            'binary',
            'blob',
        ]);

        static::registerCustomOption('category', 'Objects', [
            'object',
        ]);
    }
}
