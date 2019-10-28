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
        $bread = BreadFacade::getBread($request->get('bread'));
        $results = collect([]);
        if ($bread) {
            $layout = $bread->getLayoutFor('browse');
            $columns = $layout->getSearchableColumns()->pluck('column')->filter(function ($column) {
                return !Str::contains($column, '.');
                // TODO: Also search for relationships?
            });
            $query = $bread->getModel()->where(function ($query) use ($columns, $q) {
                $columns->each(function ($column) use (&$query, $q) {
                    $query->orWhere($column, 'LIKE', '%'.$q.'%');
                });
            })->orderBy($layout->getDefaultSortColumn());

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
            ],
        ];
    }

    public function searchRelationship(Request $request)
    {
        $bread = BreadFacade::getBread($request->get('table'));
        $multiple = false;
        $results = [];
        if ($bread) {
            $assigned = null;
            $model = $bread->getModel();

            if ($request->get('primary', null)) {
                $model = $model->findOrFail($request->get('primary'));
                $related = $model->{$request->get('relationship')}()->getRelated();
                $query = $request->get('query', null);
                $foreign_key = $related->getKeyName();
                if ($query) {
                    $related = $related->where($request->get('column'), 'LIKE', '%'.$query.'%')->get();
                }
                $related_entries = $related->pluck($request->get('column'), $foreign_key);
            } else {
                $related = $model->{$request->get('relationship')}()->getRelated();
                $foreign_key = $related->getKeyName();
                $related_entries = $related->pluck($request->get('column'), $foreign_key);
            }

            $assigned = $model->{$request->get('relationship')};

            foreach ($related_entries as $key => $label) {
                $is_assigned = false;
                if ($assigned instanceof Collection) {
                    $multiple = true;
                    $is_assigned = $assigned->contains($foreign_key, $key);
                } elseif ($assigned) {
                    $is_assigned = $assigned->{$foreign_key} == $key;
                }
                $results[] = [
                    'key'      => $key,
                    'label'    => $label,
                    'assigned' => $is_assigned,
                ];
            }
        }

        return [
            'multiple' => $multiple,
            'results'  => $results,
        ];
    }

    public function addRelationship(Request $request)
    {
        $bread = BreadFacade::getBread($request->get('table'));
        $result = '';
        if ($bread) {
            $model = $bread->getModel()->findOrFail($request->get('primary'));

            $related = $model->{$request->get('relationship')}()->getRelated();
            $tag = $request->get('tag', null);
            $data = new $related();
            $data->{$request->get('column')} = $tag;
            $data->save();

            $result = [
                'key'      => $data->getKey(),
                'label'    => $tag,
                'assigned' => false,
            ];
        }

        return $result;
    }

    public function getOptions(Request $request)
    {
        $results = [];

        $controller = Str::start($request->get('controller'), '\\');
        $method = $request->get('method');
        $selected = $request->get('selected');

        if (class_exists($controller) && method_exists(new $controller(), $method)) {
            $results = call_user_func([new $controller(), $method], $selected);
        }

        return $results;
    }
}
