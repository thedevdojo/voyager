<?php

namespace TCG\Voyager\Http\Controllers\Traits;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

trait DatabaseUpdate
{
    use DatabaseQueryBuilder;

    /**
     * These column types cannot be renamed. A custom SQL query is necessary to make those updates.
     *
     * @var array
     */
    private $renameBlacklist = [
        'enum',
    ];

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
                return back()
                    ->withMessage('Exception: '.$e->getMessage())
                    ->with('alert-type', 'error');
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
        foreach ($request->field as $index => $column) {
            // If the column type matches something from the blacklist, then we just need to move on to the next column.
            if (in_array($request->type[$index], $this->renameBlacklist)) {
                continue;
            }

            $originalColumn = $request->original_field[$index];

            // If the name of the column has changed rename it.
            if ($originalColumn && $originalColumn != $column) {
                Schema::table(
                    $tableName,
                    function (Blueprint $table) use ($originalColumn, $column) {
                        $table->renameColumn($originalColumn, $column);
                    }
                );
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
        foreach ($request->delete_field as $index => $delete) {
            // If the column is set for destruction, then by all means, destroy it!
            if ((bool) $delete) {
                $columnName = $request->field[$index];

                Schema::table(
                    $tableName,
                    function (Blueprint $table) use ($columnName) {
                        $table->dropColumn($columnName);
                    }
                );
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
        $existingColumns = $this->describeTable($tableName)->keyBy('field');
        $columnQueries = $this->buildQuery($request, $existingColumns);

        Schema::table(
            $tableName,
            function (Blueprint $table) use ($columnQueries, $request, $existingColumns) {
                foreach ($columnQueries as $index => $query) {
                    $field = $request->field[$index];

                    if ($existingColumns->has($field)) {
                        $query($table)->change();

                        continue;
                    }

                    // If we get here, it means that this is a new table column. So let's create it.
                    $query($table);
                }
            }
        );
    }
}
