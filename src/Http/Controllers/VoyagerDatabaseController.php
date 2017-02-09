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

        $formAction = route('voyager.database.store');
        $columnTypes = Type::getPlatformTypes();
        $tableObj = new Table('New Table'); // need  way to denote that this ia a new table

        return view('voyager::tools.database.edit-add', compact('formAction', 'columnTypes', 'tableObj'));
    }

    public function store(Request $request)
    {
        Voyager::can('browse_database');

        $tableName = $request->name;

        try {
            Schema::create($tableName, function (Blueprint $table) use ($request) {
                foreach ($this->buildQuery($request) as $query) {
                    $query($table);
                }
            });

            if (isset($request->create_model) && $request->create_model == 'on') {
                $params = [
                    'name' => Str::studly(Str::singular($tableName)),
                ];

                if (in_array('deleted_at', $request->input('field.*'))) {
                    $params['--softdelete'] = true;
                }

                if (isset($request->create_migration) && $request->create_migration == 'on') {
                    $params['--migration'] = true;
                }

                Artisan::call('voyager:make:model', $params);
            } elseif (isset($request->create_migration) && $request->create_migration == 'on') {
                Artisan::call('make:migration', [
                    'name'    => 'create_'.$tableName.'_table',
                    '--table' => $tableName,
                ]);
            }

            return redirect()
                ->route('voyager.database.index')
                ->with(
                    [
                        'message'    => "Successfully created $tableName table",
                        'alert-type' => 'success',
                    ]
                );
        } catch (Exception $e) {
            return back()->with(
                [
                    'message'    => 'Exception: '.$e->getMessage(),
                    'alert-type' => 'error',
                ]
            );
        }
    }

    public function edit($table)
    {
        Voyager::can('browse_database');

        if (!SchemaManager::tableExists($table)) {
            return redirect()
                ->route('voyager.database.index')
                ->with(
                    [
                        'message'    => "The table you want to edit doesn't exist",
                        'alert-type' => 'error',
                    ]
                );
        }

        // todo: create a function that prepares the table and db types etc....
        //     use it in create()
        $database = new \stdClass();
        $database->types = Type::getPlatformTypes();
        $database->table = SchemaManager::listTableDetails($table);
        // todo: use $database->table instead in the views...
        $table = $database->table;
        $database->identifierRegex = Identifier::REGEX;
        $database->platform = SchemaManager::getDatabasePlatform()->getName();

        $formAction = route('voyager.database.update', $table->name);

        return view(
            'voyager::tools.database.edit-add',
            compact('database', 'table', 'formAction')
        );
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
            return back()->with(
                [
                    'message'    => 'Exception: '.$e->getMessage(),
                    'alert-type' => 'error',
                ]
            );
        }

        return redirect()
               ->route('voyager.database.edit', $table['name'])
               ->with(
                [
                    'message'    => "Successfully updated {$table['name']} table",
                    'alert-type' => 'success',
                ]
            );
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
                ->with(
                    [
                        'message'    => "Successfully deleted $table table",
                        'alert-type' => 'success',
                    ]
                );
        } catch (Exception $e) {
            return back()->with(
                [
                    'message'    => 'Exception: '.$e->getMessage(),
                    'alert-type' => 'error',
                ]
            );
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
            ? [
                'message'    => 'Successfully created new BREAD',
                'alert-type' => 'success',
            ]
            : [
                'message'    => 'Sorry it appears there may have been a problem creating this bread',
                'alert-type' => 'error',
            ];

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
            ? [
                'message'    => "Successfully updated the {$dataType->name} BREAD",
                'alert-type' => 'success',
            ]
            : [
                'message'    => 'Sorry it appears there may have been a problem updating this bread',
                'alert-type' => 'error',
            ];

        return redirect()->route('voyager.database.index')->with($data);
    }

    public function deleteBread($id)
    {
        Voyager::can('browse_database');

        /** @var \TCG\Voyager\Models\DataType $dataType */
        $dataType = DataType::find($id);
        $data = DataType::destroy($id)
            ? [
                'message'    => "Successfully removed BREAD from {$dataType->name}",
                'alert-type' => 'success',
            ]
            : [
                'message'    => 'Sorry it appears there was a problem removing this bread',
                'alert-type' => 'danger',
            ];

        if (!is_null($dataType)) {
            Permission::removeFrom($dataType->name);
        }

        return redirect()->route('voyager.database.index')->with($data);
    }
}
