<?php

namespace TCG\Voyager\Database\Schema;

use Doctrine\DBAL\Schema\Column as DoctrineColumn;
use Doctrine\DBAL\Types\Type;

abstract class Column
{
    protected static $availableTypes;
    protected static $doctrineTypeCategories = [];
    protected static $platformTypes = [];

    public static function make(array $column)
    {
        $name = Identifier::validate($column['name'], 'Column');
        $type = $column['type'];
        $type = ($type instanceof Type) ? $type : Type::getType(trim($type));

        $options = array_diff_key($column, ['name' => $name, 'type' => $type]);

        return new DoctrineColumn($name, $type, $options);
    }

    /**
     * @return array
     */
    public static function toArray(DoctrineColumn $column)
    {
        $columnArray = $column->toArray();
        $columnArray['type'] = $columnArray['type']->getName();
        $columnArray['oldName'] = $columnArray['name'];

        return $columnArray;
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        // Add custom names to be used for ambiguous types (simple_array etc...)
        //   maybe do this in the view?
        // Note:
        //  special column and index types are determined by things like:
        //     Length, Fixed, Flags etc...
        // Add link to Doctrine types documentation for more info..
        if (static::$availableTypes) {
            return static::$availableTypes;
        }

        $availablePlatformTypes = static::getPlatformTypes();
        $typeCategories = static::getDoctrineTypeCategories();

        // We'll categorize the types
        foreach ($availablePlatformTypes as $type) {
            $category = $typeCategories[$type];

            static::$availableTypes[$category][] = $type;
        }

        return static::$availableTypes;
    }

    protected static function getPlatformTypes()
    {
        if (static::$platformTypes) {
            return static::$platformTypes;
        }

        static::$platformTypes = array_unique(
            array_values(SchemaManager::getDatabasePlatformTypes())
        );

        return static::$platformTypes;
    }

    protected static function getDoctrineTypeCategories()
    {
        if (static::$doctrineTypeCategories) {
            return static::$doctrineTypeCategories;
        }

        static::setupDoctrineTypeCategories();

        return static::$doctrineTypeCategories;
    }

    protected static function setupDoctrineTypeCategories()
    {
        $numbers = [
            'boolean',
            'smallint',
            'integer',
            'bigint',
            'decimal',
            'float',
        ];

        $strings = [
            'string',
            'text',
            'guid',
        ];

        $dateTime = [
            'date',
            'datetime',
            'time',
            'datetimetz',
            'dateinterval',
        ];

        $lists = [
            'enum',
            'simple_array',
            'array',
            'json',
        ];

        $binary = [
            'binary',
            'blob',
        ];

        $objects = [
            'object',
        ];

        $categories = [
            'Numbers' => $numbers,
            'Strings' => $strings,
            'Date and Time' => $dateTime,
            'Lists'   => $lists,
            'Binary' => $binary,
            'Objects' => $objects,
        ];

        foreach ($categories as $category => $types) {
            foreach ($types as $type) {
                static::$doctrineTypeCategories[$type] = $category;
            }
        }
    }
}
