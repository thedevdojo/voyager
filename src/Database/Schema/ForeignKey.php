<?php

namespace TCG\Voyager\Database\Schema;

use Doctrine\DBAL\Schema\ForeignKeyConstraint as DoctrineForeignKey;

class ForeignKey
{
    protected $doctrineForeignKey;

    public function __construct(DoctrineForeignKey $foreignKey)
    {
        $this->doctrineForeignKey = $foreignKey;
    }

    public static function buildFromArray(array $foreignKey)
    {
        return new self(SchemaManager::getDoctrineForeignKeyFromArray($foreignKey));
    }

    public function __get($property)
    {
        $getter = 'get'.ucfirst($property);

        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        if (method_exists($this->doctrineForeignKey, $getter)) {
            return $this->doctrineForeignKey->$getter();
        }

        throw new \Exception("Property {$property} doesn't exist or is unavailable");
    }

    public function __call($method, array $args)
    {
        if (method_exists($this->doctrineForeignKey, $method)) {
            return $this->doctrineForeignKey->$method(...$args);
        }

        throw new \Exception("Method {$method} doesn't exist or is unavailable");
    }

    public function toArray()
    {
        return [
            'name'           => $this->doctrineForeignKey->getName(),
            'localTable'     => $this->doctrineForeignKey->getLocalTableName(),
            'localColumns'   => $this->doctrineForeignKey->getLocalColumns(),
            'foreignTable'   => $this->doctrineForeignKey->getForeignTableName(),
            'foreignColumns' => $this->doctrineForeignKey->getForeignColumns(),
            'options'        => $this->doctrineForeignKey->getOptions(),
        ];
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }
}
