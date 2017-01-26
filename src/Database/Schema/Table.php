<?php

namespace TCG\Voyager\Database\Schema;

use Doctrine\DBAL\Schema\Table as DoctrineTable;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\DBAL\Schema\Comparator;

class Table
{
    protected $doctrineTable;
    protected $columns;
    protected $indexes;
    protected $foreignKeys;

    public function __construct($table)
    {
        if (!($table instanceof DoctrineTable)) {
            $table = SchemaManager::getDoctrineTable($table);
        }

        $this->doctrineTable = $table;
        $this->setupColumns();
        $this->setupIndexes();
        $this->setupForeignKeys();
    }

    public static function buildFromArray(array $table)
    {
        $name = $table['name'];
        $doctrineColumns = static::getDoctrineColumnsFromArray($table['columns']);
        $doctrineIndexes = static::getDoctrineIndexesFromArray($table['indexes']);
        $foreignKeys = static::getDoctrineForeignKeysFromArray($table['foreignKeys']);

        return new self(
            new DoctrineTable($name, $doctrineColumns, $doctrineIndexes, $foreignKeys, false, [])
        );
    }

    public static function buildFromJson($jsonTable)
    {
        return static::buildFromArray(json_decode($jsonTable, true));
    }

    public function __get($property)
    {
        $getter = 'get'.ucfirst($property);

        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        if (method_exists($this->doctrineTable, $getter)) {
            return $this->doctrineTable->$getter();
        }

        throw new \Exception("Property {$property} doesn't exist or is unavailable");
    }

    public function __call($method, array $args)
    {
        if (method_exists($this->doctrineTable, $method)) {
            return $this->doctrineTable->$method(...$args);
        }

        throw new \Exception("Method {$method} doesn't exist or is unavailable");
    }

    public function __clone()
    {
        $this->doctrineTable = clone $this->doctrineTable;
        $this->columns = [];
        $this->setupColumns();
        $this->indexes = [];
        $this->setupIndexes();
        $this->foreignKeys = [];
        $this->setupForeignKeys();
    }

    public function getDoctrineTable()
    {
        return $this->doctrineTable;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getColumn($columnName)
    {
        if (!$this->hasColumn($columnName)) {
            throw SchemaException::columnDoesNotExist($columnName, $this->name);
        }

        return $this->columns[$columnName];
    }

    public function addColumn($columnName, $typeName, array $options = [])
    {
        $column = new Column(
            $this->doctrineTable->addColumn($columnName, $typeName, $options)
        );

        $this->columns[$column->name] = $column;

        return $column;
    }

    public function dropColumn($columnName)
    {
        $this->doctrineTable->dropColumn($columnName);
        unset($this->columns[$columnName]);

        return $this;
    }

    public function diff($compareTable)
    {
        if ($compareTable instanceof self) {
            $compareTable = $compareTable->doctrineTable;
        } elseif (!($compareTable instanceof DoctrineTable)) {
            $compareTable = SchemaManager::getDoctrineTable($compareTable);
        }

        return (new Comparator)->diffTable(
            $this->doctrineTable,
            $compareTable
        );
    }

    public function diffOriginal()
    {
        if ($this->isNew()) {
            return false;
        }

        return (new Comparator)->diffTable(
            SchemaManager::getDoctrineTable($this->name),
            $this->doctrineTable
        );
    }

    public function getIndexes()
    {
        return $this->indexes;
    }

    public function getIndexByColumns($columns)
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        foreach ($this->indexes as $index) {
            if ($columns == $index->columns) {
                return $index;
            }
        }
    }

    public function addIndex(Index $index)
    {
        switch ($index->type) {
            case Index::PRIMARY:
                $this->doctrineTable->setPrimaryKey($index->columns, $index->name);
                break;

            case Index::UNIQUE:
                $this->doctrineTable->addUniqueIndex($index->columns, $index->name);
                break;

            case Index::INDEX:
                $this->doctrineTable->addIndex($index->columns, $index->name);
                break;
        }

        $this->indexes[$index->name] = $index;
    }

    public function dropIndex(Index $index)
    {
        // TODO: change this to take Index or Index->name
        //        detect if it's a primary key
        //  Add dropPrimaryKey()
        switch ($index->type) {
            case Index::PRIMARY:
                $this->doctrineTable->dropPrimaryKey();
                break;

            case Index::UNIQUE:
            case Index::INDEX:
                $this->doctrineTable->dropIndex($index->name);
                break;
        }

        unset($this->indexes[$index->name]);
    }

    public function addForeignKey()
    {
        // TODO
        // set the name to foreign index
    }

    public function isNew()
    {
        return !SchemaManager::tableExists($this->name);
    }

    public function toArray()
    {
        return [
            'name'        => $this->name,
            'columns'     => $this->exportColumnsToArray(),
            'indexes'     => $this->exportIndexesToArray(),
            'foreignKeys' => $this->exportForeignKeysToArray(),
        ];
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }

    protected function exportColumnsToArray()
    {
        $columns = [];

        foreach ($this->columns as $name => $column) {
            $columnArray = $column->toArray();
            $columnArray['type'] = $columnArray['type']->getName();
            $columns[$name] = $columnArray;
        }

        return $columns;
    }

    protected function exportIndexesToArray()
    {
        $indexes = [];

        foreach ($this->indexes as $name => $index) {
            $indexArray = $index->toArray();
            $indexArray['table'] = $this->name;
            $indexes[$name] = $indexArray;
        }

        return $indexes;
    }

    protected function exportForeignKeysToArray()
    {
        $foreignKeys = [];

        foreach ($this->foreignKeys as $name => $foreignKey) {
            $foreignKeys[$name] = $foreignKey->toArray();
        }

        return $foreignKeys;
    }

    protected function setupColumns()
    {
        foreach ($this->doctrineTable->getColumns() as $column) {
            $this->columns[$column->getName()] = new Column($column);
        }
    }

    protected function setupIndexes()
    {
        foreach ($this->doctrineTable->getIndexes() as $name => $index) {
            $this->indexes[$name] = new Index($index);
        }
    }

    protected function setupForeignKeys()
    {
        foreach ($this->doctrineTable->getForeignKeys() as $name => $foreignKey) {
            $this->foreignKeys[$name] = new ForeignKey($foreignKey);
        }
    }

    protected static function getDoctrineColumnsFromArray(array $columns)
    {
        $doctrineColumns = [];

        foreach ($columns as $column) {
            $doctrineColumn = SchemaManager::getDoctrineColumnFromArray($column);
            $doctrineColumns[$doctrineColumn->getName()] = $doctrineColumn;
        }

        return $doctrineColumns;
    }

    protected static function getDoctrineIndexesFromArray(array $indexes)
    {
        $doctrineIndexes = [];

        foreach ($indexes as $index) {
            $doctrineIndex = SchemaManager::getDoctrineIndexFromArray($index);
            $doctrineIndexes[$doctrineIndex->getName()] = $doctrineIndex;
        }

        return $doctrineIndexes;
    }

    protected static function getDoctrineForeignKeysFromArray(array $foreignKeys)
    {
        $doctrineForeignKeys = [];

        foreach ($foreignKeys as $foreignKey) {
            $doctrineForeignKey = SchemaManager::getDoctrineForeignKeyFromArray($foreignKey);
            $doctrineForeignKeys[$doctrineForeignKey->getName()] = $doctrineForeignKey;
        }

        return $doctrineForeignKeys;
    }
}
