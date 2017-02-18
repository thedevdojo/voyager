<?php

namespace TCG\Voyager\Http\Controllers\Traits;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;

trait DatabaseQueryBuilder
{
    /**
     * These column types cannot be updated. A custom SQL query is necessary to make those updates.
     *
     * @var array
     */
    private $typeBlacklist = [
        'char',
        'double',
        //'enum',
        'ipAddress',
        'json',
        'jsonb',
        'macAddress',
        'mediumIncrements',
        'mediumInteger',
        'morphs',
        'nullableTimestamps',
        'softDeletes',
        'timestamp',
        'timestamps',
        'timestampsTz',
        'timestampTz',
        'timeTz',
        'tinyInteger',
        'unsignedMediumInteger',
        'unsignedTinyInteger',
        'uuid',
    ];

    /**
     * Build the queries necessary for creating/updating tables.
     *
     * @param Collection $columns
     *
     * @return Collection
     */
    private function buildQuery(Collection $columns)
    {
        return $columns->map(function ($column) {
            // We need to check that an existing database table column in now being
            // updated. If it is, we also need to check that the supplied column
            // type can actually be update without throwing an annoying error.
            if ($column['exists'] && in_array($column['type'], $this->typeBlacklist)) {
                return false;
            }

            return function (Blueprint $table) use ($column) {
                if ($column['key'] == 'PRI') {
                    return $table->increments($column['field']);
                }

                if ($column['field'] == 'created_at & updated_at') {
                    return $table->timestamps();
                }

                if ($column['field'] == 'deleted_at') {
                    return $table->softDeletes();
                }

                $type = $column['type'] ?: 'string';

                $result = $type == 'enum' ? $table->enum(
                    $column['field'],
                    array_map('trim', explode(',', $column['enum']))
                ) : $table->{$type}($column['field']);

                if (($column['key'] == 'UNI')) {
                    // Only add a unique index if:
                    //  this is a new column
                    //  or an existing column that doesn't already have a unique index !('UNI' || 'PRI')
                    if ((!$column['exists']) ||
                        (($originalKey = $column['original']->key) != 'UNI') && ($originalKey != 'PRI')) {
                        $result->unique();
                    }
                }

                // TODO: handle columns that change their index
                // dropUniqe() and dropPrimary()
                // Add handling fot other types of Indexes

                $result->nullable($column['nullable']);

                if ($column['default']) {
                    $result->default($column['default']);
                }

                return $result;
            };
        })->filter();
    }
}
