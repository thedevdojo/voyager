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
    use DispatchesJobs,
        ValidatesRequests,
        AuthorizesRequests;

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
        $multi_select = [];

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

            if (is_null($content)) {
                if (isset($data->{$row->field})) {
                    $content = $data->{$row->field};
                }
                if ($row->field == 'password') {
                    $content = $data->{$row->field};
                }
            }

            if ($row->type == 'select_multiple') {
                array_push($multi_select, ['row' => $row->field, 'content' => $content]);
            } else {
                $data->{$row->field} = $content;
            }
        }

        $this->validate($request, $rules, $messages);

        $data->save();

        foreach ($multi_select as $sync_data) {
            $data->{$sync_data['row']}()->sync($sync_data['content']);
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

                    $request->file($row->field)->storeAs(config('voyager.storage.subfolder').$path, $filename.'.'.$file->getClientOriginalExtension());

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
                                $image = Image::make($file)
                                    ->fit($crop_width, $crop_height)
                                    ->encode($file->getClientOriginalExtension(), 75);
                            }

                            Storage::put(
                                config('voyager.storage.subfolder').$path.$filename.'-'.$thumbnails->name.'.'.$file->getClientOriginalExtension(),
                                (string) $image, 'public'
                            );
                        }
                    }

                    return $fullPath;
                }
                break;

            /********** TIMESTAMP TYPE **********/
            case 'timestamp':
                if ($request->isMethod('PUT')) {
                    $content = gmdate('Y-m-d H:i:s', strtotime($request->input($row->field)));
                }
                break;

            /********** ALL OTHER TEXT TYPE **********/
            default:            
                $value = $request->input($row->field);
                $options = json_decode($row->details);
                if (isset($options->null)) {
                    return $value == $options->null ? null : $value;                    
                }
                return $value;
        }

        return $content;
    }

    public function deleteFileIfExists($path)
    {
        if (Storage::exists($path)) {
            Storage::delete($path);
        }
    }
}
