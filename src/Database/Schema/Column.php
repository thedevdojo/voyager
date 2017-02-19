<?php

namespace TCG\Voyager\Database\Schema;

use Doctrine\DBAL\Schema\Column as DoctrineColumn;
use Doctrine\DBAL\Types\Type as DoctrineType;
use TCG\Voyager\Database\Types\Type;

abstract class Column
{
    public static function make(array $column)
    {
        $name = Identifier::validate($column['name'], 'Column');
        $type = $column['type'];
        $type = ($type instanceof DoctrineType) ? $type : DoctrineType::getType(trim($type['name']));

        $options = array_diff_key($column, ['name' => $name, 'type' => $type]);

        return new DoctrineColumn($name, $type, $options);
    }

    /**
     * @return array
     */
    public static function toArray(DoctrineColumn $column)
    {
        $columnArray = $column->toArray();
        $columnArray['type'] = Type::toArray($columnArray['type']);
        $columnArray['oldName'] = $columnArray['name'];
        $columnArray['composite'] = false;

        return $columnArray;
    }
}
