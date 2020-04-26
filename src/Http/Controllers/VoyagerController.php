<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Bread as BreadFacade;

class VoyagerController extends Controller
{
    public function assets(Request $request)
    {
        $path = str_replace('/', DIRECTORY_SEPARATOR, Str::start(urldecode($request->path), '/'));
        $path = realpath(dirname(__DIR__, 3).'/resources/assets/dist').$path;

        if (realpath($path) != $path) {
            abort(404);
        }

        if (File::exists($path)) {
            $mime = '';
            if (Str::endsWith($path, '.js')) {
                $mime = 'text/javascript';
            } elseif (Str::endsWith($path, '.css')) {
                $mime = 'text/css';
            } elseif (Str::endsWith($path, '.woff')) {
                $mime = 'font/woff';
            } elseif (Str::endsWith($path, '.woff2')) {
                $mime = 'font/woff2';
            } elseif (Str::endsWith($path, '.ttf')) {
                $mime = 'font/ttf';
            } else {
                $mime = File::mimeType($path);
            }
            $response = response(File::get($path), 200, ['Content-Type' => $mime]);
            $response->setSharedMaxAge(31536000);
            $response->setMaxAge(31536000);
            $response->setExpires(new \DateTime('+1 year'));

            return $response;
        }

        abort(404);
    }

    // Search all BREADS
    public function search(Request $request)
    {
        $q = $request->get('query');

        $bread = BreadFacade::getBread($request->get('bread'));
        $results = collect([]);
        if ($bread && $bread->global_search_field) {
            $query = $bread->getModel()->where($bread->global_search_field, 'LIKE', '%'.$q.'%');

            $results = $query->get()->transform(function ($result) use ($bread) {
                return [
                    'id'   => $result->getKey(),
                    'data' => $result->{$bread->global_search_field},
                ];
            });
        }

        return [
            [
                'table'   => $bread->table,
                'results' => $results,
                'count'   => $results->count(),
            ],
        ];
    }
}
