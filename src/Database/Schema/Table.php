<?php

namespace TCG\Voyager\Database\Schema;

use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Table as DoctrineTable;

class Table extends DoctrineTable
{
    public static function make($table)
    {
        if (!is_array($table)) {
            $table = json_decode($table, true);
        }

        $name = Identifier::validate($table['name'], 'Table');

        $columns = [];
        foreach ($table['columns'] as $columnArr) {
            $column = Column::make($columnArr);
            $columns[$column->getName()] = $column;
        }

        $indexes = [];
        foreach ($table['indexes'] as $indexArr) {
            $index = Index::make($indexArr);
            $indexes[$index->getName()] = $index;
        }

        $foreignKeys = [];
        foreach ($table['foreignKeys'] as $foreignKeyArr) {
            $foreignKey = ForeignKey::make($foreignKeyArr);
            $foreignKeys[$foreignKey->getName()] = $foreignKey;
        }

        $options = $table['options'];

        return new self($name, $columns, $indexes, $foreignKeys, false, $options);
    }

    public function diff(DoctrineTable $compareTable)
    {
        return (new Comparator())->diffTable($this, $compareTable);
    }

    public function diffOriginal()
    {
        return (new Comparator())->diffTable(SchemaManager::getDoctrineTable($this->_name), $this);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'name'           => $this->_name,
            'oldName'        => $this->_name,
            'columns'        => $this->exportColumnsToArray(),
            'indexes'        => $this->exportIndexesToArray(),
            'primaryKeyName' => $this->_primaryKeyName,
            'foreignKeys'    => $this->exportForeignKeysToArray(),
            'options'        => $this->_options,
        ];
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * @return array
     */
    public function exportColumnsToArray()
    {
        $exportedColumns = [];

        foreach ($this->getColumns() as $name => $column) {
            $exportedColumns[] = Column::toArray($column);
        }

        return $exportedColumns;
    }

    /**
     * @return array
     */
    public function exportIndexesToArray()
    {
        $exportedIndexes = [];

        foreach ($this->getIndexes() as $name => $index) {
            $indexArr = Index::toArray($index);
            $indexArr['table'] = $this->_name;
            $exportedIndexes[] = $indexArr;
        }

        return $exportedIndexes;
    }

    /**
     * @return array
     */
    public function exportForeignKeysToArray()
    {
        $exportedForeignKeys = [];

        foreach ($this->getForeignKeys() as $name => $fk) {
            $exportedForeignKeys[$name] = ForeignKey::toArray($fk);
        }

        return $exportedForeignKeys;
    }

    public function __get($property)
    {
        $getter = 'get'.ucfirst($property);

        if (!method_exists($this, $getter)) {
            throw new \Exception("Property {$property} doesn't exist or is unavailable");
        }

        return $this->$getter();
    }
}
