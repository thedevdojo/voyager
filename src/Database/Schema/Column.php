<?php

namespace TCG\Voyager\Database\Schema;

use Doctrine\DBAL\Schema\Column as DoctrineColumn;
use Doctrine\DBAL\Types\Type;

class Column extends DoctrineColumn
{
    public function __construct(array $columnInfo)
    {
        $name = $columnInfo['name'];
        $type = Type::getType($columnInfo['type']);
        $options = $columnInfo['options'];

        parent::__construct($name, $type, $options);
    }

    public function __get($property)
    {
        $getter = 'get'.ucfirst($property);

        if (!method_exists($this, $getter)) {
            throw new \Exception("Property {$property} doesn't exist or is unavailable");
        }

        return $this->$getter();
    }

    public function setNull($null)
    {
        $this->setNotnull(!$null);
    }

    public function getNull()
    {
        return !$this->getNotnull();
    }

    public function getExtra()
    {
        return [
            'autoincrement' => $this->autoincrement,
        ];
    }

    public function toArray()
    {
        $info = parent::toArray();
        $info['type'] = $info['type']->getName();
        $info['null'] = $this->null;
        
        return $info;
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }
}
