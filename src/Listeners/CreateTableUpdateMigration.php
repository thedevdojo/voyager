<?php

namespace TCG\Voyager\Listeners;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use TCG\Voyager\Events\TableUpdated;

class CreateTableUpdateMigration
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Create the event listener.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }


    public function handle(TableUpdated $event)
    {
        $stub = $this->getStub();

        $date = now();

        $table = $event->name;
        $table_name = $table['name'];

        $originalTable = $event->originalTable;

        $content = str_replace(
            'CLASS_NAME',
            $this->getClassName($table_name, $date),
            $stub
        );

        $content = str_replace(
            '//IMPORTS',
            'use TCG\Voyager\Database\DatabaseUpdater;',
            $content
        );

        $content = str_replace(
            '//MIGRATION_UP',
            $this->getMigrationUpCommand($table),
            $content
        );

        $content = str_replace(
            '//MIGRATION_DOWN',
            $this->getMigrationDownCommand($originalTable),
            $content
        );

        // $updater = new DatabaseUpdater($tableOriginal->toArray());
        //dd($updater->updateTable());
        $name = $this->getName($table_name, $date);

        $this->filesystem->put($this->getPath($name), $content);

        $migration = DB::table('migrations');

        $batch = (int)$migration->max('batch') + 1;

        $migration->insert([
            'migration' => $name,
            'batch'     => $batch
        ]);
    }

    /**
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getStub()
    {
        return $this->filesystem->get(VOYAGER_PATH . '/stubs/migration.stub');
    }

    protected function getClassName($tableName, Carbon $date)
    {
        $tableName = Str::studly($tableName) . $date->timestamp;

        return "VoyagerUpdate{$tableName }Table";
    }

    private function getMigrationUpCommand($table)
    {
        return "DatabaseUpdater::update(unserialize('" . serialize($table) . "'));";
    }

    private function getMigrationDownCommand($table)
    {
        return "DatabaseUpdater::update(unserialize('" . serialize($table) . "'));";
    }

    private function getName($table_name, Carbon $date)
    {
        $table_name = $table_name . $date->timestamp;

        return $date->format('Y_m_d_His') . '_voyager_update_' . $table_name . '_table';
    }

    protected function getPath($name)
    {
        return base_path('/database/migrations/' . $name . '.php');
    }
}
