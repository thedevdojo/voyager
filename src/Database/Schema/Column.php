<?php

namespace TCG\Voyager\Database\Schema;

use Doctrine\DBAL\Schema\Column as DoctrineColumn;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Schema\Comparator;

class Column
{
    protected $doctrineColumn;

    public function __construct(DoctrineColumn $column)
    {
        $this->doctrineColumn = $column;
    }

    public static function buildFromArray(array $column)
    {
        return new self(SchemaManager::getDoctrineColumnFromArray($column));
    }

    public static function buildFromJson($jsonColumn)
    {
        return static::buildFromArray(json_decode($jsonColumn, true));
    }

    public function __get($property)
    {
        $getter = 'get'.ucfirst($property);

        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        if (method_exists($this->doctrineColumn, $getter)) {
            return $this->doctrineColumn->$getter();
        }

        throw new \Exception("Property {$property} doesn't exist or is unavailable");
    }

    public function __call($method, array $args)
    {
        if (method_exists($this->doctrineColumn, $method)) {
            return $this->doctrineColumn->$method(...$args);
        }

        throw new \Exception("Method {$method} doesn't exist or is unavailable");
    }

    public function getDoctrineColumn()
    {
        return $this->doctrineColumn;
    }

    public function setNull($null)
    {
        $this->doctrineColumn->setNotnull(!$null);
    }

    public function getNull()
    {
        return !$this->doctrineColumn->getNotnull();
    }

    public function getExtra()
    {
        return [
            'autoincrement' => $this->autoincrement,
        ];
    }

    public function toArray()
    {
        $info = $this->doctrineColumn->toArray();
        $info['null'] = $this->null;
        
        return $info;
    }

    public function toJson()
    {
        $info = $this->toArray();
        $info['type'] = $info['type']->getName();
        return json_encode($info);
    }

    public function diff(Column $compareColumn)
    {
        return (new Comparator)->diffColumn(
            $this->doctrineColumn,
            $compareColumn->doctrineColumn
        );
    }
}
