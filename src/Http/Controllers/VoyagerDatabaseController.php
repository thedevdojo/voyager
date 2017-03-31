<?php

namespace TCG\Voyager\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use TCG\Voyager\Database\DatabaseUpdater;
use TCG\Voyager\Database\Schema\Column;
use TCG\Voyager\Database\Schema\Identifier;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Database\Schema\Table;
use TCG\Voyager\Database\Types\Type;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Permission;

class VoyagerDatabaseController extends Controller
{
    public function index()
    {
        Voyager::canOrFail('browse_database');

        $dataTypes = Voyager::model('DataType')->select('id', 'name')->get()->pluck('id', 'name')->toArray();

        $tables = array_map(function ($table) use ($dataTypes) {
            $table = [
                'name'          => $table,
                'dataTypeId'    => isset($dataTypes[$table]) ? $dataTypes[$table] : null,
            ];

            return (object) $table;
        }, SchemaManager::listTableNames());

        return view('voyager::tools.database.index')->with(compact('dataTypes', 'tables'));
    }

    public function create()
    {
        Voyager::canOrFail('browse_database');

        $db = $this->prepareDbManager('create');

        return view('voyager::tools.database.edit-add', compact('db'));
    }

    public function store(Request $request)
    {
        Voyager::canOrFail('browse_database');

        try {
            Type::registerCustomPlatformTypes();

            $table = Table::make($request->table);
            SchemaManager::createTable($table);

            if (isset($request->create_model) && $request->create_model == 'on') {
                $modelNamespace = config('voyager.models.namespace', app()->getNamespace());
                $params = [
                    'name' => $modelNamespace.Str::studly(Str::singular($table->name)),
                ];

                // if (in_array('deleted_at', $request->input('field.*'))) {
                //     $params['--softdelete'] = true;
                // }

                if (isset($request->create_migration) && $request->create_migration == 'on') {
                    $params['--migration'] = true;
                }

                Artisan::call('voyager:make:model', $params);
            } elseif (isset($request->create_migration) && $request->create_migration == 'on') {
                Artisan::call('make:migration', [
                    'name'    => 'create_'.$table->name.'_table',
                    '--table' => $table->name,
                ]);
            }

            return redirect()
               ->route('voyager.database.edit', $table->name)
               ->with($this->alertSuccess("Successfully created {$table->name} table"));
        } catch (Exception $e) {
            return back()->with($this->alertException($e))->withInput();
        }
    }

    public function edit($table)
    {
        Voyager::canOrFail('browse_database');

        if (!SchemaManager::tableExists($table)) {
            return redirect()
                ->route('voyager.database.index')
                ->with($this->alertError("The table you want to edit doesn't exist"));
        }

        $db = $this->prepareDbManager('update', $table);

        return view('voyager::tools.database.edit-add', compact('db'));
    }

    /**
     * Update database table.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        Voyager::canOrFail('browse_database');

        $table = json_decode($request->table, true);

        try {
            DatabaseUpdater::update($table);
            // TODO: synch BREAD with Table
            // $this->cleanOldAndCreateNew($request->original_name, $request->name);
        } catch (Exception $e) {
            return back()->with($this->alertException($e))->withInput();
        }

        return redirect()
               ->route('voyager.database.edit', $table['name'])
               ->with($this->alertSuccess("Successfully updated {$table['name']} table"));
    }

    protected function prepareDbManager($action, $table = '')
    {
        $db = new \stdClass();

        // Need to get the types first to register custom types
        $db->types = Type::getPlatformTypes();

        if ($action == 'update') {
            $db->table = SchemaManager::listTableDetails($table);
            $db->formAction = route('voyager.database.update', $table);
        } else {
            $db->table = new Table('New Table');

            // Add prefilled columns
            $db->table->addColumn('id', 'integer', [
                'unsigned'      => true,
                'notnull'       => true,
                'autoincrement' => true,
            ]);

            $db->table->setPrimaryKey(['id'], 'primary');

            $db->formAction = route('voyager.database.store');
        }

        $oldTable = old('table');
        $db->oldTable = $oldTable ? $oldTable : json_encode(null);
        $db->action = $action;
        $db->identifierRegex = Identifier::REGEX;
        $db->platform = SchemaManager::getDatabasePlatform()->getName();

        return $db;
    }

    public function cleanOldAndCreateNew($originalName, $tableName)
    {
        if (!empty($originalName) && $originalName != $tableName) {
            $dt = DB::table('data_types')->where('name', $originalName);
            if ($dt->get()) {
                $dt->delete();
            }

            $perm = DB::table('permissions')->where('table_name', $originalName);
            if ($perm->get()) {
                $perm->delete();
            }

            $params = ['name' => Str::studly(Str::singular($tableName))];
            Artisan::call('voyager:make:model', $params);
        }
    }

    public function reorder_column(Request $request)
    {
        Voyager::canOrFail('browse_database');

        if ($request->ajax()) {
            $table = $request->table;
            $column = $request->column;
            $after = $request->after;
            if ($after == null) {
                // SET COLUMN TO THE TOP
                DB::query("ALTER $table MyTable CHANGE COLUMN $column FIRST");
            }

            return 1;
        }

        return 0;
    }

    public function show($table)
    {
        Voyager::canOrFail('browse_database');

        return response()->json(SchemaManager::describeTable($table));
    }

    public function destroy($table)
    {
        Voyager::canOrFail('browse_database');

        try {
            SchemaManager::dropTable($table);

            return redirect()
                ->route('voyager.database.index')
                ->with($this->alertSuccess("Successfully deleted $table table"));
        } catch (Exception $e) {
            return back()->with($this->alertException($e));
        }
    }

    /********** BREAD METHODS **********/

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addBread(Request $request, $table)
    {
        Voyager::canOrFail('browse_database');

        $data = $this->prepopulateBreadInfo($table);
        $data['fieldOptions'] = SchemaManager::describeTable($table);

        return view('voyager::tools.database.edit-add-bread', $data);
    }

    private function prepopulateBreadInfo($table)
    {
        $displayName = Str::singular(implode(' ', explode('_', Str::title($table))));
        $modelNamespace = config('voyager.models.namespace', app()->getNamespace());
        if (empty($modelNamespace)) {
            $modelNamespace = app()->getNamespace();
        }

        return [
            'isModelTranslatable'  => true,
            'table'                => $table,
            'slug'                 => Str::slug($table),
            'display_name'         => $displayName,
            'display_name_plural'  => Str::plural($displayName),
            'model_name'           => $modelNamespace.Str::studly(Str::singular($table)),
            'generate_permissions' => true,
            'server_side'          => false,
        ];
    }

    public function storeBread(Request $request)
    {
        Voyager::canOrFail('browse_database');

        try {
            $dataType = Voyager::model('DataType');
            $data = $dataType->updateDataType($request->all(), true)
                ? $this->alertSuccess('Successfully created new BREAD')
                : $this->alertError('Sorry it appears there may have been a problem creating this BREAD');

            return redirect()->route('voyager.database.index')->with($data);
        } catch (Exception $e) {
            return redirect()->route('voyager.database.index')->with($this->alertException($e, 'Saving Failed'));
        }
    }

    public function addEditBread($table)
    {
        Voyager::canOrFail('browse_database');

        $dataType = Voyager::model('DataType')->whereName($table)->first();

        try {
            $fieldOptions = isset($dataType) ? $dataType->fieldOptions() : SchemaManager::describeTable($dataType->name);
        } catch (Exception $e) {
            $fieldOptions = SchemaManager::describeTable($dataType->name);
        }

        $isModelTranslatable = isBreadTranslatable($dataType);

        return view('voyager::tools.database.edit-add-bread', compact('dataType', 'fieldOptions', 'isModelTranslatable'));
    }

    public function updateBread(Request $request, $id)
    {
        Voyager::canOrFail('browse_database');

        /* @var \TCG\Voyager\Models\DataType $dataType */
        try {
            $dataType = Voyager::model('DataType')->find($id);

            // Prepare Translations and Transform data
            $translations = isBreadTranslatable($dataType)
                ? $dataType->prepareTranslations($request)
                : [];

            $data = $dataType->updateDataType($request->all(), true)
                ? $this->alertSuccess("Successfully updated the {$dataType->name} BREAD")
                : $this->alertError('Sorry it appears there may have been a problem updating this BREAD');

            // Save translations if applied
            $dataType->saveTranslations($translations);

            return redirect()->route('voyager.database.index')->with($data);
        } catch (Exception $e) {
            return back()->with($this->alertException($e, 'Update Failed'));
        }
    }

    public function deleteBread($id)
    {
        Voyager::canOrFail('browse_database');

        /* @var \TCG\Voyager\Models\DataType $dataType */
        $dataType = Voyager::model('DataType')->find($id);

        // Delete Translations, if present
        if (isBreadTranslatable($dataType)) {
            $dataType->deleteAttributeTranslations($dataType->getTranslatableAttributes());
        }

        $data = Voyager::model('DataType')->destroy($id)
            ? $this->alertSuccess("Successfully removed BREAD from {$dataType->name}")
            : $this->alertError('Sorry it appears there was a problem removing this BREAD');

        if (!is_null($dataType)) {
            Voyager::model('Permission')->removeFrom($dataType->name);
        }

        return redirect()->route('voyager.database.index')->with($data);
    }
}
