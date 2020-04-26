<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Plugin\ListWith;
use TCG\Voyager\Facades\Voyager as VoyagerFacade;

class MediaController extends Controller
{
    public function uploadFile(Request $request)
    {
        debug($request->all());
    }

    public function listFiles(Request $request)
    {
        $storage = Storage::disk('public')->addPlugin(new ListWith());
        $files = collect($storage->listWith(['mimetype'], $request->get('path', '')))->transform(function ($file) {
            return [
                'is_upload' => false,
                'file'      => [
                    'type'          => $file['type'],
                    'name'          => $file['basename'],
                    'relative_path' => str_replace('\\', '/', $file['dirname']),
                    'url'           => asset($file['path']),
                    'type'          => $file['mimetype'] ?? 'dir',
                    'size'          => $file['size'] ?? 0,
                ]
            ];
        });

        return response()->json($files);
    }
}
