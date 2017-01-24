<?php

namespace TCG\Voyager\Database\Schema;

use Doctrine\DBAL\Schema\Table as DoctrineTable;
use Doctrine\DBAL\Schema\Comparator;

class Table extends DoctrineTable
{
    public function __get($property)
    {
        $getter = 'get'.ucfirst($property);

        if (!method_exists($this, $getter)) {
            throw new \Exception("Property {$property} doesn't exist or is unavailable");
        }

        return $this->$getter();
    }

    public function addColumn($columnName, $typeName, array $options = [])
    {
        $columnInfo = [
            'name' => $columnName,
            'type' => $typeName,
            'options' => $options
        ];

        $column = new Column($columnInfo);

        $this->_addColumn($column);

        return $column;
    }

    public function addExistingColumn(Column $column, $clone = true)
    {
        if ($clone) {
            $column = clone $column;
        }

        $this->_addColumn($column);

        return $column;
    }

    public function diff(Table $compareTable)
    {
        return (new Comparator)->diffTable($this, $compareTable);
    }

    public function diffOriginal()
    {
        if ($this->isNew()) {
            return false;
        }

        return (new Comparator)->diffTable(SchemaManager::listTableDetails($this->name), $this);
    }

    public function getKeys()
    {
        $keys = [];

        foreach ($this->getIndexes() as $index) {
            $keys[] = Key::getKey($index);
        }

        return $keys;
    }

    public function getKeyByColumns(array $columns)
    {
        foreach ($this->getIndexes() as $index) {
            if ($columns == $index->getColumns()) {
                return Key::getKey($index);
            }
        }
    }

    public function addKey(Key $key)
    {
        switch ($key->type) {
            case Key::PRIMARY:
                return $this->setPrimaryKey($key->columns);

            case Key::UNIQUE:
                return $this->addUniqueIndex($key->columns);

            case Key::INDEX:
                return $this->addIndex($key->columns);
        }
    }

    public function dropKey(Key $key)
    {
        switch ($key->type) {
            case Key::PRIMARY:
                $this->dropPrimaryKey();
                break;

            case Key::UNIQUE:
            case Key::INDEX:
                $this->dropIndex($key->name);
                break;
        }
    }

    public function isNew()
    {
        return !SchemaManager::tableExists($this->name);
    }
}
