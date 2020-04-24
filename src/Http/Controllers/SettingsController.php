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
        // Push a fake column object to settings
        $settings = collect($request->get('settings', []))->transform(function ($setting) {
            $setting = (object) $setting;
            foreach ($setting->validation as $key => $rule) {
                $setting->validation[$key] = (object) $rule;
            }
            $key = $setting->key;
            if (!empty($setting->group)) {
                $key = implode('.', [$setting->group, $setting->key]);
            }
            $setting->column = (object) ['column' => $key];

            return $setting;
        });
        // Prepare a data-array which is readable
        $data = $settings->mapWithKeys(function ($setting) {
            return [$setting->column->column => $setting->value];
        });
        $validation_errors = $this->validateData($settings, $data);

        if (count($validation_errors) > 0) {
            return response()->json($validation_errors, 422);
        }
        SettingsFacade::saveSettings($request->get('settings', '[]'));

        return response(200);
    }
}
