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
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\Traits\DatabaseUpdate;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Permission;

class VoyagerDatabaseController extends Controller
{
    use DatabaseUpdate, AppNamespaceDetectorTrait;

    public function index()
    {
        Voyager::canOrFail('browse_database');

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
        Voyager::canOrFail('browse_database');

        $formAction = route('voyager.database.store');

        return view('voyager::tools.database.edit-add', compact('formAction'));
    }

    public function store(Request $request)
    {
        Voyager::canOrFail('browse_database');

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
        Voyager::canOrFail('browse_database');

        $rows = DBSchema::describeTable($table);
        $formAction = route('voyager.database.update', $table);

        return view('voyager::tools.database.edit-add', compact('table', 'rows', 'formAction'));
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

        $this->renameTable($request->original_name, $request->name);
        $this->renameColumns($request, $request->name);
        $this->dropColumns($request, $request->name);
        $this->updateColumns($request, $request->name);

        return redirect()
            ->route('voyager.database.index')
            ->with(
                [
                    'message'    => "Successfully updated $request->name table",
                    'alert-type' => 'success',
                ]
            );
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

        return response()->json(DBSchema::describeTable($table));
    }

    public function destroy($table)
    {
        Voyager::canOrFail('browse_database');

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
        Voyager::canOrFail('browse_database');

        $table = $request->input('table');

        $data = $this->prepopulateBreadInfo($table);
        $data['fieldOptions'] = \TCG\Voyager\Facades\DBSchema::describeTable($table);

        return view('voyager::tools.database.edit-add-bread', $data);
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
        Voyager::canOrFail('browse_database');

        try {
            $dataType = new DataType();
            $data = $dataType->updateDataType($request->all(), true)
                ? [
                    'message'    => 'Successfully created new BREAD',
                    'alert-type' => 'success',
                ]
                : [
                    'message'    => 'Sorry it appears there may have been a problem creating this bread',
                    'alert-type' => 'error',
                ];

            return redirect()->route('voyager.database.index')->with($data);
        } catch (\Exception $e) {
            return redirect()->route('voyager.database.index')->with([
                'message'    => 'Saving Failed! '.$e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }

    public function addEditBread($id)
    {
        Voyager::canOrFail('browse_database');

        $dataType = DataType::find($id);

        try {
            $fieldOptions = isset($dataType) ? $dataType->fieldOptions() : \TCG\Voyager\Facades\DBSchema::describeTable($dataType->name);
        } catch (\Exception $e) {
            $fieldOptions = \TCG\Voyager\Facades\DBSchema::describeTable($dataType->name);
        }

        return view(
            'voyager::tools.database.edit-add-bread', [
                'dataType'     => $dataType,
                'fieldOptions' => $fieldOptions,
            ]
        );
    }

    public function updateBread(Request $request, $id)
    {
        Voyager::canOrFail('browse_database');

        /* @var \TCG\Voyager\Models\DataType $dataType */
        try {
            $dataType = DataType::find($id);

            $data = $dataType->updateDataType($request->all(), true)
                ? [
                    'message'    => "Successfully updated the {$dataType->name} BREAD",
                    'alert-type' => 'success',
                ]
                : [
                    'message'    => 'Sorry it appears there may have been a problem updating this bread',
                    'alert-type' => 'error',
                ];

            return redirect()->route('voyager.database.index')->with($data);
        } catch (\Exception $e) {
            return back()->with([
                'message'    => 'Update Failed! '.$e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }

    public function deleteBread($id)
    {
        Voyager::canOrFail('browse_database');

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
