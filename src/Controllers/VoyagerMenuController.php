<?php

namespace TCG\Voyager\Controllers;

use App\Http\Controllers\Controller;
use \Redirect as Redirect;
use \TCG\Voyager\Models\Setting;
use Illuminate\Http\Request;
use \Storage;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\MenuItem;

class VoyagerMenuController extends Controller
{

    public function builder($id)
    {
        $menu = Menu::find($id);
        return view('voyager::menus.builder', ['menu' => $menu]);
    }

    public function delete_menu($id)
    {
        $item = MenuItem::find($id);
        $menu_id = $item->menu_id;
        $item->destroy($id);
        return redirect(route('voyager.menu.builder',
            $menu_id))->with(['message' => 'Successfully Deleted Menu Item.', 'alert-type' => 'success']);
    }

    public function add_item(Request $request)
    {
        $data = $request->all();
        $highest_order_menu_item = MenuItem::where('parent_id', '=', null)->orderBy('order', 'DESC')->first();
        if (isset($highest_order_menu_item->id)) {
            $data['order'] = intval($highest_order_menu_item->order) + 1;
        } else {
            $data['order'] = 1;
        }
        MenuItem::create($data);
        return redirect(route('voyager.menu.builder', $data['menu_id']))->with([
            'message' => 'Successfully Created New Menu Item.',
            'alert-type' => 'success'
        ]);
    }

    public function update_item(Request $request)
    {
        $id = $request->input('id');
        $data = $request->all();
        unset($data['id']);
        $menu_item = MenuItem::find($id);
        $menu_item->update($data);
        return redirect(route('voyager.menu.builder', $menu_item->menu_id))->with([
            'message' => 'Successfully Updated Menu Item.',
            'alert-type' => 'success'
        ]);
    }

    public function order_item(Request $request)
    {
        $menu_item_order = json_decode($request->input('order'));
        $this->order_menu($menu_item_order, null);
    }

    private function order_menu($menu_items, $parent_id)
    {
        foreach ($menu_items as $index => $menu_item):
            $item = MenuItem::find($menu_item->id);
            $item->order = $index + 1;
            $item->parent_id = $parent_id;
            $item->save();
            if (isset($menu_item->children)) {
                $this->order_menu($menu_item->children, $item->id);
            }
        endforeach;
    }
}