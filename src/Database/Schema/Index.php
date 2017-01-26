<?php

namespace TCG\Voyager\Database\Schema;

use Doctrine\DBAL\Schema\Index as DoctrineIndex;

class Index 
{
    protected $doctrineIndex;
    protected $type;

    const PRIMARY = 'PRIMARY';
    const UNIQUE  = 'UNIQUE';
    const INDEX   = 'INDEX';

    public function __construct(DoctrineIndex $index)
    {
        $this->doctrineIndex = $index;

        // Set type
        if ($index->isPrimary()) {
            $this->type = static::PRIMARY;
        } elseif ($index->isUnique()) {
            $this->type = static::UNIQUE;
        } else {
            $this->type = static::INDEX;
        }
    }

    public function __get($property)
    {
        $getter = 'get'.ucfirst($property);

        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        if (method_exists($this->doctrineIndex, $getter)) {
            return $this->doctrineIndex->$getter();
        }

        throw new \Exception("Property {$property} doesn't exist or is unavailable");
    }

    public function __call($method, array $args)
    {
        if (method_exists($this->doctrineIndex, $method)) {
            return $this->doctrineIndex->$method(...$args);
        }

        throw new \Exception("Method {$method} doesn't exist or is unavailable");
    }

    public static function buildFromArray(array $index)
    {
        return new self(SchemaManager::getDoctrineIndexFromArray($index));
    }

    public static function buildFromJson($jsonIndex)
    {
        return static::buildFromArray(json_decode($jsonIndex, true));
    }

    public function getDoctrineIndex()
    {
        return $this->doctrineIndex;
    }

    public function getType()
    {
        return $this->type;
    }

    public function toArray()
    {
        return [
            'type'    => $this->type,
            'name'    => $this->doctrineIndex->getName(),
            'columns' => $this->doctrineIndex->getColumns(),
            'flags'   => $this->doctrineIndex->getFlags(),
            'options' => $this->doctrineIndex->getOptions(),
        ];
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Create a default index name.
     *
     * @param  array  $columns
     * @param  string  $type
     * @param  string  $table
     *
     * @return string
     */
    public static function createName(array $columns, $type, $table = null)
    {
        $type = static::validateType($type);
        $table = isset($table) ? "{$table}_" : '';
        $name = strtolower($table.implode('_', $columns).'_'.$type);

        return str_replace(['-', '.'], '_', $name);
    }

    public static function availableTypes()
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

        if (in_array($type, static::availableTypes())) {
            return $type;
        }

        throw new \InvalidArgumentException("Index type {$type} is invalid.");
    }
}
