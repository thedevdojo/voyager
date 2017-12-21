<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Constraint;
use Intervention\Image\Facades\Image;
use TCG\Voyager\Events\FileDeleted;
use TCG\Voyager\Traits\AlertsMessages;
use Validator;

abstract class Controller extends BaseController
{
    use DispatchesJobs,
        ValidatesRequests,
        AuthorizesRequests,
        AlertsMessages;

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
        $multi_select = [];

        /*
         * Prepare Translations and Transform data
         */
        $translations = is_bread_translatable($data)
                        ? $data->prepareTranslations($request)
                        : [];

        foreach ($rows as $row) {
            $options = json_decode($row->details);

            // if the field for this row is absent from the request, continue
            // checkboxes will be absent when unchecked, thus they are the exception
            if (!$request->hasFile($row->field) && !$request->has($row->field) && $row->type !== 'checkbox') {
                continue;
            }

            $content = $this->getContentBasedOnType($request, $slug, $row);

            if ($row->type == 'relationship' && $options->type != 'belongsToMany') {
                $row->field = @$options->column;
            }

            /*
             * merge ex_images and upload images
             */
            if ($row->type == 'multiple_images' && !is_null($content)) {
                if (isset($data->{$row->field})) {
                    $ex_files = json_decode($data->{$row->field}, true);
                    if (!is_null($ex_files)) {
                        $content = json_encode(array_merge($ex_files, json_decode($content)));
                    }
                }
            }

            if (is_null($content)) {

                // If the image upload is null and it has a current image keep the current image
                if ($row->type == 'image' && is_null($request->input($row->field)) && isset($data->{$row->field})) {
                    $content = $data->{$row->field};
                }

                // If the multiple_images upload is null and it has a current image keep the current image
                if ($row->type == 'multiple_images' && is_null($request->input($row->field)) && isset($data->{$row->field})) {
                    $content = $data->{$row->field};
                }

                // If the file upload is null and it has a current file keep the current file
                if ($row->type == 'file') {
                    $content = $data->{$row->field};
                }

                if ($row->type == 'password') {
                    $content = $data->{$row->field};
                }
            }

            if ($row->type == 'relationship' && $options->type == 'belongsToMany') {
                // Only if select_multiple is working with a relationship
                $multi_select[] = ['model' => $options->model, 'content' => $content, 'table' => $options->pivot_table];
            } else {
                $data->{$row->field} = $content;
            }
        }

        $data->save();

        // Save translations
        if (count($translations) > 0) {
            $data->saveTranslations($translations);
        }

        foreach ($multi_select as $sync_data) {
            $data->belongsToMany($sync_data['model'], $sync_data['table'])->sync($sync_data['content']);
        }

        return $data;
    }

    public function validateBread($request, $data)
    {
        $rules = [];
        $messages = [];

        foreach ($data as $row) {
            $options = json_decode($row->details);

            if (isset($options->validation)) {
                if (isset($options->validation->rule)) {
                    if (!is_array($options->validation->rule)) {
                        $rules[$row->display_name] = explode('|', $options->validation->rule);
                    } else {
                        $rules[$row->display_name] = $options->validation->rule;
                    }
                }

                if (isset($options->validation->messages)) {
                    foreach ($options->validation->messages as $key => $msg) {
                        $messages[$row->display_name.'.'.$key] = $msg;
                    }
                }
            }
        }

        return Validator::make($request, $rules, $messages);
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
                if ($files = $request->file($row->field)) {
                    if (!is_array($files)) {
                        $files = [$files];
                    }
                    $filesPath = [];
                    foreach ($files as $key => $file) {
                        $filename = Str::random(20);
                        $path = $slug.'/'.date('FY').'/';
                        $file->storeAs(
                            $path,
                            $filename.'.'.$file->getClientOriginalExtension(),
                            config('voyager.storage.disk', 'public')
                        );
                        array_push($filesPath, [
                            'download_link' => $path.$filename.'.'.$file->getClientOriginalExtension(),
                            'original_name' => $file->getClientOriginalName(),
                        ]);
                    }

                    return json_encode($filesPath);
                }
            /********** MULTIPLE IMAGES TYPE **********/
            // no break
            case 'multiple_images':
                if ($files = $request->file($row->field)) {
                    /**
                     * upload files.
                     */
                    $filesPath = [];

                    $options = json_decode($row->details);

                    if (isset($options->resize) && isset($options->resize->width) && isset($options->resize->height)) {
                        $resize_width = $options->resize->width;
                        $resize_height = $options->resize->height;
                    } else {
                        $resize_width = 1800;
                        $resize_height = null;
                    }

                    foreach ($files as $key => $file) {
                        $filename = Str::random(20);
                        $path = $slug.'/'.date('FY').'/';
                        array_push($filesPath, $path.$filename.'.'.$file->getClientOriginalExtension());
                        $filePath = $path.$filename.'.'.$file->getClientOriginalExtension();

                        $image = Image::make($file)->resize(
                            $resize_width,
                            $resize_height,
                            function (Constraint $constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            }
                        )->encode($file->getClientOriginalExtension(), 75);

                        Storage::disk(config('voyager.storage.disk'))->put($filePath, (string) $image, 'public');

                        if (isset($options->thumbnails)) {
                            foreach ($options->thumbnails as $thumbnails) {
                                if (isset($thumbnails->name) && isset($thumbnails->scale)) {
                                    $scale = intval($thumbnails->scale) / 100;
                                    $thumb_resize_width = $resize_width;
                                    $thumb_resize_height = $resize_height;

                                    if ($thumb_resize_width != null) {
                                        $thumb_resize_width = $thumb_resize_width * $scale;
                                    }

                                    if ($thumb_resize_height != null) {
                                        $thumb_resize_height = $thumb_resize_height * $scale;
                                    }

                                    $image = Image::make($file)->resize(
                                        $thumb_resize_width,
                                        $thumb_resize_height,
                                        function (Constraint $constraint) {
                                            $constraint->aspectRatio();
                                            $constraint->upsize();
                                        }
                                    )->encode($file->getClientOriginalExtension(), 75);
                                } elseif (isset($options->thumbnails) && isset($thumbnails->crop->width) && isset($thumbnails->crop->height)) {
                                    $crop_width = $thumbnails->crop->width;
                                    $crop_height = $thumbnails->crop->height;
                                    $image = Image::make($file)
                                        ->fit($crop_width, $crop_height)
                                        ->encode($file->getClientOriginalExtension(), 75);
                                }

                                Storage::disk(config('voyager.storage.disk'))->put(
                                    $path.$filename.'-'.$thumbnails->name.'.'.$file->getClientOriginalExtension(),
                                    (string) $image,
                                    'public'
                                );
                            }
                        }
                    }

                    return json_encode($filesPath);
                }
                break;

            /********** SELECT MULTIPLE TYPE **********/
            case 'select_multiple':
                $content = $request->input($row->field);

                if ($content === null) {
                    $content = [];
                } else {
                    // Check if we need to parse the editablePivotFields to update fields in the corresponding pivot table
                    $options = json_decode($row->details);
                    if (isset($options->relationship) && !empty($options->relationship->editablePivotFields)) {
                        $pivotContent = [];
                        // Read all values for fields in pivot tables from the request
                        foreach ($options->relationship->editablePivotFields as $pivotField) {
                            if (!isset($pivotContent[$pivotField])) {
                                $pivotContent[$pivotField] = [];
                            }
                            $pivotContent[$pivotField] = $request->input('pivot_'.$pivotField);
                        }
                        // Create a new content array for updating pivot table
                        $newContent = [];
                        foreach ($content as $contentIndex => $contentValue) {
                            $newContent[$contentValue] = [];
                            foreach ($pivotContent as $pivotContentKey => $value) {
                                $newContent[$contentValue][$pivotContentKey] = $value[$contentIndex];
                            }
                        }
                        $content = $newContent;
                    }
                }

                return json_encode($content);

            /********** IMAGE TYPE **********/
            case 'image':
                if ($request->hasFile($row->field)) {
                    $file = $request->file($row->field);
                    $options = json_decode($row->details);

                    $path = $slug.'/'.date('FY').'/';
                    if (isset($options->preserveFileUploadName) && $options->preserveFileUploadName) {
                        $filename = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
                        $filename_counter = 1;

                        // Make sure the filename does not exist, if it does make sure to add a number to the end 1, 2, 3, etc...
                        while (Storage::disk(config('voyager.storage.disk'))->exists($path.$filename.'.'.$file->getClientOriginalExtension())) {
                            $filename = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension()).(string) ($filename_counter++);
                        }
                    } else {
                        $filename = Str::random(20);

                        // Make sure the filename does not exist, if it does, just regenerate
                        while (Storage::disk(config('voyager.storage.disk'))->exists($path.$filename.'.'.$file->getClientOriginalExtension())) {
                            $filename = Str::random(20);
                        }
                    }

                    $fullPath = $path.$filename.'.'.$file->getClientOriginalExtension();

                    if (isset($options->resize) && isset($options->resize->width) && isset($options->resize->height)) {
                        $resize_width = $options->resize->width;
                        $resize_height = $options->resize->height;
                    } else {
                        $resize_width = 1800;
                        $resize_height = null;
                    }

                    $image = Image::make($file)->resize(
                        $resize_width,
                        $resize_height,
                        function (Constraint $constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        }
                    )->encode($file->getClientOriginalExtension(), 75);

                    if ($this->is_animated_gif($file)) {
                        Storage::disk(config('voyager.storage.disk'))->put($fullPath, file_get_contents($file), 'public');
                        $fullPathStatic = $path.$filename.'-static.'.$file->getClientOriginalExtension();
                        Storage::disk(config('voyager.storage.disk'))->put($fullPathStatic, (string) $image, 'public');
                    } else {
                        Storage::disk(config('voyager.storage.disk'))->put($fullPath, (string) $image, 'public');
                    }

                    if (isset($options->thumbnails)) {
                        foreach ($options->thumbnails as $thumbnails) {
                            if (isset($thumbnails->name) && isset($thumbnails->scale)) {
                                $scale = intval($thumbnails->scale) / 100;
                                $thumb_resize_width = $resize_width;
                                $thumb_resize_height = $resize_height;

                                if ($thumb_resize_width != null && $thumb_resize_width != 'null') {
                                    $thumb_resize_width = intval($thumb_resize_width * $scale);
                                }

                                if ($thumb_resize_height != null && $thumb_resize_height != 'null') {
                                    $thumb_resize_height = intval($thumb_resize_height * $scale);
                                }

                                $image = Image::make($file)->resize(
                                    $thumb_resize_width,
                                    $thumb_resize_height,
                                    function (Constraint $constraint) {
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    }
                                )->encode($file->getClientOriginalExtension(), 75);
                            } elseif (isset($options->thumbnails) && isset($thumbnails->crop->width) && isset($thumbnails->crop->height)) {
                                $crop_width = $thumbnails->crop->width;
                                $crop_height = $thumbnails->crop->height;
                                $image = Image::make($file)
                                    ->fit($crop_width, $crop_height)
                                    ->encode($file->getClientOriginalExtension(), 75);
                            }

                            Storage::disk(config('voyager.storage.disk'))->put(
                                $path.$filename.'-'.$thumbnails->name.'.'.$file->getClientOriginalExtension(),
                                (string) $image,
                                'public'
                            );
                        }
                    }

                    return $fullPath;
                }
                break;

            /********** TIMESTAMP TYPE **********/
            case 'timestamp':
                $content = $request->input($row->field);
                if (in_array($request->method(), ['PUT', 'POST'])) {
                    if (empty($request->input($row->field))) {
                        $content = null;
                    } else {
                        $content = gmdate('Y-m-d H:i:s', strtotime($request->input($row->field)));
                    }
                }
                break;

            /********** COORDINATES TYPE **********/
            case 'coordinates':
                if (empty($coordinates = $request->input($row->field))) {
                    $content = null;
                } else {
                    //DB::connection()->getPdo()->quote won't work as it quotes the
                    // lat/lng, which leads to wrong Geometry type in POINT() MySQL constructor
                    $lat = (float) ($coordinates['lat']);
                    $lng = (float) ($coordinates['lng']);
                    $content = DB::raw('ST_GeomFromText(\'POINT('.$lat.' '.$lng.')\')');
                }
                break;

            case 'relationship':
                    return $request->input($row->field);
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

    private function is_animated_gif($filename)
    {
        $raw = file_get_contents($filename);

        $offset = 0;
        $frames = 0;
        while ($frames < 2) {
            $where1 = strpos($raw, "\x00\x21\xF9\x04", $offset);
            if ($where1 === false) {
                break;
            } else {
                $offset = $where1 + 1;
                $where2 = strpos($raw, "\x00\x2C", $offset);
                if ($where2 === false) {
                    break;
                } else {
                    if ($where1 + 8 == $where2) {
                        $frames++;
                    }
                    $offset = $where2 + 1;
                }
            }
        }

        return $frames > 1;
    }

    public function deleteFileIfExists($path)
    {
        if (Storage::disk(config('voyager.storage.disk'))->exists($path)) {
            Storage::disk(config('voyager.storage.disk'))->delete($path);
            event(new FileDeleted($path));
        }
    }

    // public function handleRelationshipContent($row, $content){

    //     $options = json_decode($row->details);

    //     switch ($options->type) {
    //         /********** PASSWORD TYPE **********/
    //         case 'belongsToMany':

    //             // $pivotContent = [];
    //             // // Read all values for fields in pivot tables from the request
    //             // foreach ($options->relationship->editablePivotFields as $pivotField) {
    //             //     if (!isset($pivotContent[$pivotField])) {
    //             //         $pivotContent[$pivotField] = [];
    //             //     }
    //             //     $pivotContent[$pivotField] = $request->input('pivot_'.$pivotField);
    //             // }
    //             // // Create a new content array for updating pivot table
    //             // $newContent = [];
    //             // foreach ($content as $contentIndex => $contentValue) {
    //             //     $newContent[$contentValue] = [];
    //             //     foreach ($pivotContent as $pivotContentKey => $value) {
    //             //         $newContent[$contentValue][$pivotContentKey] = $value[$contentIndex];
    //             //     }
    //             // }
    //             // $content = $newContent;

    //                 return [1];

    //             break;

    //         case 'hasMany':

    //         default:

    //             return $content;

    //     }

    //     return $content;

    // }
}
