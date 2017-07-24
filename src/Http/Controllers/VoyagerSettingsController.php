<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use TCG\Voyager\Facades\Voyager;

class VoyagerSettingsController extends Controller
{
    public function index()
    {
        // Check permission
        $this->authorize('browse', Voyager::model('Setting'));

        $settings = Voyager::model('Setting')->orderBy('order', 'ASC')->get();

        return Voyager::view('voyager::settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        // Check permission
        $this->authorize('browse', Voyager::model('Setting'));

        $lastSetting = Voyager::model('Setting')->orderBy('order', 'DESC')->first();

        if (is_null($lastSetting)) {
            $order = 0;
        } else {
            $order = intval($lastSetting->order) + 1;
        }

        $request->merge(['order' => $order]);
        $request->merge(['value' => '']);

        Voyager::model('Setting')->create($request->all());

        return back()->with([
            'message'    => __('voyager.settings.successfully_created'),
            'alert-type' => 'success',
        ]);
    }

    public function update(Request $request)
    {
        // Check permission
        $this->authorize('browse', Voyager::model('Setting'));

        $settings = Voyager::model('Setting')->all();

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
            'message'    => __('voyager.settings.successfully_saved'),
            'alert-type' => 'success',
        ]);
    }

    public function delete($id)
    {
        // Check permission
        $this->authorize('browse', Voyager::model('Setting'));

        Voyager::model('Setting')->destroy($id);

        return back()->with([
            'message'    => __('voyager.settings.successfully_deleted'),
            'alert-type' => 'success',
        ]);
    }

    public function move_up($id)
    {
        $setting = Voyager::model('Setting')->find($id);

        // Check permission
        $this->authorize('browse', $setting);

        $swapOrder = $setting->order;
        $previousSetting = Voyager::model('Setting')->where('order', '<', $swapOrder)->orderBy('order', 'DESC')->first();
        $data = [
            'message'    => __('voyager.settings.already_at_top'),
            'alert-type' => 'error',
        ];

        if (isset($previousSetting->order)) {
            $setting->order = $previousSetting->order;
            $setting->save();
            $previousSetting->order = $swapOrder;
            $previousSetting->save();

            $data = [
                'message'    => __('voyager.settings.moved_order_up', ['name' => $setting->display_name]),
                'alert-type' => 'success',
            ];
        }

        return back()->with($data);
    }

    public function delete_value($id)
    {
        $setting = Voyager::model('Setting')->find($id);

        // Check permission
        $this->authorize('browse', $setting);

        if (isset($setting->id)) {
            // If the type is an image... Then delete it
            if ($setting->type == 'image') {
                if (Storage::disk(config('voyager.storage.disk'))->exists($setting->value)) {
                    Storage::disk(config('voyager.storage.disk'))->delete($setting->value);
                }
            }
            $setting->value = '';
            $setting->save();
        }

        return back()->with([
            'message'    => __('voyager.settings.successfully_removed', ['name' => $setting->display_name]),
            'alert-type' => 'success',
        ]);
    }

    public function move_down($id)
    {
        $setting = Voyager::model('Setting')->find($id);

        // Check permission
        $this->authorize('browse', $setting);

        $swapOrder = $setting->order;

        $previousSetting = Voyager::model('Setting')->where('order', '>', $swapOrder)->orderBy('order', 'ASC')->first();
        $data = [
            'message'    => __('voyager.settings.already_at_bottom'),
            'alert-type' => 'error',
        ];

        if (isset($previousSetting->order)) {
            $setting->order = $previousSetting->order;
            $setting->save();
            $previousSetting->order = $swapOrder;
            $previousSetting->save();

            $data = [
                'message'    => __('voyager.settings.moved_order_down', ['name' => $setting->display_name]),
                'alert-type' => 'success',
            ];
        }

        return back()->with($data);
    }
}
