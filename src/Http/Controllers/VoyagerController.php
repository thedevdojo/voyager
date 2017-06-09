<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Constraint;
use Intervention\Image\Facades\Image;
use TCG\Voyager\Facades\Voyager;

class VoyagerController extends Controller
{
    public function index()
    {
        return view('voyager::index');
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('voyager.login');
    }

    public function lockScreen()
    {
        $user = Voyager::model('User')->find(Auth::id());

        if (!$user) {
            session()->flush();

            return redirect()->route('voyager.login');
        }
        session(['admin_lock' => 1]);

        return view('voyager::lockscreen');
    }

    public function unlockScreen(Request $request)
    {
        $id = Auth::user()->id;
        $password = $request->password;
        $users = Voyager::model('User')->find($id);

        if (\Hash::check($password, $users->password)) {
            session(['admin_lock' => 0]);

            return redirect()->route('voyager.dashboard');
        }

        return redirect()->route('voyager.lock')->with($this->alertError("Password is incorrect!"));
    }

    public function upload(Request $request)
    {
        $fullFilename = null;
        $resizeWidth = 1800;
        $resizeHeight = null;
        $slug = $request->input('type_slug');
        $file = $request->file('image');
        $filename = Str::random(20);
        $fullPath = $slug.'/'.date('F').date('Y').'/'.$filename.'.'.$file->getClientOriginalExtension();

        $ext = $file->guessClientExtension();

        if (in_array($ext, ['jpeg', 'jpg', 'png', 'gif'])) {
            $image = Image::make($file)
                ->resize($resizeWidth, $resizeHeight, function (Constraint $constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode($file->getClientOriginalExtension(), 75);

            // move uploaded file from temp to uploads directory
            if (Storage::disk(config('voyager.storage.disk'))->put($fullPath, (string) $image, 'public')) {
                $status = 'Image successfully uploaded!';
                $fullFilename = $fullPath;
            } else {
                $status = 'Upload Fail: Unknown error occurred!';
            }
        } else {
            $status = 'Upload Fail: Unsupported file format or It is too large to upload!';
        }

        // echo out script that TinyMCE can handle and update the image in the editor
        return "<script> parent.setImageValue('".Voyager::image($fullFilename)."'); </script>";
    }

    public function profile()
    {
        return view('voyager::profile');
    }
}
