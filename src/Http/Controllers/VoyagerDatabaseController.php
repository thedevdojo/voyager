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
use TCG\Voyager\Http\Controllers\Traits\DatabaseUpdate;
use TCG\Voyager\Models\DataRow;
use TCG\Voyager\Models\DataType;

class VoyagerDatabaseController extends Controller
{
    use AppNamespaceDetectorTrait;
    use DatabaseUpdate;

    public function index()
    {
        return view('voyager::tools.database.index');
    }

    public function create()
    {
        return view('voyager::tools.database.edit-add');
    }

    public function store(Request $request)
    {
        $tableName = $request->name;

        try {
            Schema::create($tableName, function (Blueprint $table) use ($request) {
                foreach ($this->buildQuery($request) as $query) {
                    $query($table);
                }
            });

            if (isset($request->create_model) && $request->create_model == 'on') {
                Artisan::call('make:model', [
                    'name' => ucfirst($tableName),
                ]);
            }

            return redirect()
                ->route('voyager.database')
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
        $rows = $this->describeTable($table);

        return view('voyager::tools.database.edit-add', compact('table', 'rows'));
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
        $tableName = $request->name;

        $this->renameTable($request->original_name, $tableName);
        $this->renameColumns($request, $tableName);
        $this->dropColumns($request, $tableName);
        $this->updateColumns($request, $tableName);

        return redirect()
            ->route('voyager.database')
            ->withMessage("Successfully updated {$tableName} table")
            ->with('alert-type', 'success');
    }

    public function reorder_column(Request $request)
    {
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

    public function table($table)
    {
        return response()->json($this->describeTable($table));
    }

    public function delete($table)
    {
        try {
            Schema::drop($table);

            return redirect()
                ->route('voyager.database')
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
        $table = $request->input('table');

        return view('voyager::tools.database.edit-add-bread', $this->prepopulateBreadInfo($table));
    }

    private function prepopulateBreadInfo($table)
    {
        $displayName = Str::singular(implode(' ', explode('_', Str::title($table))));

        return [
            'table'               => $table,
            'slug'                => Str::slug($table),
            'display_name'        => $displayName,
            'display_name_plural' => Str::plural($displayName),
            'model_name'          => $this->getNamespace().'\\'.Str::studly(Str::singular($table)),
        ];
    }

    public function storeBread(Request $request)
    {
        $data = $this->updateDataType(new DataType(), $request->all())
            ? [
                'message'    => 'Successfully created new BREAD',
                'alert-type' => 'success',
            ]
            : [
                'message'    => 'Sorry it appears there may have been a problem creating this bread',
                'alert-type' => 'error',
            ];

        return redirect()->route('voyager.database')->with($data);
    }

    public function addEditBread($id)
    {
        return view(
            'voyager::tools.database.edit-add-bread', [
            'dataType' => DataType::find($id),
        ]
        );
    }

    public function updateBread(Request $request, $id)
    {
        /** @var \TCG\Voyager\Models\DataType $dataType */
        $dataType = DataType::find($id);
        $data = $this->updateDataType($dataType, $request->all())
            ? [
                'message'    => "Successfully updated the {$dataType->name} BREAD",
                'alert-type' => 'success',
            ]
            : [
                'message'    => 'Sorry it appears there may have been a problem updating this bread',
                'alert-type' => 'error',
            ];

        return redirect()->route('voyager.database')->with($data);
    }

    public function updateDataType(DataType $dataType, $requestData)
    {
        $success = $dataType->fill($requestData)->save();
        $columns = Schema::getColumnListing($dataType->name);

        foreach ($columns as $column) {
            $dataRow = DataRow::where('data_type_id', '=', $dataType->id)
                              ->where('field', '=', $column)
                              ->first();

            if (!isset($dataRow->id)) {
                $dataRow = new DataRow();
            }

            $dataRow->data_type_id = $dataType->id;
            $dataRow->required = $requestData['field_required_'.$column];

            foreach (['browse', 'read', 'edit', 'add', 'delete'] as $check) {
                if (isset($requestData["field_{$check}_{$column}"])) {
                    $dataRow->{$check} = 1;
                } else {
                    $dataRow->{$check} = 0;
                }
            }

            $dataRow->field = $requestData['field_'.$column];
            $dataRow->type = $requestData['field_input_type_'.$column];
            $dataRow->details = $requestData['field_details_'.$column];
            $dataRow->display_name = $requestData['field_display_name_'.$column];
            $dataRowSuccess = $dataRow->save();
            // If success has never failed yet, let's add DataRowSuccess to success
            if ($success !== false) {
                $success = $dataRowSuccess;
            }
        }

        return $success !== false;
    }

    public function deleteBread($id)
    {
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

        return redirect()->route('voyager.database')->with($data);
    }
}
