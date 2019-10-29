<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use TCG\Voyager\Facades\Settings as SettingsFacade;
use Validator;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = SettingsFacade::setting(false);

        return view('voyager::settings.browse', compact('settings'));
    }

    public function store(Request $request)
    {
        $settings = $request->get('settings', '');
        // Reload settings from request, so new validation rules are respected
        SettingsFacade::loadSettings(json_decode(json_encode($settings)));
        $data = collect();
        $formfields = SettingsFacade::setting(false)->transform(function ($setting, $key) use ($settings, &$data) {
            $column = $setting->key;
            if ($setting->group) {
                $column = $setting->group.'_'.$column;
            }
            $setting->formfield->column = $column;
            $data->put($column, $settings[$key]['value']);

            return $setting;
        })->pluck('formfield');

        $layout = new class {
            public $formfields = [];
            public function isColumnTranslatable ($column) {
                return true;
            }
        };
        $layout->formfields = $formfields;

        $validator = $this->getValidator($layout, $data);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        } else {
            SettingsFacade::saveSettings($settings);
        }

        return response()->json($validator->messages(), 200);
    }
}
