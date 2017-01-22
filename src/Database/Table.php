<?php

namespace TCG\Voyager\Database;

use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\DBAL\Schema\Table as DoctrineTable;

class Table
{
    protected $table;
    protected $originalKeys;
    protected $columns;
    protected $originalColumns;

    // todo: improve checking for existing columns
    //        improve adding new columns to the table. how can that be done?

    public function __construct($table)
    {
        if ($table instanceof DoctrineTable) {
            $this->table = $table;
        } elseif (is_string($table)) {
            $this->table = Schema::getDoctrineTable($table);
        } else {
            throw new \InvalidArgumentException('Invalid table');
        }

        if (!Schema::tableExists($this->name)) {
            throw SchemaException::tableDoesNotExist($this->name);
        }

        $this->columns = $this->setupColumns();
        $this->originalColumns = $this->setupColumns(true);
        $this->originalKeys = $this->setupKeys();
    }

    public function __get($property)
    {
        $getter = 'get'.ucfirst($property);

        if (!method_exists($this, $getter)) {
            throw new \Exception("Property {$property} doesn't exist or is unavailable");
        }

        return $this->$getter();
    }

    public function __set($property, $value)
    {
        $setter = 'set'.ucfirst($property);

        if (!method_exists($this, $setter)) {
            throw new \Exception("Property {$property} doesn't exist or is unavailable");
        }

        $this->$setter($value);
    }

    public function getName()
    {
        return $this->table->getName();
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getOriginalColumns()
    {
        return $this->originalColumns;
    }

    public function getKeys()
    {
        return $this->setupKeys();
    }

    public function getOriginalKeys()
    {
        return $this->originalKeys;
    }

    public function changeKey($column, $key)
    {
        // TODO: Add Foreign keys support?
            // hasForeignKey , getForeignKey , getForeignKeys , addForeignKeyConstraint , removeForeignKey

        if (($newKey = Key::validate($key)) === false) {
            throw new \InvalidArgumentException("Key type {$key} is invalid");
        }

        // get the current key
        if ($currentKey = $this->keys[$column]) {
            // if the key already exists, no need to re-create it
            if ($currentKey->type == $newKey) {
                return;
            }

            // Drop the current key
            $this->dropKey($currentKey);
        }

        if ($newKey) {
            // Create the new key
            $this->addKey($column, $newKey);
        }
    }

    protected function dropKey(Key $key)
    {
        switch ($key->type) {
            case 'PRI':
                $this->table->dropPrimaryKey();
                break;

            case 'UNI':
            case 'MUL':
                $this->table->dropIndex($key->name);
                break;
        }
    }

    protected function addKey($column, $key)
    {
        // NOTE: Composite keys are not supported for now
        $column = [$column];

        switch ($key) {
            case 'PRI':
                $this->table->setPrimaryKey($column);
                break;

            case 'UNI':
                $this->table->addUniqueIndex($column);
                break;

            case 'MUL':
                $this->table->addIndex($column);
                break;

            default:
                throw new \InvalidArgumentException("The key you want to add ({$key}) is invalid");
        }
    }

    public function getDoctrineColumn($column)
    {
        return $this->table->getColumn($column);
    }

    public function hasColumn($column)
    {
        return $this->table->hasColumn($column);
    }

    protected function setupColumns($clone = false)
    {
        $doctrineColumns = $clone ? $this->cloneDoctrineColumns() : $this->table->getColumns();
        $columns = [];

        foreach ($doctrineColumns as $column) {
            $column = new Column($column, $this);
            $columns[$column->name] = $column;
        }

        return $columns;
    }

    protected function cloneDoctrineColumns()
    {
        $cloned = [];

        foreach ($this->table->getColumns() as $column) {
            $cloned[] = clone $column;
        }

        return $cloned;
    }

    protected function setupKeys()
    {
        if (!$this->columns) {
            return [];
        }

        $keys = [];

        foreach ($this->columns as $column) {
            $keys[$column->name] = null;
        }

        foreach ($this->table->getIndexes() as $index) {
            $key = new Key($index);

            foreach ($key->columns as $column) {
                $keys[$column] = $key;
            }
        }

        return $keys;
    }
}
