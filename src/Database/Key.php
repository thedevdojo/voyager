<?php

namespace TCG\Voyager\Database;

use Doctrine\DBAL\Schema\Index;

class Key
{
    protected $index;

    protected $name;
    protected $type;
    protected $columns;
    protected $isComposite;

    public function __construct(Index $index)
    {
        $this->index = $index;
        $this->name = $index->getName();
        $this->setType();
        $this->columns = $index->getColumns();
        $this->isComposite = count($this->columns) > 1;
    }

    public function __get($property)
    {
        $getter = 'get' . ucfirst($property);

        if(! method_exists($this, $getter)) {
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

    public static function validate($key)
    {
        $availableKeys = [
            '',
            'PRI',
            'UNI',
            'MUL',
        ];

        $key = strtoupper(trim($key));

        return in_array($key, $availableKeys) ? $key : false;
    }

    protected function setType()
    {
        if($this->index->isPrimary()) {
            $this->type = 'PRI';
        } else if($this->index->isUnique()) {
            $this->type = 'UNI';
        } else {
            $this->type = 'MUL';
        }
    }
}
