<?php

namespace TCG\Voyager\Http\Controllers;

use Exception;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use TCG\Voyager\Models\DataRow;
use TCG\Voyager\Models\DataType;

class VoyagerDatabaseController extends Controller
{
    use \Illuminate\Console\AppNamespaceDetectorTrait;

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
                ->with([
                    'message'    => "Successfully created $tableName table",
                    'alert-type' => 'success',
                ]);
        } catch (Exception $e) {
            return back()->with([
                'message'    => 'Exception: '.$e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }

    public function edit($table)
    {
        $rows = $this->describeTable($table);

        return view('voyager::tools.database.edit-add', compact('table', 'rows'));
    }

    /**
     * @todo: Refactor this huge method.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $originalName = $request->original_name;
        $tableName = $request->name;

        // If the user has renamed the table then rename it
        if (!empty($originalName) && $originalName != $tableName) {
            try {
                Schema::rename($originalName, $tableName);
            } catch (Exception $e) {
                return back()->with([
                    'message'    => 'Exception: '.$e->getMessage(),
                    'alert-type' => 'error',
                ]);
            }
        }

        foreach ($request->row as $index => $row) {
            $rowName = $request->field[$index];
            $originalRowName = $request->original_field[$index];

            // if the name of the row has changed rename it
            if (!empty($originalRowName) && $rowName != $originalRowName) {
                Schema::table($tableName, function ($table) use ($originalRowName, $rowName) {
                    $table->renameColumn($originalRowName, $rowName);
                });
                $rowName = $originalRowName;
            }

            // if the row has been deleted, then delete it
            if ($request->delete_field[$index]) {
                Schema::table($tableName, function ($table) use ($rowName) {
                    $table->dropColumn($rowName);
                });
            }
        }

        $queryRows = $this->buildQuery($request);

        $tableArray = [];
        $tableKey = [];

        foreach ($this->describeTable((string) $tableName) as $desc) {
            array_push($tableArray, $desc->Field);
            $tableKey[$desc->Field] = $desc->Key;
        }

        Schema::table($tableName, function (Blueprint $table) use ($queryRows, $request, $tableArray, $tableKey) {
            foreach ($queryRows as $index => $query) {
                if (in_array((string) $request->field[$index], $tableArray)) {
                    $disableUnique = $tableKey[(string) $request->field[$index]] == 'UNI';
                    $query($table, $disableUnique)->change();
                } else {
                    $query($table);
                }
            }
        });

        $tableTypeArray = [];
        $tableDefaultArray = [];
        $tableNullArray = [];

        foreach ($this->describeTable((string) $tableName) as $desc) {
            $tableTypeArray[$desc->Field] = $desc->Type;
            $tableDefaultArray[$desc->Field] = empty($desc->Default) ? '' : ' DEFAULT '.$desc->Default;
            $tableNullArray[$desc->Field] = $desc->null == 'NO' ? ' NOT NULL' : ' NULL';
        }

        foreach ($request->row as $index => $row) {
            //Reorder the rows
            $fieldName = (string) $request->field[$index];
            if ($index > 0) {
                if ($fieldName != 'id') {
                    DB::statement('ALTER TABLE '.(string) $tableName.' MODIFY COLUMN '.$fieldName.' '.$tableTypeArray[$fieldName].$tableDefaultArray[$fieldName].$tableNullArray[$fieldName].' AFTER '.$request->field[$index - 1]);
                }
            } elseif ($fieldName != 'id') {
                DB::statement('ALTER TABLE '.(string) $tableName.' MODIFY COLUMN '.$fieldName.' '.$tableTypeArray[$fieldName].$tableDefaultArray[$fieldName].$tableNullArray[$fieldName].' FIRST');
            }
        }

        return redirect()
            ->route('voyager.database')
            ->with([
                'message'    => "Successfully update $tableName table",
                'alert-type' => 'success',
            ]);
    }

    /**
     * @todo: Refactor this huge method.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function buildQuery(Request $request)
    {
        $queryRows = [];

        foreach ($request->row as $index => $row) {
            $field = $request->field[$index];
            $type = isset($request->type[$index]) ? $request->type[$index] : 'string';
            $nullable = isset($request->nullable[$index]) && $request->nullable[$index] == 'on';

            $key = $request->key[$index];
            $default = $request->default[$index];

            $query = function (Blueprint $table, $disableUnique = false) use ($request, $index, $field, $type, $nullable, $key, $default) {
                if ($key == 'PRI') {
                    $result = $table->increments($field);
                } else {
                    if ($field == 'created_at & updated_at') {
                        $result = $table->timestamp($field);
                    } else {
                        $result = $type == 'enum'
                            ? $table->enum($field, [$request->enum[$index]])
                            : $table->$type($field);
                    }
                }

                if ($key == 'UNI' && $disableUnique === false) {
                    $result = $result->unique();
                }
                if ($field != 'created_at & updated_at') {
                    $result = $result->nullable($nullable);
                    if ($default) {
                        $result->default($default);
                    }
                }

                return $result;
            };

            array_push($queryRows, $query);
        }

        return $queryRows;
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
                ->with([
                    'message'    => "Successfully deleted $table table",
                    'alert-type' => 'success',
                ]);
        } catch (Exception $e) {
            return back()->with([
                'message'    => 'Exception: '.$e->getMessage(),
                'alert-type' => 'error',
            ]);
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
        return view('voyager::tools.database.edit-add-bread', [
            'dataType' => DataType::find($id),
        ]);
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
        $success = $dataType->update($requestData);
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

    protected function describeTable($table)
    {
        $raw = "select COLUMN_NAME as 'Field',
                       COLUMN_TYPE as 'Type',
                       IS_NULLABLE as 'Null',
                       COLUMN_KEY as 'Key',
                       COLUMN_DEFAULT as 'Default',
                       EXTRA as 'Extra'
                from INFORMATION_SCHEMA.COLUMNS
                where TABLE_NAME='{$table}'";

        return \DB::select(\DB::raw($raw));
    }
}
