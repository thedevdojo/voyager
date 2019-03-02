<?php

namespace TCG\Voyager\Http\Controllers\ContentTypes;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Constraint;
use Intervention\Image\Facades\Image as InterventionImage;

class MultipleImage extends BaseType
{
    /**
     * @return string
     */
    public function handle()
    {
        $filesPath = [];
        $files = $this->request->file($this->row->field);

        if (!$files) {
            return;
        }

        foreach ($files as $file) {
            if (!$file->isValid()) {
                continue;
            }

            $image = InterventionImage::make($file);
            $fullPath = $path.$filename.'.'.$file->getClientOriginalExtension();

            $resize_width = null;
            $resize_height = null;
            $resize_quality = intval($this->options->quality ?? 75);

            if (isset($this->options->fit) && isset($this->options->fit->width)) {
                $resize_width = $this->options->fit->width;

                if (isset($this->options->fit->height)) {
                    $resize_height = $this->options->fit->height;
                } else {
                    $resize_height = $this->options->fit->width;
                }

                $image = $image->fit(
                    $resize_width,
                    $resize_height,
                    function (Constraint $constraint) {
                        if (isset($this->options->upsize) && !$this->options->upsize) {
                            $constraint->upsize();
                        }
                    }
                )->encode($file->getClientOriginalExtension(), $resize_quality);

            } else {
                if (isset($this->options->resize) && (
                    isset($this->options->resize->width) || isset($this->options->resize->height)
                )) {
                    if (isset($this->options->resize->width)) {
                        $resize_width = $this->options->resize->width;
                    }
                    if (isset($this->options->resize->height)) {
                        $resize_height = $this->options->resize->height;
                    }
                } else {
                    $resize_width = $image->width();
                    $resize_height = $image->height();
                }

                $image = $image->resize(
                    $resize_width,
                    $resize_height,
                    function (Constraint $constraint) {
                        $constraint->aspectRatio();
                        if (isset($this->options->upsize) && !$this->options->upsize) {
                            $constraint->upsize();
                        }
                    }
                )->encode($file->getClientOriginalExtension(), $resize_quality);
            }

            if ($this->is_animated_gif($file)) {
                Storage::disk(config('voyager.storage.disk'))->put($fullPath, file_get_contents($file), 'public');
                $fullPathStatic = $path.$filename.'-static.'.$file->getClientOriginalExtension();
                Storage::disk(config('voyager.storage.disk'))->put($fullPathStatic, (string) $image, 'public');
            } else {
                Storage::disk(config('voyager.storage.disk'))->put($fullPath, (string) $image, 'public');
            }

            if (isset($this->options->thumbnails)) {
                foreach ($this->options->thumbnails as $thumbnails) {
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

                        $image = InterventionImage::make($file)->resize(
                            $thumb_resize_width,
                            $thumb_resize_height,
                            function (Constraint $constraint) {
                                $constraint->aspectRatio();
                                if (isset($this->options->upsize) && !$this->options->upsize) {
                                    $constraint->upsize();
                                }
                            }
                        )->encode($file->getClientOriginalExtension(), $resize_quality);
                    } elseif (isset($thumbnails->crop->width) && isset($thumbnails->crop->height)) {
                        $crop_width = $thumbnails->crop->width;
                        $crop_height = $thumbnails->crop->height;
                        $image = InterventionImage::make($file)
                            ->fit($crop_width, $crop_height)
                            ->encode($file->getClientOriginalExtension(), $resize_quality);
                    }

                    Storage::disk(config('voyager.storage.disk'))->put(
                        $path.$filename.'-'.$thumbnails->name.'.'.$file->getClientOriginalExtension(),
                        (string) $image,
                        'public'
                    );
                }
            }

            array_push($filesPath, $fullPath);
        }

        return json_encode($filesPath);
    }
}
