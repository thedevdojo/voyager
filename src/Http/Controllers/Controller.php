<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Constraint;
use Intervention\Image\Facades\Image;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getSlug(Request $request)
    {
        if (isset($this->slug)) {
            $slug = $this->slug;
        } else {
            $slug = explode('.', $request->route()->getName())[1];
        }

        return $slug;
    }

    public function insertUpdateData($request, $slug, $rows, $data)
    {
        $rules = [];
        $messages = [];

        foreach ($rows as $row) {
            $options = json_decode($row->details);
            if (isset($options->validation)) {
                if (isset($options->validation->rule)) {
                    $rules[$row->field] = $options->validation->rule;
                }
                if (isset($options->validation->messages)) {
                    foreach ($options->validation->messages as $key => $msg) {
                        $messages[$row->field.'.'.$key] = $msg;
                    }
                }
            }

            $content = $this->getContentBasedOnType($request, $slug, $row);
            if ($content === null) {
                if (isset($data->{$row->field})) {
                    $content = $data->{$row->field};
                }
                if ($row->field == 'password') {
                    $content = $data->{$row->field};
                }
            }
            if ($row->type == 'select_multiple') {
                // do nothing
            } else {
                $data->{$row->field} = $content;
            }
        }

        $this->validate($request, $rules, $messages);

        $data->save();

        if ($row->type == 'select_multiple') {
            $data->{$row->field}()->sync($content);
        }

        return $data;
    }

    public function getContentBasedOnType(Request $request, $slug, $row)
    {
        $content = null;
        switch ($row->type) {
            /********** PASSWORD TYPE **********/
            case 'password':
                $pass_field = $request->input($row->field);

                if (isset($pass_field) && !empty($pass_field)) {
                    return bcrypt($request->input($row->field));
                }
                break;

            /********** CHECKBOX TYPE **********/
            case 'checkbox':
                $checkBoxRow = $request->input($row->field);

                if (isset($checkBoxRow)) {
                    return 1;
                }

                $content = 0;
                break;

            /********** FILE TYPE **********/
            case 'file':
                if ($file = $request->file($row->field)) {
                    $filename = Str::random(20);
                    $path = $slug.'/'.date('F').date('Y').'/';
                    $fullPath = $path.$filename.'.'.$file->getClientOriginalExtension();

                    Storage::put(config('voyager.storage.subfolder').$fullPath, (string) $file, 'public');

                    return $fullPath;
                }
            // no break

            /********** SELECT MULTIPLE TYPE **********/
            case 'select_multiple':
                $content = $request->input($row->field);
                if ($content === null) {
                    $content = [];
                }

                return $content;

            /********** IMAGE TYPE **********/
            case 'image':
                if ($request->hasFile($row->field)) {
                    $storage_disk = 'local';
                    $file = $request->file($row->field);
                    $filename = Str::random(20);

                    $path = $slug.'/'.date('F').date('Y').'/';
                    $fullPath = $path.$filename.'.'.$file->getClientOriginalExtension();

                    $options = json_decode($row->details);

                    if (isset($options->resize) && isset($options->resize->width) && isset($options->resize->height)) {
                        $resize_width = $options->resize->width;
                        $resize_height = $options->resize->height;
                    } else {
                        $resize_width = 1800;
                        $resize_height = null;
                    }

                    $image = Image::make($file)->resize($resize_width, $resize_height,
                        function (Constraint $constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        })->encode($file->getClientOriginalExtension(), 75);

                    Storage::put(config('voyager.storage.subfolder').$fullPath, (string) $image, 'public');

                    if (isset($options->thumbnails)) {
                        foreach ($options->thumbnails as $thumbnails) {
                            if (isset($thumbnails->name) && isset($thumbnails->scale)) {
                                $scale = intval($thumbnails->scale) / 100;
                                $thumb_resize_width = $resize_width;
                                $thumb_resize_height = $resize_height;
                                if ($thumb_resize_width != 'null') {
                                    $thumb_resize_width = $thumb_resize_width * $scale;
                                }
                                if ($thumb_resize_height != 'null') {
                                    $thumb_resize_height = $thumb_resize_height * $scale;
                                }
                                $image = Image::make($file)->resize($thumb_resize_width, $thumb_resize_height,
                                    function (Constraint $constraint) {
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    })->encode($file->getClientOriginalExtension(), 75);
                            } elseif (isset($options->thumbnails) && isset($thumbnails->crop->width) && isset($thumbnails->crop->height)) {
                                $crop_width = $thumbnails->crop->width;
                                $crop_height = $thumbnails->crop->height;
                                $image = Image::make($file)->fit($crop_width,
                                    $crop_height)->encode($file->getClientOriginalExtension(), 75);
                            }

                            Storage::put(config('voyager.storage.subfolder').$path.$filename.'-'.$thumbnails->name.'.'.$file->getClientOriginalExtension(),
                                (string) $image, 'public');
                        }
                    }

                    return $fullPath;
                }
                break;

            /********** ALL OTHER TEXT TYPE **********/
            default:
                return $request->input($row->field);
            // no break
        }

        return $content;
    }

    public function generate_views(Request $request)
    {
        //$dataType = DataType::where('slug', '=', $slug)->first();
    }

    public function deleteFileIfExists($path)
    {
        if (Storage::exists($path)) {
            Storage::delete($path);
        }
    }
}
