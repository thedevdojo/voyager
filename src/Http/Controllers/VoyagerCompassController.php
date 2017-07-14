<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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