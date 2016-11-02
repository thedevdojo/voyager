<?php

namespace TCG\Voyager\Controllers;

use App\Http\Controllers\Controller;
use \Redirect as Redirect;
use \TCG\Voyager\Models\Setting;
use Illuminate\Http\Request;
use \Storage;

class VoyagerSettingsController extends Controller
{

    public function index()
    {
        $settings = Setting::orderBy('order', 'ASC')->get();
        return view('voyager::settings.index', compact('settings'));
    }

    public function create(Request $request)
    {
        $last_setting = Setting::orderBy('order', 'DESC')->first();
        $order = intval($last_setting->order) + 1;
        $request->merge(['order' => $order]);
        $request->merge(['value' => '']);
        Setting::create($request->all());
        return back()->with(array('message' => 'Successfully Created New Setting', 'alert-type' => 'success'));
    }

    public function delete(Request $request, $id)
    {
        Setting::destroy($id);
        return back()->with(array('message' => 'Successfully Deleted Setting', 'alert-type' => 'success'));
    }

    public function move_up(Request $request, $id)
    {
        $setting = Setting::find($id);
        $swap_order = $setting->order;

        $previous_setting = Setting::where('order', '<', $swap_order)->orderBy('order', 'DESC')->first();

        if (isset($previous_setting->order)) {
            $setting->order = $previous_setting->order;
            $setting->save();
            $previous_setting->order = $swap_order;
            $previous_setting->save();

            return back()->with(array(
                'message' => 'Moved ' . $setting->display_name . ' setting order up',
                'alert-type' => 'success'
            ));
        } else {
            return back()->with(array('message' => 'This is already at the top of the list', 'alert-type' => 'error'));
        }
    }

    public function delete_value($id)
    {
        $setting = Setting::find($id);
        if (isset($setting->id)) {
            // If the type is an image... Then delete it
            if ($setting->type == 'image') {
                if (Storage::exists(config('voyager.storage.subfolder') . $setting->value)) {
                    Storage::delete(config('voyager.storage.subfolder') . $setting->value);
                }
            }
            $setting->value = '';
            $setting->save();
        }
        return back()->with(array(
            'message' => 'Successfully removed ' . $setting->display_name . ' value',
            'alert-type' => 'success'
        ));
    }

    public function move_down(Request $request, $id)
    {
        $setting = Setting::find($id);
        $swap_order = $setting->order;

        $previous_setting = Setting::where('order', '>', $swap_order)->orderBy('order', 'ASC')->first();

        if (isset($previous_setting->order)) {
            $setting->order = $previous_setting->order;
            $setting->save();
            $previous_setting->order = $swap_order;
            $previous_setting->save();

            return back()->with(array(
                'message' => 'Moved ' . $setting->display_name . ' setting order down',
                'alert-type' => 'success'
            ));
        } else {
            return back()->with(array(
                'message' => 'This is already at the bottom of the list',
                'alert-type' => 'error'
            ));
        }
    }

    public function save(Request $request)
    {

        $settings = Setting::all();
        $breadController = new VoyagerBreadController;

        foreach ($settings as $setting) {
            $rows = [];
            $row = ['type' => $setting->type, 'field' => $setting->key, 'details' => $setting->details];
            $content = $breadController->getContentBasedOnType($request, 'settings', (object)$row);

            if ($content === null) {
                if (isset($setting->value)) {
                    $content = $setting->value;
                }
            }

            $setting->value = $content;
            $setting->save();
        }


        return back()->with(array('message' => 'Successfully Saved Settings', 'alert-type' => 'success'));


    }

}

