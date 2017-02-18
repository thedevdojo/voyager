<?php

namespace TCG\Voyager\Database;

use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\DBAL\Schema\Table as DoctrineTable;

class Table
{
    protected $table;
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
        $keys = [];

        foreach ($this->columns as $column) {
            if ($key = $column->key) {
                foreach ($key->columns as $keyColumn) {
                    $keys[$keyColumn] = $key;
                }
            }
        }

        return $keys;
    }

    public function getOriginalKeys()
    {
        $keys = [];

        foreach ($this->originalColumns as $column) {
            if ($key = $column->key) {
                foreach ($key->columns as $keyColumn) {
                    $keys[$keyColumn] = $key;
                }
            }
        }

        return $keys;
    }

    public function getColumnKey($column, $refreshKey = false)
    {
        // Note: this doesn't support composite keys for now
        if (is_array($column)) {
            $column = $column[0];
        }

        // If the column already has a key setup, just return it
        if (!$refreshKey && $this->columns && ($key = $this->columns[$column]->key)) {
            return $key;
        }

        // Try to setup or refresh a key
        if ($index = $this->getColumnIndex($column)) {
            return new Key($index);
        }

        // If we reach here, it means the column has no key
        return null;
    }

    public function changeKey($column, $key)
    {
        // TODO: Add Foreign keys support?
            // hasForeignKey , getForeignKey , getForeignKeys , addForeignKeyConstraint , removeForeignKey

        if (($newKey = Key::validate($key)) === false) {
            throw new \InvalidArgumentException("Key type {$key} is invalid");
        }

        // get the current key
        if ($currentKey = $this->getColumnKey($column)) {
            // if the key already exists, no need to re-create it
            if ($currentKey->type == $newKey) {
                return $currentKey;
            }

            // Drop the current key
            $this->dropKey($currentKey);
        }

        if ($newKey) {
            // Create the new key
            return $this->addKey($column, $newKey);
        }

        // if no new key is specified, return null
        // this means the user wants to only drop the key
        return null;
    }

    protected function dropKey(Key $key)
    {
        switch ($key->type) {
            case Key::PRIMARY:
                $this->table->dropPrimaryKey();
                break;

            case Key::UNIQUE:
            case Key::INDEX:
                $this->table->dropIndex($key->name);
                break;
        }
    }

    protected function addKey($column, $key)
    {
        // NOTE: Composite keys are not supported for now
        if(! is_array($column)) {
            $column = [$column];
        }

        switch ($key) {
            case Key::PRIMARY:
                $this->table->setPrimaryKey($column);
                break;

            case Key::UNIQUE:
                $this->table->addUniqueIndex($column);
                break;

            case Key::INDEX:
                $this->table->addIndex($column);
                break;

            default:
                throw new \InvalidArgumentException("The key you want to add ({$key}) is invalid");
        }

        return $this->getColumnKey($column, true);
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

    protected function getColumnIndex($column)
    {
        // this currently doesn't support Composite keys
        if(! is_array($column)) {
            $column = [$column];
        }
        
        foreach ($this->table->getIndexes() as $index) {
            if ($column == $index->getColumns()) {
                return $index;
            }
        }
    }
}
