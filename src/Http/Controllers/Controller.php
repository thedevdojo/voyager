<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;

abstract class Controller extends BaseController
{
    use AuthorizesRequests;

    public function getBread(Request $request)
    {
        $slug = explode('.', $request->route()->getName())[1];

        return Voyager::getBreadBySlug($slug);
    }
}
