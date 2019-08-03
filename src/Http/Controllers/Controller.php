<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use TCG\Voyager\Facades\Voyager;

abstract class Controller extends BaseController
{
    use AuthorizesRequests;

    public function getBread(Request $request)
    {
        $slug = explode('.', $request->route()->getName())[1];

        return Voyager::getBreadBySlug($slug);
    }

    protected function searchQuery(&$query, $filters)
    {
        foreach ($filters as $field => $filter) {
            // TODO: Search relationships and translatable
            $query = $query->where($field, 'like', '%'.$filter.'%');
        }
    }
    protected function orderQuery(&$query, $field, $direction)
    {
        // TODO: Order by relationship or translatable
        $orderMethod = 'sortBy'.($direction == 'asc' ? '' : 'Desc');
        $query = $query->get()->$orderMethod($field);
    }
}
