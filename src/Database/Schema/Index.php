<?php

namespace TCG\Voyager\Database\Schema;

use Doctrine\DBAL\Schema\Index as DoctrineIndex;

abstract class Index 
{
    const PRIMARY = 'PRIMARY';
    const UNIQUE  = 'UNIQUE';
    const INDEX   = 'INDEX';

    public static function make(array $index)
    {
        $columns = $index['columns'];
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        $isPrimary = $index['isPrimary'];
        $isUnique = $index['isUnique'];

        // Set the name
        $name = isset($index['name']) ? trim($index['name']) : '';
        if (empty($name)) {
            $table = isset($index['table']) ? $index['table'] : null;
            $name = static::createName($columns, $type, $table);
        }

        $flags = isset($index['flags']) ? $index['flags'] : [];
        $options = isset($index['options']) ? $index['options'] : [];
        
        return new DoctrineIndex($name, $columns, $isUnique, $isPrimary, $flags, $options);
    }

    /**
     * @return array
     */
    public static function toArray(DoctrineIndex $index)
    {
        return [
            'name'      => $index->getName(),
            'oldName'   => $index->getName(),
            'columns'   => $index->getColumns(),
            'type'      => static::getType($index),
            'isPrimary' => $index->isPrimary(),
            'isUnique'  => $index->isUnique(),
            'flags'     => $index->getFlags(),
            'options'   => $index->getOptions(),
        ];
    }

    public static function getType(DoctrineIndex $index)
    {
        if ($index->isPrimary()) {
            return static::PRIMARY;
        } elseif ($index->isUnique()) {
            return static::UNIQUE;
        } else {
            return static::INDEX;
        }
    }

    /**
     * Create a default index name.
     *
     * @param  array  $columns
     * @param  string  $type
     * @param  string  $table
     *
     * @return string
     */
    public static function createName(array $columns, $type, $table = null)
    {
        $table = isset($table) ? trim($table).'_' : '';
        $type = trim($type);
        $name = strtolower($table.implode('_', $columns).'_'.$type);

        return str_replace(['-', '.'], '_', $name);
    }

    public static function availableTypes()
    {
        return [
            static::PRIMARY,
            static::UNIQUE,
            static::INDEX
        ];
    }
}
