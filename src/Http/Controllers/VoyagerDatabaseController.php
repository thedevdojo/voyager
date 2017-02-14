<?php

namespace TCG\Voyager\Http\Controllers;

use Exception;
use Illuminate\Console\AppNamespaceDetectorTrait;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\DBSchema;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Permission;
use TCG\Voyager\Voyager;
use TCG\Voyager\Database\DatabaseUpdater;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Database\Schema\Table;
use TCG\Voyager\Database\Schema\Column;
use TCG\Voyager\Database\Schema\Identifier;
use TCG\Voyager\Database\Types\Type;

class VoyagerDatabaseController extends Controller
{
    use AppNamespaceDetectorTrait;

    public function index()
    {
        Voyager::can('browse_database');

        $dataTypes = DataType::select('id', 'name')->get()->pluck('id', 'name')->toArray();

        $tables = array_map(function ($table) use ($dataTypes) {
            $table = [
                'name'          => $table,
                'dataTypeId'    => isset($dataTypes[$table]) ? $dataTypes[$table] : null,
            ];

            return (object) $table;
        }, DBSchema::tables());

        return view('voyager::tools.database.index')->with(compact('dataTypes', 'tables'));
    }

    public function create()
    {
        Voyager::can('browse_database');

        $db = $this->prepareDbManager('create');

        return view('voyager::tools.database.edit-add', compact('db'));
    }

    public function store(Request $request)
    {
        Voyager::can('browse_database');

        try {
            $table = Table::make($request->table);
            SchemaManager::createTable($table);

            if (isset($request->create_model) && $request->create_model == 'on') {
                $params = [
                    'name' => Str::studly(Str::singular($table->name)),
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
        Voyager::can('browse_database');

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
        Voyager::can('browse_database');

        $table = json_decode($request->table, true);

        try {
            DatabaseUpdater::update($table);
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

        if ($action == 'update') {
            $db->table = SchemaManager::listTableDetails($table);
            $db->formAction = route('voyager.database.update', $table);
        } else {
            $db->table = new Table('New Table');
            $db->formAction = route('voyager.database.store');
        }

        $oldTable = old('table');
        $db->oldTable = $oldTable ? $oldTable : json_encode(null);
        $db->types = Type::getPlatformTypes();
        $db->action = $action;
        $db->identifierRegex = Identifier::REGEX;
        $db->platform = SchemaManager::getDatabasePlatform()->getName();

        return $db;
    }

    public function reorder_column(Request $request)
    {
        Voyager::can('browse_database');

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
        Voyager::can('browse_database');

        return response()->json(DBSchema::describeTable($table));
    }

    public function destroy($table)
    {
        Voyager::can('browse_database');

        try {
            Schema::drop($table);

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
    public function addBread(Request $request)
    {
        Voyager::can('browse_database');

        $table = $request->input('table');

        return view('voyager::tools.database.edit-add-bread', $this->prepopulateBreadInfo($table));
    }

    private function prepopulateBreadInfo($table)
    {
        $displayName = Str::singular(implode(' ', explode('_', Str::title($table))));
        $modelNamespace = config('voyager.models.namespace', $this->getAppNamespace());
        if (empty($modelNamespace)) {
            $modelNamespace = $this->getAppNamespace();
        }

        return [
            'table'                 => $table,
            'slug'                  => Str::slug($table),
            'display_name'          => $displayName,
            'display_name_plural'   => Str::plural($displayName),
            'model_name'            => $modelNamespace.Str::studly(Str::singular($table)),
            'generate_permissions'  => true,
            'server_side'           => false,
        ];
    }

    public function storeBread(Request $request)
    {
        Voyager::can('browse_database');

        $dataType = new DataType();
        $data = $dataType->updateDataType($request->all())
            ? $this->alertSuccess('Successfully created new BREAD')
            : $this->alertError('Sorry it appears there may have been a problem creating this BREAD');

        return redirect()->route('voyager.database.index')->with($data);
    }

    public function addEditBread($id)
    {
        Voyager::can('browse_database');

        return view(
            'voyager::tools.database.edit-add-bread', [
            'dataType' => DataType::find($id),
        ]
        );
    }

    public function updateBread(Request $request, $id)
    {
        Voyager::can('browse_database');

        /** @var \TCG\Voyager\Models\DataType $dataType */
        $dataType = DataType::find($id);
        $data = $dataType->updateDataType($request->all())
            ? $this->alertSuccess("Successfully updated the {$dataType->name} BREAD")
            : $this->alertError('Sorry it appears there may have been a problem updating this BREAD');

        return redirect()->route('voyager.database.index')->with($data);
    }

    public function deleteBread($id)
    {
        Voyager::can('browse_database');

        /** @var \TCG\Voyager\Models\DataType $dataType */
        $dataType = DataType::find($id);
        $data = DataType::destroy($id)
            ? $this->alertSuccess("Successfully removed BREAD from {$dataType->name}")
            : $this->alertError('Sorry it appears there was a problem removing this BREAD');

        if (!is_null($dataType)) {
            Permission::removeFrom($dataType->name);
        }

        return redirect()->route('voyager.database.index')->with($data);
    }
}
