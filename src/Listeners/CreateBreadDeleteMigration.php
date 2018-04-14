<?php

namespace TCG\Voyager\Listeners;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use TCG\Voyager\Events\BreadDeleted;

class CreateBreadDeleteMigration
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


    public function handle(BreadDeleted $event)
    {
        $stub = $this->getStub();

        $date = now();

        $dataType = $event->dataType;
        $table_name = $dataType->name;

        $content = str_replace(
            'CLASS_NAME',
            $this->getClassName($table_name, $date),
            $stub
        );

//        $content = str_replace(
//            '//IMPORTS',
//            'use TCG\Voyager\Database\Schema\SchemaManager;
        //use TCG\Voyager\Database\Types\Type;',
//            $content
//        );

        $content = str_replace(
            '//MIGRATION_UP',
            $this->getMigrationUpCommand($dataType->id),
            $content
        );
//
//        $content = str_replace(
//            '//MIGRATION_DOWN',
//            $this->getMigrationDownCommand($table),
//            $content
//        );

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

        return "VoyagerDelete{$tableName}Bread";
    }

    private function getMigrationUpCommand($id)
    {
        return '
        $dataType = Voyager::model("DataType")->find(' . $id . ');

        if (is_bread_translatable($dataType)) {
            $dataType->deleteAttributeTranslations($dataType->getTranslatableAttributes());
        }

        Voyager::model("DataType")->destroy(' . $id . ');
        
        Voyager::model("Permission")->removeFrom($dataType->name);
        ';
    }

    private function getName($table_name, Carbon $date)
    {
        $table_name = $table_name . $date->timestamp;

        return $date->format('Y_m_d_His') . '_voyager_delete_' . $table_name . '_bread';
    }

    protected function getPath($name)
    {
        return base_path('/database/migrations/' . $name . '.php');
    }

    private function getMigrationDownCommand($table)
    {
        return "//TODO";
    }
}
