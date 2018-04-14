<?php

namespace TCG\Voyager\Listeners;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use TCG\Voyager\Events\TableAdded;

class CreateTableAddMigration
{
    /**
     * @var Request
     */
    private $request;

    /**
     * Create the event listener.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }


    public function handle(TableAdded $event)
    {
        $stub = $this->getStub();

        $date = now();

        $table_name = $event->table->name;

        $content = str_replace(
            'CLASS_NAME',
            $this->getClassName($table_name, $date),
            $stub
        );

        $content = str_replace(
            '//IMPORTS',
            'use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Database\Types\Type;',
            $content
        );

        $content = str_replace(
            '//MIGRATION_UP',
            $this->getMigrationUpCommand($event->table),
            $content
        );

        $content = str_replace(
            '//MIGRATION_DOWN',
            $this->getMigrationDownCommand($table_name),
            $content
        );

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

        return "VoyagerCreate{$tableName}Table";
    }

    protected function getMigrationUpCommand($table)
    {
        return "
        Type::registerCustomPlatformTypes();

        SchemaManager::createTable(unserialize('" . serialize($table) . "'));";
    }

    private function getMigrationDownCommand($table_name)
    {
        return "SchemaManager::dropTable('{$table_name}');";
    }

    private function getName($table_name, Carbon $date)
    {
        $table_name = $table_name . $date->timestamp;

        return $date->format('Y_m_d_His') . '_voyager_create_' . $table_name . '_table';
    }

    protected function getPath($name)
    {
        return base_path('/database/migrations/' . $name . '.php');
    }
}
