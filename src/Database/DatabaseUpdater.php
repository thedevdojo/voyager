<?php

namespace TCG\Voyager\Database;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\TableDiff;
use TCG\Voyager\Database\Schema\Table;
use TCG\Voyager\Database\Schema\SchemaManager;

class DatabaseUpdater
{
    protected $tableArr;
    protected $table;
    protected $originalTable;

    public function __construct(array $tableArr)
    {
        $this->table = Table::make($tableArr);
        $this->tableArr = $tableArr;
        $this->originalTable = SchemaManager::listTableDetails($tableArr['oldName']);
    }

    /**
     * Update or create the table.
     *
     * @return void|bool
     */
    public static function update(array $table)
    {
        // if the table is new, create it
        if (!SchemaManager::tableExists($table['oldName'])) {
            return SchemaManager::createTable($table);
        }

        $updater = new self($table);
        
        return $updater->updateTable();
    }

    /**
     * Updates the table.
     *
     * @return bool
     */
    public function updateTable()
    {
        // Rename columns and indexes
        if ($renamedDiff = $this->getRenamedDiff()) {
            SchemaManager::alterTable($renamedDiff);

            // Refresh original table after renaming the columns and indexes
            $this->originalTable = SchemaManager::listTableDetails($this->tableArr['oldName']);
        }

        $tableDiff = $this->originalTable->diff($this->table);

        // Add new table name to tableDiff
        if ($this->table->getName() != $this->originalTable->getName()) {
            if (!$tableDiff) {
                $tableDiff = new TableDiff($this->tableArr['oldName']);
            }

            $tableDiff->newName = $this->table->getName();
        }

        // Update the table
        if ($tableDiff) {
            SchemaManager::alterTable($tableDiff);
        }

        // if there is no difference after the update, it means it was successful
        return !$this->table->diffOriginal();
    }

    /**
     * Get the table diff to rename columns and indexes.
     *
     * @return \Doctrine\DBAL\Schema\TableDiff
     */
    protected function getRenamedDiff()
    {
        $renamedDiff = new TableDiff($this->tableArr['oldName']);

        foreach ($this->getRenamedColumns() as $oldName => $newName) {
            $renamedDiff->renamedColumns[$oldName] = $this->table->getColumn($newName);
        }

        foreach ($this->getRenamedIndexes() as $oldName => $newName) {
            $renamedDiff->renamedIndexes[$oldName] = $this->table->getIndex($newName);
        }

        return $renamedDiff;
    }

    /**
     * Get columns that were renamed.
     *
     * @return array
     */
    protected function getRenamedColumns()
    {
        $renamedColumns = [];

        foreach ($this->tableArr['columns'] as $column) {
            $oldName = $column['oldName'];

            // make sure this is an existing column and not a new one
            if ($this->originalTable->hasColumn($oldName)) {
                $name = $column['name'];

                if ($name != $oldName) {
                    $renamedColumns[$oldName] = $name;
                }
            }
        }

        return $renamedColumns;
    }

    /**
     * Get indexes that were renamed.
     *
     * @return array
     */
    protected function getRenamedIndexes()
    {
        $renamedIndexes = [];

        foreach ($this->tableArr['indexes'] as $index) {
            $oldName = $index['oldName'];

            // make sure this is an existing index and not a new one
            if ($this->originalTable->hasIndex($oldName)) {
                $name = $index['name'];

                if ($name != $oldName) {
                    $renamedIndexes[$oldName] = $name;
                }
            }
        }

        return $renamedIndexes;
    }
}
