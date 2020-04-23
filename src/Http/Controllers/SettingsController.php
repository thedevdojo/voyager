<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use TCG\Voyager\Facades\Settings as SettingsFacade;
use TCG\Voyager\Facades\Voyager as VoyagerFacade;

class SettingsController extends Controller
{
    public function index()
    {
        return view('voyager::settings.browse', [
            'settings' => SettingsFacade::getSettings()
        ]);
    }

    public function store(Request $request)
    {
        SettingsFacade::saveSettings($request->get('settings', '[]'));
    }
}
