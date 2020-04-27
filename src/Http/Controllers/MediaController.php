<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\Plugin\ListWith;

class MediaController extends Controller
{
    public function index()
    {
        return view('voyager::media.browse');
    }

    public function uploadFile(Request $request)
    {
        $path = Str::finish($request->get('path', ''), '/');
        $file = $request->file('file');
        $name = '';
        $count = 0;
        do {
            $name = $this->getFileName($file, $count);
            $count++;
        } while (Storage::disk('public')->exists($path.$name));

        return response()->json([
            'result' => Storage::disk('public')->putFileAs($path, $request->file('file'), $name),
        ]);
    }

    public function listFiles(Request $request)
    {
        $storage = Storage::disk('public')->addPlugin(new ListWith());
        $files = collect($storage->listWith(['mimetype'], $request->get('path', '')))->transform(function ($file) {
            return [
                'is_upload' => false,
                'file'      => [
                    'name'          => $file['basename'],
                    'relative_path' => Str::finish(str_replace('\\', '/', $file['dirname']), '/'),
                    'url'           => Storage::disk('public')->url($file['path']),
                    'type'          => $file['mimetype'] ?? 'dir',
                    'size'          => $file['size'] ?? 0,
                ],
            ];
        })->sortBy('file.name')->sortBy(function ($file) {
            return $file['file']['type'] == 'dir' ? 0 : 99999999;
        })->values();

        return response()->json($files);
    }

    public function delete(Request $request)
    {
        foreach ($request->get('files', []) as $file) {
            //debug($file);
        }

        return Storage::disk('public')->delete($request->get('files', []));
    }

    public function createFolder(Request $request)
    {
        return Storage::disk('public')->makeDirectory(
            Str::finish($request->get('path', ''), '/').$request->get('name', '')
        );
    }

    // Checks if a file exists and add a numbered prefix until the file does not exist.
    private function getFileName($file, $count = 0)
    {
        $pathinfo = pathinfo($file->getClientOriginalName());
        $name = $pathinfo['filename'];
        if ($count >= 1) {
            $name .= '_'.$count;
        }

        return $name.'.'.$pathinfo['extension'];
    }
}
