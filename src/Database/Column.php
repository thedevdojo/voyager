<?php

namespace TCG\Voyager\Database;

use Doctrine\DBAL\Schema\Column as DoctrineColumn;
use Doctrine\DBAL\Types\Type;

class Column
{
    protected $column;
    protected $originalName;
    protected $newName;

    protected $table;

    public function __construct($column, $table)
    {
        if (!($table instanceof Table)) {
            $table = new Table($table);
        }

        $this->table = $table;

        // Get the doctrine column instance
        if ($column instanceof self) {
            $this->column = clone $column->getDoctrineColumn();
        } elseif ($column instanceof DoctrineColumn) {
            $this->column = $column;
        } elseif (is_string($column)) {
            $this->column = $table->getDoctrineColumn($column);
        } elseif (is_array($column)) {
            $name = $column['name'];
            $originalName = isset($column['originalName']) ? $column['originalName'] : $name;
            $type = Type::getType($column['type']);
            $extra = $column['extra'];
            $options = [
                'notnull'       => !$column['null'],
                'default'       => $column['default'],
                'autoincrement' => $extra['autoincrement'],
            ];

            $this->column = new DoctrineColumn($name, $type, $options);
        } else {
            throw new \InvalidArgumentException('Invalid column');
        }

        $this->originalName = isset($originalName) ? $originalName : $this->name;

        if ($this->name != $this->originalName) {
            $this->newName = $this->name;
        }
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

    public function getDoctrineColumn()
    {
        return $this->column;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function setKey($key)
    {
        $this->table->changeKey($this->name, $key);
    }

    public function setName($name)
    {
        if ($name != $this->name) {
            $this->newName = $name;
        }
    }

    public function getName()
    {
        return isset($this->newName) ? $this->newName : $this->column->getName();
    }

    public function getOriginalName()
    {
        return $this->originalName;
    }

    public function getType()
    {
        return $this->column->getType()->getName();
    }

    public function getNull()
    {
        return !$this->column->getNotNull();
    }

    public function getKey()
    {
        return $this->table->keys[$this->originalName];
    }

    public function getOriginalKey()
    {
        return $this->table->originalKeys[$this->originalName];
    }

    public function getDefault()
    {
        return $this->column->getDefault();
    }

    public function getAutoincrement()
    {
        return $this->column->getAutoincrement();
    }

    public function getExtra()
    {
        return [
            'autoincrement' => $this->autoincrement,
        ];
    }

    public function getOriginal()
    {
        if (isset($this->table->originalColumns[$this->originalName])) {
            return $this->table->originalColumns[$this->originalName];
        }
    }

    public function isNew()
    {
        return !$this->table->hasColumn($this->originalName);
    }

    public function diffOriginal()
    {
        if ($this->isNew()) {
            throw new \Exception("Column {$this->name} is a new column");
        }

        return $this->diff($this->getOriginal());
    }

    public function diff(Column $column)
    {
        // return diff between this and column
        // so only perform necessary actions based on what changed
        $properties = [
            'name',
            'type',
            'null',
            'default',
            'key',
            'autoincrement',
        ];

        // NOTE: try to figure out how key is compared since $this->key will get updated keys and not original ones...
        // test it

        $diff = [];

        foreach ($properties as $property) {
            if ($this->$property != $column->$property) {
                $diff[$property] = [
                    'current' => $this->$property,
                    'compare' => $column->$property,
                ];
            }
        }

        return $diff;
    }
}
