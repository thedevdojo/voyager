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

    public function __get($property)
    {
        $getter = 'get'.ucfirst($property);

        if (!method_exists($this, $getter)) {
            throw new \Exception("Property {$property} doesn't exist or is unavailable");
        }

        return $this->$getter();
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

    public static function getKey(Index $index)
    {
        $keyInfo = [
            'columns' => $index->getColumns(),
            'type'    => static::getKeyType($index),
            'name'    => $index->getName()
        ];
        
        return new self($keyInfo);
    }

    public static function validateType($type)
    {
        $type = strtoupper(trim($type));

        if (in_array($type, static::getTypes())) {
            return $type;
        }

        throw new \InvalidArgumentException("Key type {$type} is invalid.");
    }

    public static function getKeyType(Index $index)
    {
        if ($index->isPrimary()) {
            return static::PRIMARY;
        } elseif ($index->isUnique()) {
            return static::UNIQUE;
        } else {
            return static::INDEX;
        }
    }

    public static function getTypes()
    {
        return [
            static::PRIMARY,
            static::UNIQUE,
            static::INDEX
        ];
    }
}
