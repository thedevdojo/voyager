<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager as VoyagerFacade;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = VoyagerFacade::setting(false);

        return view('voyager::settings.browse', compact('settings'));
    }

    public function store(Request $request)
    {
        $settings = $request->get('settings', '');
        // Reload settings from request, so new validation rules are respected
        VoyagerFacade::loadSettings(json_decode(json_encode($settings)));
        $data = collect();
        $formfields = VoyagerFacade::setting(false)->transform(function ($setting, $key) use ($settings, &$data) {
            $column = $setting->key;
            if ($setting->group) {
                $column = $setting->group.'_'.$column;
            }
            $setting->formfield->column = $column;
            $data->put($column, $settings[$key]['value']);

            return $setting;
        })->pluck('formfield');

        $layout = new class() {
            public $formfields = [];

            public function isFormfieldTranslatable($column)
            {
                return true;
            }
        };
        $layout->formfields = $formfields;

        $validator = $this->getValidator($layout, $data);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        } else {
            VoyagerFacade::saveSettings($settings);
        }

        return response()->json($validator->messages(), 200);
    }
}
