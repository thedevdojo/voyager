<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Constraint;
use Intervention\Image\Facades\Image;
use TCG\Voyager\Facades\Voyager;

class VoyagerController extends Controller
{
    public function index()
    {
        return Voyager::view('voyager::index');
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('voyager.login');
    }

    public function upload(Request $request)
    {
        $fullFilename = null;
        $resizeWidth = 1800;
        $resizeHeight = null;
        $slug = $request->input('type_slug');
        $file = $request->file('image');

        $path = $slug.'/'.date('F').date('Y').'/';

        $filename = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
        $filename_counter = 1;

        // Make sure the filename does not exist, if it does make sure to add a number to the end 1, 2, 3, etc...
        while (Storage::disk(config('voyager.storage.disk'))->exists($path.$filename.'.'.$file->getClientOriginalExtension())) {
            $filename = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension()).(string) ($filename_counter++);
        }

        $fullPath = $path.$filename.'.'.$file->getClientOriginalExtension();

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
                $status = __('voyager::media.success_uploading');
                $fullFilename = $fullPath;
            } else {
                $status = __('voyager::media.error_uploading');
            }
        } else {
            $status = __('voyager::media.uploading_wrong_type');
        }

        // echo out script that TinyMCE can handle and update the image in the editor
        return "<script> parent.helpers.setImageValue('".Voyager::image($fullFilename)."'); </script>";
    }

    public function profile(Request $request)
    {
        $id = auth()->user()->id;

        return redirect()->route('voyager.users.showProfile', $id);
    }

    public function showProfile(Request $request, $id)
    {
        if ($id == auth()->user()->id) {
            return Voyager::view('voyager::profile');
        } else {
            abort(404);
        }
    }

    public function editProfile(Request $request, $id)
    {
        if ($id == auth()->user()->id) {
            $voyagerBaseController = new VoyagerBaseController();

            return $voyagerBaseController->edit($request, $id);
        } else {
            abort(404);
        }
    }

    public function updateProfile(Request $request, $id)
    {
        if ($id == auth()->user()->id) {
            // If can't edit users can't change his own role but need to preserve roles set
            if (!auth::user()->hasPermission('edit_users')) {
                $role = Voyager::model('Role');
                $roles = auth::user()->belongsToMany($role, 'user_roles')
                            ->pluck($role->getTable().'.'.$role->getKeyName())
                            ->all();

                $params = $request->all();
                $params['role_id'] = Auth::user()->role_id;
                $params['user_belongstomany_role_relationship'] = $roles;

                $request->replace($params);
            }
            $voyagerBaseController = new VoyagerBaseController();

            return $voyagerBaseController->update($request, $id);
        } else {
            abort(403);
        }
    }
}
