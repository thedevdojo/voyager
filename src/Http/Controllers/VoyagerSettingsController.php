<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use TCG\Voyager\Models\Setting;
use TCG\Voyager\Voyager;

class VoyagerSettingsController extends Controller
{
    public function index()
    {
        // Check permission
        Voyager::can('browse_settings');

        $settings = Setting::orderBy('order', 'ASC')->get();

        return view('voyager::settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        // Check permission
        Voyager::can('browse_settings');

        $lastSetting = Setting::orderBy('order', 'DESC')->first();
        if ($lastSetting == null) {
            $order = 0;
        } else {
            $order = intval($lastSetting->order) + 1;
        }
        $request->merge(['order' => $order]);
        $request->merge(['value' => '']);
        Setting::create($request->all());

        return back()->with([
            'message'    => 'Successfully Created Settings',
            'alert-type' => 'success',
        ]);
    }

    public function update(Request $request)
    {
        // Check permission
        Voyager::can('visit_settings');

        $settings = Setting::all();

        foreach ($settings as $setting) {
            $content = $this->getContentBasedOnType($request, 'settings', (object) [
                'type'    => $setting->type,
                'field'   => $setting->key,
                'details' => $setting->details,
            ]);

            if ($content === null && isset($setting->value)) {
                $content = $setting->value;
            }

            $setting->value = $content;
            $setting->save();
        }

        return back()->with([
            'message'    => 'Successfully Saved Settings',
            'alert-type' => 'success',
        ]);
    }

    public function delete($id)
    {
        Voyager::can('browse_settings');

        // Check permission
        Voyager::can('visit_settings');

        Setting::destroy($id);

        return back()->with([
            'message'    => 'Successfully Deleted Setting',
            'alert-type' => 'success',
        ]);
    }

    public function move_up($id)
    {
        $setting = Setting::find($id);
        $swapOrder = $setting->order;
        $previousSetting = Setting::where('order', '<', $swapOrder)->orderBy('order', 'DESC')->first();
        $data = [
            'message'    => 'This is already at the top of the list',
            'alert-type' => 'error',
        ];

        if (isset($previousSetting->order)) {
            $setting->order = $previousSetting->order;
            $setting->save();
            $previousSetting->order = $swapOrder;
            $previousSetting->save();

            $data = [
                'message'    => "Moved {$setting->display_name} setting order up",
                'alert-type' => 'success',
            ];
        }

        return back()->with($data);
    }

    public function delete_value($id)
    {
        // Check permission
        Voyager::can('browse_settings');

        $setting = Setting::find($id);

        if (isset($setting->id)) {
            // If the type is an image... Then delete it
            if ($setting->type == 'image') {
                if (Storage::exists(config('voyager.storage.subfolder').$setting->value)) {
                    Storage::delete(config('voyager.storage.subfolder').$setting->value);
                }
            }
            $setting->value = '';
            $setting->save();
        }

        return back()->with([
            'message'    => "Successfully removed {$setting->display_name} value",
            'alert-type' => 'success',
        ]);
    }

    public function move_down($id)
    {
        $setting = Setting::find($id);
        $swapOrder = $setting->order;

        $previousSetting = Setting::where('order', '>', $swapOrder)->orderBy('order', 'ASC')->first();
        $data = [
            'message'    => 'This is already at the bottom of the list',
            'alert-type' => 'error',
        ];

        if (isset($previousSetting->order)) {
            $setting->order = $previousSetting->order;
            $setting->save();
            $previousSetting->order = $swapOrder;
            $previousSetting->save();

            $data = [
                'message'    => "Moved {$setting->display_name} setting order down",
                'alert-type' => 'success',
            ];
        }

        return back()->with($data);
    }
}
