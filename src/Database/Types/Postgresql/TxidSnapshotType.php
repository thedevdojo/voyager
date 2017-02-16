<?php

namespace TCG\Voyager\Database\Types\Postgresql;

use TCG\Voyager\Database\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class TxidSnapshotType extends Type
{
    const NAME = 'txid_snapshot';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'txid_snapshot';
    }
}
