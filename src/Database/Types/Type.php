<?php

namespace TCG\Voyager\Database\Types;

use TCG\Voyager\Database\Schema\SchemaManager;
use Doctrine\DBAL\Platforms\AbstractPlatform as DoctrineAbstractPlatform;
use Doctrine\DBAL\Types\Type as DoctrineType;
use TCG\Voyager\Database\Platforms\Platform;

abstract class Type extends DoctrineType
{
    protected static $customTypesRegistered = false;
    protected static $platformTypeMapping = [];
    protected static $allTypes = [];
    protected static $platformTypes = [];
    protected static $customTypeOptions = [];
    protected static $typeCategories = [];

    const NAME = 'UNDEFINED_TYPE_NAME';
    const NOT_SUPPORTED = 'notSupported';
    const NOT_SUPPORT_INDEX = 'notSupportIndex';

    // todo: make sure this is not overwrting DoctrineType properties

    // todo: go through laravel supported types in grammars
    // add ones that are missing from doctrine
    //    the dbtype name must match doctrine type name
    // Note: length, precision and scale need default values manually

    // make sure all types are correct.. test..
    // Next: sql server > postgres > sqlite

    public function getName()
    {
        return static::NAME;
    }

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

        static::$platformTypes = Platform::getPlatformTypes(
            $platform->getName(),
            static::getPlatformTypeMapping($platform)
        );

        static::$platformTypes = static::$platformTypes->map(function($type) {
            return static::toArray(static::getType($type));
        })->groupBy('category');

        return static::$platformTypes;
    }

    public static function getPlatformTypeMapping(DoctrineAbstractPlatform $platform)
    {
        if (static::$platformTypeMapping) {
            return static::$platformTypeMapping;
        }

        static::$platformTypeMapping = collect(
            get_protected_property($platform, 'doctrineTypeMapping')
        );

        return static::$platformTypeMapping;
    }

    public static function registerCustomPlatformTypes()
    {
        if (static::$customTypesRegistered) {
            return;
        }

        $platform = SchemaManager::getDatabasePlatform();
        $platformName = ucfirst($platform->getName());

        $customTypes = array_merge(
            static::getPlatformCustomTypes('Common'),
            static::getPlatformCustomTypes($platformName)
        );

        foreach ($customTypes as $type) {
            $name = $type::NAME;

            if (static::hasType($name)) {
                static::overrideType($name, $type);
            } else {
                static::addType($name, $type);
            }

            $platform->registerDoctrineTypeMapping($name, $name);
        }

        static::addCustomTypeOptions($platformName);

        static::$customTypesRegistered = true;
    }

    protected static function addCustomTypeOptions($platformName)
    {
        static::registerCommonCustomTypeOptions();

        Platform::registerPlatformCustomTypeOptions($platformName);
        
        // Add the custom options to the types
        foreach (static::$customTypeOptions as $option) {
            foreach ($option['types'] as $type) {
                if (static::hasType($type)) {
                    static::getType($type)->customOptions[$option['name']] = $option['value'];
                }
            }
        }
    }

    protected static function getPlatformCustomTypes($platformName)
    {
        $typesPath = __DIR__.DIRECTORY_SEPARATOR.$platformName.DIRECTORY_SEPARATOR;
        $namespace = __NAMESPACE__.'\\'.$platformName.'\\';
        $types = [];
        
        foreach (glob($typesPath.'*.php') as $classFile) {
             $types[] = $namespace.str_replace(
                '.php',
                '',
                str_replace($typesPath, '', $classFile)
            );
        }

        return $types;
    }

    public static function registerCustomOption($name, $value, $types)
    {
        if (is_string($types)) {
            $types = trim($types);

            if ($types == '*') {
                $types = static::getAllTypes()->toArray();
            } else if (strpos($types, '*') !== false) {
                $searchType = str_replace('*', '', $types);
                $types = static::getAllTypes()->filter(function($type) use ($searchType) {
                    return strpos($type, $searchType) !== false;
                })->toArray();
            } else {
                $types = [$types];
            }
        }

        static::$customTypeOptions[] = [
            'name'  => $name,
            'value' => $value,
            'types' => $types,
        ];
    }

    protected static function registerCommonCustomTypeOptions()
    {
        static::registerTypeCategories();
        static::registerTypeDefaultOptions();
    }

    protected static function registerTypeDefaultOptions()
    {
        $types = static::getTypeCategories();

        // Numbers
        static::registerCustomOption('default', [
            'type' => 'number',
            'step' => 'any',
        ], $types['numbers']);

        // Date and Time
        static::registerCustomOption('default', [
            'type' => 'date',
        ], 'date');
        static::registerCustomOption('default', [
            'type' => 'time',
            'step' => '1',
        ], 'time');
        static::registerCustomOption('default', [
            'type' => 'number',
            'min'  => '0',
        ], 'year');

        // Disable Default for unsupported types
        static::registerCustomOption('default', [
            'disabled' => true,
        ], '*text');
        static::registerCustomOption('default', [
            'disabled' => true,
        ], '*blob');
        static::registerCustomOption('default', [
            'disabled' => true,
        ], 'json');
    }

    protected static function registerTypeCategories()
    {
        $types = static::getTypeCategories();

        static::registerCustomOption('category', 'Numbers', $types['numbers']);
        static::registerCustomOption('category', 'Strings', $types['strings']);
        static::registerCustomOption('category', 'Date and Time', $types['datetime']);
        static::registerCustomOption('category', 'Lists', $types['lists']);
        static::registerCustomOption('category', 'Binary', $types['binary']);
        static::registerCustomOption('category', 'Objects', $types['objects']);
    }

    public static function getAllTypes()
    {
        if (static::$allTypes) {
            return static::$allTypes;
        }

        static::$allTypes = collect(static::getTypeCategories())->flatten();

        return static::$allTypes;
    }

    public static function getTypeCategories()
    {
        if (static::$typeCategories) {
            return static::$typeCategories;
        }

        $numbers = [
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
        ];

        $strings = [
            'char',
            'varchar',
            'string',
            'guid',
            'tinytext',
            'text',
            'mediumtext',
            'longtext',
        ];

        $datetime = [
            'date',
            'datetime',
            'year',
            'time',
            'timestamp',
            'datetimetz',
            'dateinterval',
        ];

        $lists = [
            'enum',
            'set',
            'simple_array',
            'array',
            'json',
            'jsonb',
            'json_array',
        ];

        $binary = [
            'bit',
            'binary',
            'varbinary',
            'tinyblob',
            'blob',
            'mediumblob',
            'longblob',
        ];

        $objects = [
            'object',
        ];

        static::$typeCategories = [
            'numbers'  => $numbers,
            'strings'  => $strings,
            'datetime' => $datetime,
            'lists'    => $lists,
            'binary'   => $binary,
            'objects'  => $objects
        ];

        return static::$typeCategories;
    }
}
