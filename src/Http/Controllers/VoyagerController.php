<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Support\Facades\File;

class VoyagerController extends Controller
{
    public function assets(Request $request)
    {
        $path = str_start(str_replace(['../', './'], '', urldecode($request->path)), '/');
        $path = base_path('vendor/tcg/voyager/resources/assets/dist'.$path);
        if (File::exists($path)) {
            $mime = '';
            if (ends_with($path, '.js')) {
                $mime = 'text/javascript';
            } elseif (ends_with($path, '.css')) {
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

        return response('', 404);
    }
}
