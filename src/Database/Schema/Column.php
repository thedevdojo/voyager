<?php

namespace TCG\Voyager\Database\Schema;

use Doctrine\DBAL\Schema\Column as DoctrineColumn;
use Doctrine\DBAL\Types\Type;

abstract class Column
{
    public static function make(array $column)
    {
        $name = $column['name'];
        $type = $column['type'];
        $type = ($type instanceof Type) ? $type : Type::getType($type);
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
        return array_keys(Type::getTypesMap());
    }
}
