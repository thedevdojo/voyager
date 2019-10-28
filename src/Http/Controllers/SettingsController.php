<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use TCG\Voyager\Facades\Settings as SettingsFacade;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = SettingsFacade::setting(false);

        return view('voyager::settings.browse', compact('settings'));
    }

    public function store(Request $request)
    {
        SettingsFacade::saveSettings($request->get('settings', ''));
    }
}
