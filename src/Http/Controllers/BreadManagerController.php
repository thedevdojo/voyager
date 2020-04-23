<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Bread as BreadFacade;
use TCG\Voyager\Facades\Voyager as VoyagerFacade;
use TCG\Voyager\Rules\ClassExists as ClassExistsRule;
use TCG\Voyager\Rules\DefaultLocale as DefaultLocaleRule;

class BreadManagerController extends Controller
{
    /**
     * Display all BREADs.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tables = VoyagerFacade::getTables();

        return view('voyager::manager.index', compact('tables'));
    }

    /**
     * Create a BREAD for a given $table.
     *
     * @param string $table
     *
     * @return \Illuminate\Http\Response
     */
    public function create($table)
    {
        if (!in_array($table, VoyagerFacade::getTables())) {
            throw new \TCG\Voyager\Exceptions\TableNotFoundException('Table "'.$table.'" does not exist');
        }

        if (BreadFacade::getBread($table)) {
            VoyagerFacade::flashMessage(__('voyager::manager.bread_already_exists', ['table' => $table]), 'yellow', 5000, true);

            return redirect()->route('voyager.bread.edit', $table);
        }

        $bread = BreadFacade::createBread($table);
        $new = true;

        return view('voyager::manager.edit-add', compact('bread', 'new'));
    }

    /**
     * Edit a BREAD for a given $table.
     *
     * @param string $table
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($table)
    {
        $bread = BreadFacade::getBread($table);
        if (!$bread) {
            VoyagerFacade::flashMessage(__('voyager::manager.bread_does_no_exist', ['table' => $table]), 'red', 5000, true);

            return redirect()->route('voyager.bread.create', $table);
        }

        $new = false;

        return view('voyager::manager.edit-add', compact('bread', 'new'));
    }

    /**
     * Update a BREAD.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $table
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $table)
    {
        $bread = $request->bread;

        @json_decode(@json_encode($bread));
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(__('voyager::bread.json_data_not_valid'), 422);
        }

        $bread = (object) $bread;

        $bread->table = $table;

        $validator = Validator::make($request->get('bread'), [
            'slug'          => ['required', new DefaultLocaleRule()],
            'name_singular' => ['required', new DefaultLocaleRule()],
            'name_plural'   => ['required', new DefaultLocaleRule()],
            'model'         => ['nullable', new ClassExistsRule()],
            'controller'    => ['nullable', new ClassExistsRule()],
            'policy'        => ['nullable', new ClassExistsRule()],
        ]);

        if ($validator->passes()) {
            if (!BreadFacade::storeBread($bread)) {
                $validator->errors()->add('bread', __('voyager::manager.bread_save_failed'));

                return response()->json($validator->errors(), 422);
            }
        } else {
            return response()->json($validator->errors(), 422);
        }

        return response()->json($validator->messages(), 200);
    }

    /**
     * Remove a BREAD by a given table.
     *
     * @param string $table
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($table)
    {
        return response('', BreadFacade::deleteBread($table) ? 200 : 500);
    }

    /**
     * Get BREAD properties (accessors, scopes and relationships) by model
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function getProperties(Request $request)
    {
        $model = $request->get('model', '');
        if ($model == '') {
            return response('Please enter a model class', 400);
        }
        $model = Str::start($model, '\\');
        if (!class_exists($model)) {
            return response('Model "'.$model.'" does not exist!', 400);
        }
        $instance = new $model();
        $reflection = BreadFacade::getModelReflectionClass($model);
        $resolve_relationships = $request->get('resolve_relationships', false);

        return response()->json([
            'columns'       => VoyagerFacade::getColumns($instance->getTable()),
            'computed'      => BreadFacade::getModelComputedProperties($reflection)->values(),
            'scopes'        => BreadFacade::getModelScopes($reflection)->values(),
            'relationships' => BreadFacade::getModelRelationships($reflection, $instance, $resolve_relationships)->values(),
            'softdeletes'   => in_array(SoftDeletes::class, class_uses($instance)),
        ], 200);
    }

    /**
     * Get all BREADs
     *
     * @return \Illuminate\Http\Response
     */
    public function getBreads()
    {
        return response()->json([
            'breads'  => BreadFacade::getBreads(),
            'backups' => BreadFacade::getBackups()
        ], 200);
    }

    /**
     * Backup a BREAD
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function backupBread(Request $request)
    {
        $result = BreadFacade::backupBread($request->get('table', ''));

        return response($result, $result === false ? 500 : 200);
    }

    /**
     * Rollback a BREAD
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function rollbackBread(Request $request)
    {
        $result = BreadFacade::rollbackBread($request->get('table', ''), $request->get('path', ''));

        return response($result, $result === false ? 500 : 200);
    }
}
