<?php

namespace TCG\Voyager\Http\Controllers\Traits;

trait DatabaseUpdate
{
    /**
     * Rename table if new table name given
     *
     * @param string $originalName
     * @param string $tableName
     */
    private function renameTable($originalName, $tableName)
    {
        if (! empty($originalName) && $originalName != $tableName) {
            try {
                Schema::rename($originalName, $tableName);
            } catch (Exception $e) {
                return back()->with(
                    [
                        'message'    => 'Exception: ' . $e->getMessage(),
                        'alert-type' => 'error',
                    ]
                );
            }
        }
    }
}
