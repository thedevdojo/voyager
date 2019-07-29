<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
     * Create a BREAD for a given $table
     *
     * @param  string  $table
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
     * Edit a BREAD for a given $table
     *
     * @param  string  $table
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
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $table
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $table)
    {
        //
    }

    /**
     * Remove a BREAD by a given table.
     *
     * @param  string  $table
     * @return \Illuminate\Http\Response
     */
    public function destroy($table)
    {
        //
    }
}
