<?php

namespace TCG\Voyager\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use TCG\Voyager\Models\User as User;
use Intervention\Image\Facades\Image;
use \Storage;
use TCG\Voyager\Voyager;

class VoyagerController extends Controller
{

	public function index()
	{
		return view('voyager::index');
	}

	public function logout()
	{
		Auth::logout();
		return redirect(route('voyager.logout'));
	}

	public function upload(Request $request){
		$valid_exts = array('jpeg', 'jpg', 'png', 'gif'); // valid extensions
		$full_filename = NULL;

        $slug = $request->input('type_slug');
        $file = $request->file('image');
        $filename = str_random(20);

        $path =  $slug . '/' . date('F') . date('Y') . '/'; 
        $full_path = $path . $filename . '.' . $file->getClientOriginalExtension();

        $ext = $file->guessClientExtension();

        $resize_width = 1800;
        $resize_height = null;
       
        if (in_array($ext, $valid_exts))
        {
            $image = Image::make($file)->resize($resize_width, $resize_height, function($constraint){ 
                $constraint->aspectRatio();
                $constraint->upsize();
            })->encode($file->getClientOriginalExtension(), 75);
            
            // move uploaded file from temp to uploads directory
            if (Storage::put(config('voyager.storage.subfolder') . $full_path, (string)$image, 'public'))
            {
                $status = 'Image successfully uploaded!';
                $full_filename = $full_path;
            }
            else {
                $status = 'Upload Fail: Unknown error occurred!';
            }
        }
        else {

            $status = 'Upload Fail: Unsupported file format or It is too large to upload!';
        }

        //echo out script that TinyMCE can handle and update the image in the editor

        return ("<script> parent.setImageValue('" . Voyager::image( $full_filename ) . "'); </script>"); 
    }
}