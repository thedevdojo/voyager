<?php
/**
 * Created by Gabriel Takács, gabriel.takacs@ui42.sk
 */

namespace TCG\Voyager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Routing\ControllerDispatcher;
use Illuminate\Routing\Route;
use TCG\Exceptions\InvalidActionException;
use TCG\Voyager\Models\Page;

class DynamicRouteController extends Controller
{
    public function handle($slug)
    {
        $controller_name = null;
        $action_name = null;

        /** @var Page $page */
        $page = Page::where('slug', $slug)->first();
        if (!is_null($page)) {
            $page_type = $page->pageTypeId()->getResults();
            $controller_name = '\\App\\Http\\Controllers\\' . $page_type->controller;
            $action_name = $page_type->action;
        }

        try {
            $route = app()->make(Route::class);
            $controller  = app()->make($controller_name);

            if (empty($action_name) || false === method_exists($controller, $action_name)) {
                throw new InvalidActionException('Invalid controller-action pair: "' . $controller_name . ':' . $action_name . '"!');
            }
        } catch (\Exception $e) {
            abort(404);
            return '';
        }

        return (new ControllerDispatcher(app()))
            ->dispatch($route, $controller, $action_name);
    }
}
