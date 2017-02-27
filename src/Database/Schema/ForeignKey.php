<?php

namespace TCG\Voyager\Database\Schema;

use Doctrine\DBAL\Schema\ForeignKeyConstraint as DoctrineForeignKey;

abstract class ForeignKey
{
    public static function make(array $foreignKey)
    {
        // Set the local table
        $localTable = null;
        if (isset($foreignKey['localTable'])) {
            $localTable = SchemaManager::getDoctrineTable($foreignKey['localTable']);
        }

        $localColumns = $foreignKey['localColumns'];
        $foreignTable = $foreignKey['foreignTable'];
        $foreignColumns = $foreignKey['foreignColumns'];
        $options = isset($foreignKey['options']) ? $foreignKey['options'] : [];

        // Set the name
        $name = isset($foreignKey['name']) ? trim($foreignKey['name']) : '';
        if (empty($name)) {
            $table = isset($localTable) ? $localTable->getName() : null;
            $name = Index::createName($localColumns, 'foreign', $table);
        } else {
            $name = Identifier::validate($name, 'Foreign Key');
        }

        $doctrineForeignKey = new DoctrineForeignKey(
            $localColumns, $foreignTable, $foreignColumns, $name, $options
        );

        if (isset($localTable)) {
            $doctrineForeignKey->setLocalTable($localTable);
        }

        return $doctrineForeignKey;
    }

    /**
     * @return array
     */
    public static function toArray(DoctrineForeignKey $fk)
    {
        return [
            'name'           => $fk->getName(),
            'localTable'     => $fk->getLocalTableName(),
            'localColumns'   => $fk->getLocalColumns(),
            'foreignTable'   => $fk->getForeignTableName(),
            'foreignColumns' => $fk->getForeignColumns(),
            'options'        => $fk->getOptions(),
        ];
    }
}
