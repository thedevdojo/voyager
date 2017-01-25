<?php

namespace TCG\Voyager\Database\Schema;

use Doctrine\DBAL\Schema\Index;

class Key
{
    protected $columns;
    protected $type;
    protected $name;
    protected $isComposite;

    const PRIMARY = 'PRIMARY';
    const UNIQUE  = 'UNIQUE';
    const INDEX   = 'INDEX';

    public function __construct(array $keyInfo)
    {
        // TODO: see if you can convert Key->toForeignKey

        $columns = $keyInfo['columns'];
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        $type = static::validateType($keyInfo['type']);
        $name = isset($keyInfo['name']) ? $keyInfo['name'] : '';

        $this->columns = $columns;
        $this->type = $type;
        $this->name = $name;
        $this->isComposite = count($columns) > 1;
    }

    public static function buildFromJson($jsonKey)
    {
        return new self(json_decode($jsonKey, true));
    }

    public static function buildFromIndex(Index $index)
    {
        $keyInfo = [
            'columns' => $index->getColumns(),
            'type'    => static::getKeyType($index),
            'name'    => $index->getName()
        ];
        
        return new self($keyInfo);
    }

    public function __get($property)
    {
        $getter = 'get'.ucfirst($property);

        if (!method_exists($this, $getter)) {
            throw new \Exception("Property {$property} doesn't exist or is unavailable");
        }

        return $this->$getter();
    }

    public function setName($name)
    {
        if(!is_string($name))
        {
            throw new \InvalidArgumentException("Invalid key name");
        }

        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getIsComposite()
    {
        return $this->isComposite;
    }

    public function toArray()
    {
        return [
            'columns'     => $this->columns,
            'type'        => $this->type,
            'name'        => $this->name,
            'isComposite' => $this->isComposite,
        ];
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }

    public function toIndex()
    {
        return new Index($this->name, $this->columns, $this->isUnique, $this->isPrimary);
    }

    public function getIsPrimary()
    {
        return $this->type == Key::PRIMARY;
    }

    public function getIsUnique()
    {
        return $this->isPrimary || ($this->type == Key::UNIQUE);
    }

    /**
     * Create a default key name for the table.
     *
     * @param  string  $table
     * @return string
     */
    public function createName($table)
    {
        if (!is_string($table)) {
            throw new \InvalidArgumentException("Invalid table name");
        }

        $keyName = strtolower($table.'_'.implode('_', $this->columns).'_'.$this->type);
        return str_replace(['-', '.'], '_', $keyName);
    }

    public static function getTypes()
    {
        return [
            static::PRIMARY,
            static::UNIQUE,
            static::INDEX
        ];
    }

    public static function validateType($type)
    {
        $type = strtoupper(trim($type));

        if (in_array($type, static::getTypes())) {
            return $type;
        }

        throw new \InvalidArgumentException("Key type {$type} is invalid.");
    }

    protected static function getKeyType(Index $index)
    {
        if ($index->isPrimary()) {
            return static::PRIMARY;
        } elseif ($index->isUnique()) {
            return static::UNIQUE;
        } else {
            return static::INDEX;
        }
    }
}
