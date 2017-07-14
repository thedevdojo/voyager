<?php

namespace TCG\Voyager\Http\Controllers;

use TCG\Voyager\Facades\Voyager;

class VoyagerCompassController extends Controller
{
    public function index()
    {
        // Check permission
        //Voyager::canOrFail('browse_compass');

        return view('voyager::compass.index');
    }
}
