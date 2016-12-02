<?php

namespace TCG\Voyager\Http\Controllers\Traits;

use DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
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
        'enum',
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
     * @param Request         $request
     * @param Collection|null $existingColumns
     *
     * @return Collection
     */
    private function buildQuery(Request $request, $existingColumns = null)
    {
        return $this->buildColumnsCollection($request)
            ->map(
                function ($column) use ($existingColumns) {
                    // We need to check that an existing database table column in now being
                    // updated. If it is, we also need to check that the supplied column
                    // type can actually be update without throwing an annoying error.
                    if ($existingColumns && $existingColumns->has($column['field']) &&
                        in_array($column['type'], $this->typeBlacklist)
                    ) {
                        return false;
                    }

                    return function (Blueprint $table) use ($column) {
                        if ($column['key'] == 'PRI') {
                            return $table->increments($column['field']);
                        }

                        if ($column['field'] == 'created_at & updated_at') {
                            return $table->timestamps();
                        }

                        $type = $column['type'] ?: 'string';

                        $result = $type == 'enum'
                            ? $table->enum($column['field'], [$column['enum']])
                            : $table->{$type}($column['field']);

                        if ($column['key'] == 'UNI') {
                            $result->unique();
                        }

                        $result->nullable($column['nullable']);

                        if ($column['default']) {
                            $result->default($column['default']);
                        }

                        return $result;
                    };
                }
            )
            ->filter();
    }

    /**
     * Describe given table.
     *
     * @param string $table
     *
     * @return Collection
     */
    private function describeTable($table)
    {
        $schema_name = DB::connection()->getDatabaseName();
        $raw = "SELECT column_name    AS 'field',
                       column_type    AS 'type',
                       is_nullable    AS 'null',
                       column_key     AS 'key',
                       column_default AS 'default',
                       extra          AS 'extra'
                FROM   information_schema.columns
                WHERE  table_schema = '{$schema_name}'
                AND    table_name = '{$table}'";

        return collect(DB::select(DB::raw($raw)));
    }

    /**
     * Build a collection containing each column's info.
     *
     * @param Request $request
     *
     * @return Collection
     */
    private function buildColumnsCollection(Request $request)
    {
        $columns = collect();

        foreach ($request->field as $index => $field) {
            // If a column has been destroyed, just skip it and move on to the next column.
            if ((bool) $request->delete_field[$index]) {
                continue;
            }

            $columns->push(
                [
                    'field'    => $field,
                    'type'     => $request->type[$index],
                    'enum'     => $request->enum[$index],
                    'nullable' => (bool) $request->nullable[$index],
                    'key'      => $request->key[$index],
                    'default'  => $request->default[$index],
                ]
            );
        }

        return $columns;
    }
}
