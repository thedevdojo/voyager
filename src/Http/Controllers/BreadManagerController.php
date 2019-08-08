<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;

class BreadManagerController extends Controller
{
    /**
     * Display all BREADs.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();

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
        if (!Voyager::createBread($table)) {
            // TODO: throw exception
        }

        return $this->edit($table);
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
        $bread = Voyager::getBread($table);
        // TODO: throw exception if BREAD is not found.
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
        $success = true;
        $message = __('voyager::manager.bread_saved_successfully', ['name' => $table]);
        $bread = (object) $request->bread;

        if (!empty($bread->model_name) && !class_exists(Str::start($bread->model_name, '\\'))) {
            $success = false;
            $message = __('voyager::manager.model_does_not_exist', ['name' => $bread->model_name]);
        } elseif (!empty($bread->controller) && !class_exists(Str::start($bread->controller, '\\'))) {
            $success = false;
            $message = __('voyager::manager.controller_does_not_exist', ['name' => $bread->controller]);
        } elseif (!empty($bread->policy) && !class_exists(Str::start($bread->policy, '\\'))) {
            $success = false;
            $message = __('voyager::manager.policy_does_not_exist', ['name' => $bread->policy]);
        } elseif (!Voyager::storeBread($bread)) {
            $success = false;
            $message = __('voyager::manager.bread_save_failed');
        }

        Cache::forget('voyager-breads');

        return [
            'success' => $success,
            'message' => $message,
        ];
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
        Cache::forget('voyager-breads');
    }
}
