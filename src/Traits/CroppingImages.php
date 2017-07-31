<?php

namespace TCG\Voyager\Traits;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

trait CroppingImages
{
    private $quality;
    private $request;
    private $slug;    
    private $dataType;
    private $data;

    /**
     * Save cropped images
     *
     * @param Illuminate\Http\Request $request
     * @param string $slug
     * @param Illuminate\Database\Eloquent\Collection $dataType
     * @param Illuminate\Database\Eloquent\Model $data
     * @return boolean
     */
    public static function save(Request $request, $slug, Collection $dataType, Model $data)
    {
        $this->quality = config('voyager.images.quality', 100);
        $this->request = $request;
        $this->slug = $slug;
        $this->dataType = $dataType;
        $this->data = $data;

        foreach ($this->getImagesWithDetails() as $dataRow) {
            $details = json_decode($dataRow->details);

            if (!isset($details->crop)) return false;
            if (!$this->request->{$dataRow->field}) return false;

            $this->crop($details->crop, $dataRow);
        }

        return true;
    }

    /**
     * Crop images by coordinates
     *
     * @param array $crop
     * @param Illuminate\Database\Eloquent\Collection $dataRow
     * @return void
     */
    public function crop($crop, $dataRow) {
        dd($dataRow);
        $request = $this->request;
        $cropfilesystem = config('setting.crop_storage.disk');
        $directory = $this->slug;

        //If a directory is not exists, then make the directory
        if (!Storage::disk($cropfilesystem)->exists($directory)) {
            Storage::disk($cropfilesystem)->makeDirectory($directory);
        }

        $item_id = $this->data->id;

        foreach ($crop as $cropParam) {
            $inputName = $dataRow->field . '_' . $cropParam->name;
            $params = json_decode($request->get($inputName));
            $img = Image::make(public_path('storage/' . $request->image));

            $img->crop(
                (int) $params->w,
                (int) $params->h,
                (int) $params->x,
                (int) $params->y
            );

            $img->resize($cropParam->size->width, $cropParam->size->height);
            $photo_name = "crop/{$directory}/{$cropParam->name}_{$item_id}_{$cropParam->size->name}.jpg";
            $img->save($photo_name, $this->quality);

            if (!empty($cropParam->resize)) {
                foreach ($cropParam->resize as $cropParamResize) {
                    $photo_name = "crop/{$directory}/{$cropParam->name}_{$item_id}_{$cropParamResize->name}.jpg";
                    $img->resize($cropParamResize->width, $cropParamResize->height, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $img->save($photo_name, $this->quality);
                }
            }
        }
    }

    /**
     * Get the images with details
     *
     * @return Illuminate\Database\Eloquent\Collection $dataType
     */
    public function getImagesWithDetails()
    {
        return $this->dataType
            ->where('type', '=', 'image')
            ->where('details', '!=', null);
    }
}
