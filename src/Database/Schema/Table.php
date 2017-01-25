<?php

namespace TCG\Voyager\Database\Schema;

use Doctrine\DBAL\Schema\Table as DoctrineTable;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\DBAL\Schema\Comparator;

class Table
{
    protected $doctrineTable;
    protected $columns;
    protected $keys;

    public function __construct(DoctrineTable $table)
    {
        $this->doctrineTable = $table;
        $this->setupColumns();
        $this->setupKeys();
    }

    public static function buildFromArray(array $table)
    {
        $name = $table['name'];
        $doctrineColumns = static::getDoctrineColumnsFromArray($table['columns']);
        $indexes = static::getIndexesFromKeysArray($table['keys']);
        $foreignKeys = []; // todo: deal with this

        return new self(
            new DoctrineTable($name, $doctrineColumns, $indexes, $foreignKeys, false, [])
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
        $this->keys = [];
        $this->setupKeys();
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

    public function diff(Table $compareTable)
    {
        return (new Comparator)->diffTable(
            $this->doctrineTable,
            $compareTable->doctrineTable
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

    public function getKeys()
    {
        return $this->keys;
    }

    public function getKeyByColumns($columns)
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        foreach ($this->keys as $key) {
            if ($columns == $key->columns) {
                return $key;
            }
        }
    }

    public function addKey(Key $key)
    {
        $key->setName($key->createName($this->name));

        switch ($key->type) {
            case Key::PRIMARY:
                $this->doctrineTable->setPrimaryKey($key->columns, $key->name);
                break;

            case Key::UNIQUE:
                $this->doctrineTable->addUniqueIndex($key->columns, $key->name);
                break;

            case Key::INDEX:
                $this->doctrineTable->addIndex($key->columns, $key->name);
                break;
        }

        $this->keys[$key->name] = $key;
    }

    public function dropKey(Key $key)
    {
        switch ($key->type) {
            case Key::PRIMARY:
                $this->doctrineTable->dropPrimaryKey();
                break;

            case Key::UNIQUE:
            case Key::INDEX:
                $this->doctrineTable->dropIndex($key->name);
                break;
        }

        unset($this->keys[$key->name]);
    }

    public function hasKey($name)
    {
        return isset($this->keys[$name]);
    }

    public function isNew()
    {
        return !SchemaManager::tableExists($this->name);
    }

    public function toArray()
    {
        return [
            'name'    => $this->name,
            'columns' => $this->columnsToArray(),
            'keys'    => $this->keysToArray(),
        ];
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }

    protected function keysToArray()
    {
        $keys = [];

        foreach ($this->keys as $key) {
            $keyArray = $key->toArray();
            $keys[$keyArray['name']] = $keyArray;
        }

        return $keys;
    }

    protected function keysToIndexes()
    {
        $indexes = [];

        foreach ($this->keys as $key) {
            $indexes[$key->name] = $key->toIndex();
        }

        return $indexes;
    }

    protected function indexesToArray()
    {
        $indexes = [];

        foreach ($this->doctrineTable->getIndexes() as $name => $index) {
            $indexes[$name] = $this->indexToArray($index);
        }

        return $indexes;
    }

    protected function indexToArray(Index $index)
    {
        return [
            'name'      => $index->getName(),
            'columns'   => $index->getColumns(),
            'isUnique'  => $index->isUnique(),
            'isPrimary' => $index->isPrimary(),
            'flags'     => $index->getFlags(),
            'options'   => $index->getOptions()
        ];
    }

    protected function columnsToArray()
    {
        $columns = [];

        foreach ($this->columns as $column) {
            $columnArray = $column->toArray();
            $columnArray['type'] = $columnArray['type']->getName();
            $columns[$columnArray['name']] = $columnArray;
        }

        return $columns;
    }

    protected function setupColumns()
    {
        foreach ($this->doctrineTable->getColumns() as $column) {
            $this->columns[$column->getName()] = new Column($column);
        }
    }

    protected function setupKeys()
    {
        foreach ($this->doctrineTable->getIndexes() as $index) {
            $key = Key::buildFromIndex($index);
            $this->keys[$key->name] = $key;
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

    protected static function getIndexesFromKeysArray(array $keys)
    {
        $indexes = [];

        foreach ($keys as $key) {
            $indexes[$key['name']] = (new Key($key))->toIndex();
        }

        return $indexes;
    }
}
