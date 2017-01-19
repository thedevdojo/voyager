<?php

namespace TCG\Voyager\Http\Controllers\Traits;

use Exception;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use TCG\Voyager\Facades\DBSchema;

trait DatabaseUpdate
{
    use DatabaseQueryBuilder;

    /**
     * These column types cannot be renamed. A custom SQL query is necessary to make those updates.
     *
     * @var array
     */
    private $renameBlacklist = [
        //'enum',
    ];

    /**
     * Build a collection containing each column's info.
     *
     * @param Request $request
     *
     * @return Collection
     */
    private function buildColumnsCollection(Request $request, Collection $existingColumns = null)
    {
        $existingColumns = isset($existingColumns) ? $existingColumns : collect();

        $columns = collect();

        if (isset($request->field)) {
            foreach ($request->field as $index => $field) {
                // If a column has been destroyed, just skip it and move on to the next column.
                if ((bool) $request->delete_field[$index]) {
                    continue;
                }

                $columns->push([
                        'field'    => $field,
                        'type'     => $request->type[$index],
                        'enum'     => $request->enum[$index],
                        'nullable' => (bool) $request->nullable[$index],
                        'key'      => $request->key[$index],
                        'default'  => $request->default[$index],
                        'exists'   => $existingColumns->has($field),
                        'original' => (object) $existingColumns->get($field),
                ]);
            }
        }

        return $columns;
    }

    /**
     * Rename table if new table name given.
     *
     * @param string $originalName
     * @param string $tableName
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function renameTable($originalName, $tableName)
    {
        if (!empty($originalName) && $originalName != $tableName) {
            try {
                Schema::rename($originalName, $tableName);
            } catch (Exception $e) {
                return back()->withMessage('Exception: '.$e->getMessage())->with('alert-type', 'error');
            }
        }
    }

    /**
     * Rename table columns.
     *
     * @param Request $request
     * @param string  $tableName
     */
    private function renameColumns(Request $request, $tableName)
    {
        if (isset($request->field)) {
            foreach ($request->field as $index => $column) {
                // If the column type matches something from the blacklist, then we just need to move on to the next column.
                if (in_array($request->type[$index], $this->renameBlacklist)) {
                    continue;
                }

                $originalRow    = $request->original_row[$index];
                $originalColumn = isset($originalRow) ? $originalRow->field : '';

                // If the name of the column has changed rename it.
                if ($originalColumn && $originalColumn != $column) {
                    Schema::table($tableName, function (Blueprint $table) use ($originalColumn, $column) {
                        $table->renameColumn($originalColumn, $column);
                    });
                }
            }
        }
    }

    /**
     * Drop table column.
     *
     * @param Request $request
     * @param string  $tableName
     */
    private function dropColumns(Request $request, $tableName)
    {
        if (isset($request->delete_field)) {
            foreach ($request->delete_field as $index => $delete) {
                // If the column is set for destruction, then by all means, destroy it!
                if ((bool) $delete) {
                    $columnName = $request->field[$index];

                    Schema::table($tableName, function (Blueprint $table) use ($columnName) {
                        $table->dropColumn($columnName);
                    });
                }
            }
        }
    }

    /**
     * Update or add any new columns.
     *
     * @param Request $request
     * @param string  $tableName
     */
    private function updateColumns(Request $request, $tableName)
    {
        $existingColumns = DBSchema::describeTable($tableName)->keyBy('field');
        $columnsCollection = $this->buildColumnsCollection($request, $existingColumns);
        $columnQueries = $this->buildQuery($columnsCollection);

        Schema::table($tableName, function (Blueprint $table) use ($columnQueries, $request, $existingColumns) {
            foreach ($columnQueries as $index => $query) {
                $field = $request->field[$index];

                if ($existingColumns->has($field)) {
                    $query($table)->change();

                    continue;
                }

                // If we get here, it means that this is a new table column. So let's create it.
                $query($table);
            }
        });
    }

    /**
     * Check if column needs to be updated.
     *
     * @param array $column
     *
     * @return array What needs to be updated.
     */
    private function whatToUpdate($column) {
        // implement this
    }
}
