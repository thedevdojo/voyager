<?php

namespace TCG\Voyager\Database\Types\Postgresql;

use TCG\Voyager\Database\Types\Common\VarCharType;

class CharacterVaryingType extends VarCharType
{
    const NAME = 'character varying';
    const DBTYPE = 'varchar';
}
