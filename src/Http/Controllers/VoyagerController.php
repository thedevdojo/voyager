<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;

class VoyagerController extends Controller
{
    public function assets(Request $request)
    {
        $path = Str::start(str_replace(['../', './'], '', urldecode($request->path)), '/');
        $path = Str::finish(dirname(__FILE__, 4), '/').'resources/assets/dist'.$path;
        if (File::exists($path)) {
            $mime = '';
            if (Str::endsWith($path, '.js')) {
                $mime = 'text/javascript';
            } elseif (Str::endsWith($path, '.css')) {
                $mime = 'text/css';
            } else {
                $mime = File::mimeType($path);
            }
            $response = response(File::get($path), 200, ['Content-Type' => $mime]);
            $response->setSharedMaxAge(31536000);
            $response->setMaxAge(31536000);
            $response->setExpires(new \DateTime('+1 year'));

            return $response;
        }

        return response('Not found', 404);
    }

    public function search(Request $request)
    {
        $q = $request->get('query');
        $bread = Voyager::getBread($request->get('bread'));
        $results = collect([]);
        if ($bread) {
            $layout = $bread->getLayoutFor('browse');
            $fields = $layout->getSearchableFields()->pluck('field');
            $query = $bread->getModel()->where(function ($query) use ($fields, $q) {
                $fields->each(function ($field) use (&$query, $q) {
                    $query->orWhere($field, 'LIKE', '%'.$q.'%');
                });
                
            });
            
            $results = $query->get()->transform(function ($result) use ($layout) {
                return [
                    'id'   => $result->getKey(),
                    'data' => $result->{$layout->global_search},
                ];
            });
        }

        return [
            [
                'bread'   => $bread->name_plural,
                'results' => $results,
                'count'   => $results->count(),
            ]
        ];
    }
}
