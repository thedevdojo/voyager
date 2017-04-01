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
use TCG\Voyager\Traits\AlertsMessages;

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
        $rules = [];
        $messages = [];
        $multi_select = [];

        /*
         * Prepare Translations and Transform data
         */
        $translations = isBreadTranslatable($data)
                        ? $data->prepareTranslations($request)
                        : [];

        foreach ($rows as $row) {
            $options = json_decode($row->details);

            if (isset($options->validation)) {
                if (isset($options->validation->rule)) {
                    if (!is_array($options->validation->rule)) {
                        $rules[$row->field] = explode('|', $options->validation->rule);
                    } else {
                        $rules[$row->field] = $options->validation->rule;
                    }
                }

                if (isset($options->validation->messages)) {
                    foreach ($options->validation->messages as $key => $msg) {
                        $messages[$row->field.'.'.$key] = $msg;
                    }
                }
            }

            $content = $this->getContentBasedOnType($request, $slug, $row);

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
                // Only set the content back to the previous value when there is really now input for this field
                if (is_null($request->input($row->field)) && isset($data->{$row->field})) {
                    $content = $data->{$row->field};
                }
                if ($row->field == 'password') {
                    $content = $data->{$row->field};
                }
            }

            if ($row->type == 'select_multiple' && property_exists($options, 'relationship')) {
                // Only if select_multiple is working with a relationship
                $multi_select[] = ['row' => $row->field, 'content' => $content];
            } else {
                $data->{$row->field} = $content;
            }
        }

        $this->validate($request, $rules, $messages);

        $data->save();

        // Save translations
        if (count($translations) > 0) {
            $data->saveTranslations($translations);
        }

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
                    $request->file($row->field)->storeAs(
                        $path,
                        $filename.'.'.$file->getClientOriginalExtension(),
                        config('voyager.storage.disk', 'public')
                    );

                    return $fullPath;
                }
            // no break

            /********** MULTIPLE IMAGES TYPE **********/
            case 'multiple_images':
                if ($files = $request->file($row->field)) {
                    /**
                     * upload files.
                     */
                    $filesPath = [];
                    foreach ($files as $key => $file) {
                        $filename = Str::random(20);
                        $path = $slug.'/'.date('F').date('Y').'/';
                        array_push($filesPath, $path.$filename.'.'.$file->getClientOriginalExtension());
                        $filePath = $path.$filename.'.'.$file->getClientOriginalExtension();
                        $request->file($row->field)[$key]->storeAs(
                            $path,
                            $filename.'.'.$file->getClientOriginalExtension(),
                            config('voyager.storage.disk', 'public')
                        );
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

                    Storage::disk(config('voyager.storage.disk'))->put($fullPath, (string) $image, 'public');

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

                            Storage::disk(config('voyager.storage.disk'))->put($path.$filename.'-'.$thumbnails->name.'.'.$file->getClientOriginalExtension(),
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
                    if (empty($request->input($row->field))) {
                        $content = null;
                    } else {
                        $content = gmdate('Y-m-d H:i:s', strtotime($request->input($row->field)));
                    }
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
        if (Storage::disk(config('voyager.storage.disk'))->exists($path)) {
            Storage::disk(config('voyager.storage.disk'))->delete($path);
        }
    }
}
