<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use TCG\Voyager\Models\MenuItem;

class Menu extends Model
{
    protected $table = 'menus';

    public function items()
    {
        return $this->hasMany('MenuItem');
    }

    static public function display($menu_name, $type = null, $options = [])
    {
        $instance = new static;

        // GET THE MENU
        $menu = $instance->where('name', '=', $menu_name)->first();

        $menu_items = [];
        if (isset($menu->id)) {
            // GET THE ROOT MENU ITEMS
            $menu_items = MenuItem::where('menu_id', '=', $menu->id)->where('parent_id', '=', null)->orderBy('order',
                'ASC')->get();
        }

        // Convert options array into object
        $options = (object)$options;

        if ($type == 'admin') {
            $output = self::buildAdminOutput($menu_items, '', $options);
        } else {
            if ($type == 'admin_menu') {
                $output = self::buildAdminMenuOutput($menu_items, '', $options, Request());
            } else {
                if ($type == 'bootstrap') {
                    $output = self::buildBootstrapOutput($menu_items, '', $options, Request());
                } else {
                    $output = self::buildOutput($menu_items, '', $options, Request());
                }
            }
        }


        return $output;
    }

    static public function buildBootstrapOutput($menu_items, $output, $options, Request $request)
    {

        if(empty($output)){
            $output = '<ul class="nav navbar-nav">';
        } else{        
            $output .= '<ul class="dropdown-menu">';
        }

        foreach ($menu_items as $item):
            $li_class = '';
            $a_attrs = '';
            if ($request->is(ltrim($item->url, '/')) || $item->url == '/' && $request->is('/')):
                $li_class = ' class="active"';
            endif;
            $children_menu_items = MenuItem::where('parent_id', '=', $item->id)->orderBy('order', 'ASC')->get();
            if (count($children_menu_items) > 0) {
                if ($li_class != '') {
                    $li_class = rtrim($li_class, '"') . ' dropdown"';
                } else {
                    $li_class = ' class="dropdown"';
                }
                $a_attrs = 'class="dropdown-toggle" ';
            }
            $icon = '';
            if (isset($item->icon_class)) {
                $icon = '<span class="icon ' . $item->icon_class . '"></span>';
            }
            $styles = '';
            if (isset($options->color) && $options->color == true) {
                $styles = ' style="color:' . $item->color . '"';
            }
            $background = '';
            if (isset($options->background) && $options->background == true) {
                $styles = ' style="background-color:' . $item->color . '"';
            }
            $output .= '<li' . $li_class . '><a ' . $a_attrs . ' href="' . $item->url . '" target="' . $item->target . '"' . $styles . '>' . $icon . '<span class="title">' . $item->title . '</span></a>';
            if (count($children_menu_items) > 0) {
                $output = self::buildBootstrapOutput($children_menu_items, $output, $options, $request);
            }
            $output .= '</li>';
        endforeach;
        $output .= '</ul>';
        return $output;
    }

    static public function buildOutput($menu_items, $output, $options, Request $request)
    {

        if (empty($output)) {
            $output = '<ul>';
        } else {

            $output .= '<ul>';
        }

        foreach ($menu_items as $item):

            $li_class = '';
            $a_attrs = '';
            if ($request->is(ltrim($item->url, '/')) || $item->url == '/' && $request->is('/')):
                $li_class = ' class="active"';
            endif;

            $children_menu_items = MenuItem::where('parent_id', '=', $item->id)->orderBy('order', 'ASC')->get();

            $icon = '';
            if (isset($options->icon) && $options->icon == true) {
                $icon = '<i class="' . $item->icon_class . '"></i>';
            }

            $styles = '';
            if (isset($options->color) && $options->color == true) {
                $styles = ' style="color:' . $item->color . '"';
            }

            $background = '';
            if (isset($options->background) && $options->background == true) {
                $styles = ' style="background-color:' . $item->color . '"';
            }

            $output .= '<li' . $li_class . '><a href="' . $item->url . '" target="' . $item->target . '"' . $styles . '>' . $icon . '<span>' . $item->title . '</span></a>';


            if (count($children_menu_items) > 0) {
                $output = self::buildOutput($children_menu_items, $output, $options, $request);
            }

            $output .= '</li>';

        endforeach;

        $output .= '</ul>';

        return $output;
    }

    static public function buildAdminMenuOutput($menu_items, $output, $options, Request $request)
    {

        $output .= '<ul class="nav navbar-nav">';

        foreach ($menu_items as $item):

            $li_class = '';
            $a_attrs = '';
            $collapse_id = '';
            if ($request->is(ltrim($item->url, '/'))):
                $li_class = ' class="active"';
            endif;

            $children_menu_items = MenuItem::where('parent_id', '=', $item->id)->orderBy('order', 'ASC')->get();
            if (count($children_menu_items) > 0) {
                if ($li_class != '') {
                    $li_class = rtrim($li_class, '"') . ' dropdown"';
                } else {
                    $li_class = ' class="dropdown"';
                }
                $collapse_id = str_slug($item->title, "-") . '-dropdown-element';
                $a_attrs = 'data-toggle="collapse" href="#' . $collapse_id . '"';

            } else {
                $a_attrs = 'href="' . $item->url . '"';
            }

            $output .= '<li' . $li_class . '><a ' . $a_attrs . ' target="' . $item->target . '">'
                . '<span class="icon ' . $item->icon_class . '"></span>'
                . '<span class="title">' . $item->title . '</span></a>';


            if (count($children_menu_items) > 0) {
                // Add tag for collapse panel
                $output .= '<div id="' . $collapse_id . '" class="panel-collapse collapse"><div class="panel-body">';

                $output = self::buildAdminMenuOutput($children_menu_items, $output, [], $request);

                $output .= '</div></div>';      // close tag of collapse panel
            }

            $output .= '</li>';

        endforeach;

        return $output;
    }

    static public function buildAdminOutput($menu_items, $output, $options)
    {

        $output .= '<ol class="dd-list">';

        foreach ($menu_items as $item):
            $output .= '<li class="dd-item" data-id="' . $item->id . '">';
            $output .= '<div class="pull-right item_actions">';
            $output .= '<div class="btn-sm btn-danger pull-right delete" data-id="' . $item->id . '"><i class="voyager-trash"></i> Delete</div>';
            $output .= '<div class="btn-sm btn-primary pull-right edit" data-id="' . $item->id . '" data-title="' . $item->title . '" data-url="' . $item->url . '" data-target="' . $item->target . '" data-icon_class="' . $item->icon_class . '" data-color="' . $item->color . '"><i class="voyager-edit"></i> Edit</div>';
            $output .= '</div>';
            $output .= '<div class="dd-handle">' . $item->title . ' <small class="url">' . $item->url . '</small></div>';

            $children_menu_items = MenuItem::where('parent_id', '=', $item->id)->orderBy('order', 'ASC')->get();

            if (count($children_menu_items) > 0) {
                $output = self::buildAdminOutput($children_menu_items, $output, $options);
            }

            $output .= '</li>';

        endforeach;

        $output .= '</ol>';

        return $output;
    }
}
