<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
            VoyagerFacade::flashMessage(__('voyager::manager.bread_already_exists', ['table' => $table]), 'info', true);

            return redirect()->route('voyager.bread.edit', $table);
        }

        $bread = BreadFacade::createBread($table);

        return view('voyager::manager.edit-add', compact('bread'));
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
            VoyagerFacade::flashMessage(__('voyager::manager.bread_does_no_exist', ['table' => $table]), 'error', true);

            return redirect()->route('voyager.bread.create', $table);
        }

        return view('voyager::manager.edit-add', compact('bread'));
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
}
