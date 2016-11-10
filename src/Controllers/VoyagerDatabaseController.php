<?php

namespace TCG\Voyager\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use TCG\Voyager\Models\User as User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use TCG\Voyager\Models\DataType as DataType;
use TCG\Voyager\Models\DataRow as DataRow;

class VoyagerDatabaseController extends Controller
{
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

        $table_name = $request->name;
        $query_rows = $this->buildQuery($request);

        try {
            Schema::create($table_name, function (Blueprint $table) use ($query_rows) {
                foreach ($query_rows as $query) {
                    eval('$table->' . $query . ";");
                }
            });

            if (isset($request->create_model) && $request->create_model == "on") {
                Artisan::call('make:model', ['name' => ucfirst($table_name)]);
            }

            return redirect('/admin/database/')->with([
                'message' => 'Successfully created ' . $table_name . ' table',
                'alert-type' => 'success'
            ]);
        } catch (\Exception $e) {
            return back()->with(['message' => 'Exception: ' . $e->getMessage(), 'alert-type' => 'error']);
        }

    }

    public function edit($table)
    {
        $rows = \DB::select('describe ' . $table);
        return view('voyager::tools.database.edit-add', compact('table', 'rows'));
    }

    public function update(Request $request)
    {
        $original_name = $request->original_name;
        $table_name = $request->name;

        // If the user has renamed the table then rename it
        if (!empty($original_name) && $original_name != $table_name) {
            try {
                Schema::rename($original_name, $table_name);
            } catch (\Exception $e) {
                return back()->with(['message' => 'Exception: ' . $e->getMessage(), 'alert-type' => 'error']);
            }

        }


        foreach ($request->row as $index => $row) {
            $row_name = $request->field[$index];
            $original_row_name = $request->original_field[$index];

            // if the name of the row has changed rename it
            if (!empty($original_row_name) && $row_name != $original_row_name) {
                Schema::table($table_name, function ($table) use ($original_row_name, $row_name) {
                    $table->renameColumn($original_row_name, $row_name);
                });
                $row_name = $original_row_name;
            }

            // if the row has been deleted, then delete it
            if ($request->delete_field[$index]) {
                Schema::table($table_name, function ($table) use ($row_name) {
                    $table->dropColumn($row_name);
                });
            }
        }

        $query_rows = $this->buildQuery($request);

        //try{

        $table_desc = \DB::select('describe ' . (string)$table_name);
        $table_array = [];
        $table_key = [];
        foreach ($table_desc as $desc) {
            array_push($table_array, $desc->Field);
            $table_key[$desc->Field] = $desc->Key;

        }

        Schema::table($table_name, function (Blueprint $table) use ($query_rows, $request, $table_array, $table_key) {
            foreach ($query_rows as $index => $query) {
                if (strpos($query, 'timestamps()') === false) {

                    if (in_array((string)$request->field[$index], $table_array)) {
                        if ($table_key[(string)$request->field[$index]] == "UNI") {
                            $query = str_replace('->unique()', '', $query);
                        }
                        eval('$table->' . $query . "->change();");
                    } else {
                        eval('$table->' . $query . ";");
                    }

                }

            }
        });

        $table_desc = \DB::select('describe ' . (string)$table_name);

        $table_type_array = [];
        $table_default_array = [];
        $table_null_array = [];
        foreach ($table_desc as $desc) {
            $table_type_array[$desc->Field] = $desc->Type;
            $desc_default = "";
            if (!empty($desc->Default)) {
                $desc_default = " DEFAULT " . $desc->Default;

            }
            $table_default_array[$desc->Field] = $desc_default;
            $null_val = " NULL";
            if ($desc->Null == "NO") {
                $null_val = " NOT NULL";
            }
            $table_null_array[$desc->Field] = $null_val;
        }


        foreach ($request->row as $index => $row) {
            //Reorder the rows
            $field_name = (string)$request->field[$index];
            if ($index > 0) {
                if ($field_name != 'id') {
                    \DB::statement("ALTER TABLE " . (string)$table_name . " MODIFY COLUMN " . $field_name . " " . $table_type_array[$field_name] . $table_default_array[$field_name] . $table_null_array[$field_name] . " AFTER " . $request->field[$index - 1]);
                }
            } else {
                if ($field_name != 'id') {
                    \DB::statement("ALTER TABLE " . (string)$table_name . " MODIFY COLUMN " . $field_name . " " . $table_type_array[$field_name] . $table_default_array[$field_name] . $table_null_array[$field_name] . " FIRST");
                }
            }
        }

        return redirect('/admin/database/')->with([
            'message' => 'Successfully update ' . $table_name . ' table',
            'alert-type' => 'success'
        ]);
        // } catch(\Exception $e){
        // 	return back()->with(array('message' => 'Exception: ' . $e->getMessage(), 'alert-type' => 'error'));
        // }


    }

    public function buildQuery($request)
    {
        $query_rows = [];
        foreach ($request->row as $index => $row) {
            $field = $request->field[$index];
            if (isset($request->type[$index])) {
                $type = $request->type[$index];
            } else {
                $type = 'string';
            }
            //dd($request->nullable[$index]);

            if (isset($request->nullable[$index]) && $request->nullable[$index] == "on") {
                $nullable = true;
            } else {
                $nullable = false;
            }

            $key = $request->key[$index];
            $default = $request->default[$index];

            $query = '';

            if ($key == 'PRI') {
                $query .= "increments('$field')";
            } else {
                if ($field == 'created_at & updated_at' || $field == 'created_at' || $field == 'updated_at') {
                    $query .= "timestamps()";
                } else {
                    if ($type == 'enum') {
                        $query .= "enum('$field', ['" . $request->enum[$index] . "'])";
                    } else {
                        $query .= $type . "('$field')";
                    }
                }
            }

            if ($key == 'UNI') {
                $query .= "->unique()";
            }

            if ($nullable && $field != 'created_at & updated_at') {
                $query .= "->nullable()";
            }
            if (!$nullable && $field != 'created_at & updated_at') {
                $query .= "->nullable(false)";
            }

            if ($default && $field != 'created_at & updated_at') {
                $query .= "->default('$default')";
            }

            array_push($query_rows, $query);
        }

        return $query_rows;


    }

    public function reorder_column(Request $request)
    {
        if ($request->ajax()) {
            $table = $request->table;
            $column = $request->column;
            $after = $request->after;
            if ($after == null) {
                // SET COLUMN TO THE TOP
                \DB::query("ALTER $table MyTable CHANGE COLUMN $column FIRST");
            }
            return 1;
        }
        return 0;
    }

    public function table($table)
    {
        return response()->json(\DB::select('describe ' . $table));
    }

    public function delete($table)
    {
        try {
            Schema::drop($table);
            return redirect('/admin/database/')->with([
                'message' => 'Successfully deleted ' . $table . ' table',
                'alert-type' => 'success'
            ]);
        } catch (\Exception $e) {
            return back()->with(['message' => 'Exception: ' . $e->getMessage(), 'alert-type' => 'error']);
        }
    }

    /********** BREAD METHODS **********/

    public function addBread(Request $request)
    {
        return view('voyager::tools.database.edit-add-bread', ['table' => $request->input('table')]);
    }

    public function storeBread(Request $request)
    {
        //
        $requestData = $request->all();
        $dataType = new DataType;
        $success = $this->updateDataType($dataType, $requestData);

        if ($success):
            return redirect('/admin/database')->with([
                'message' => 'Successfully created new BREAD',
                'alert-type' => 'success'
            ]);
        endif;

        return redirect('/admin/database')->with([
            'message' => 'Sorry it appears there may have been a problem creating this bread',
            'alert-type' => 'error'
        ]);
    }

    public function addEditBread($id)
    {
        return view('voyager::tools.database.edit-add-bread', ['dataType' => DataType::find($id)]);
    }

    public function updateBread(Request $request, $id)
    {
        $requestData = $request->all();
        $dataType = DataType::find($id);
        $success = $this->updateDataType($dataType, $requestData);

        if ($success):
            return redirect('/admin/database')->with([
                'message' => 'Successfully updated the ' . $dataType->name . ' BREAD',
                'alert-type' => 'success'
            ]);
        endif;

        return redirect('/admin/database')->with([
            'message' => 'Sorry it appears there may have been a problem updating this bread',
            'alert-type' => 'error'
        ]);

    }

    public function updateDataType($dataType, $requestData)
    {

        $dataType->name = $requestData['name'];
        $dataType->slug = $requestData['slug'];
        $dataType->display_name_singular = $requestData['display_name_singular'];
        $dataType->display_name_plural = $requestData['display_name_plural'];
        $dataType->icon = $requestData['icon'];
        $dataType->model_name = $requestData['model_name'];
        $dataType->description = $requestData['description'];
        $success = $dataType->save();

        $columns = Schema::getColumnListing($dataType->name);

        foreach ($columns as $column) {
            $dataRow = DataRow::where('data_type_id', '=', $dataType->id)->where('field', '=', $column)->first();
            if (!isset($dataRow->id)) {
                $dataRow = new DataRow;
            }
            $dataRow->data_type_id = $dataType->id;

            $dataRow->required = $requestData['field_required_' . $column];

            $bread_checks = ['browse', 'read', 'edit', 'add', 'delete'];

            foreach ($bread_checks as $check) {
                if (isset($requestData['field_' . $check . '_' . $column])) {
                    $dataRow->{$check} = 1;
                } else {
                    $dataRow->{$check} = 0;
                }
            }
            $dataRow->field = $requestData['field_' . $column];
            $dataRow->type = $requestData['field_input_type_' . $column];
            $dataRow->details = $requestData['field_details_' . $column];
            $dataRow->display_name = $requestData['field_display_name_' . $column];
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
        $datatype = DataType::find($id);
        $table_name = $datatype->name;
        if (DataType::destroy($id)) {
            return redirect('/admin/database')->with([
                'message' => 'Successfully removed BREAD from ' . $table_name,
                'alert-type' => 'success'
            ]);
        }

        return redirect('/admin/database')->with([
            'message' => 'Sorry it appears there was a problem removing this bread',
            'alert-type' => 'danger'
        ]);
    }
}
