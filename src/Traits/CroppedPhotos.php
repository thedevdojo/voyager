<?php

namespace TCG\Voyager\Traits;

use File;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait CroppedPhotos
{
    private $filesystem;
    private $cropFolder;
    private $quality;
    private $request;
    private $dataType;
    private $data;

    /**
     * Save cropped photos.
     *
     * @param Illuminate\Http\Request                 $request
     * @param string                                  $slug
     * @param Illuminate\Database\Eloquent\Collection $dataType
     * @param Illuminate\Database\Eloquent\Model      $data
     *
     * @return bool
     */
    public function cropPhotos(Request $request, $slug, Collection $dataType, Model $data)
    {
        $this->filesystem = config('voyager.storage.disk');
        $this->cropFolder = config('voyager.images.crop_folder').'/'.$slug;
        $this->quality = config('voyager.images.quality', 100);
        $this->request = $request;
        $this->dataType = $dataType;
        $this->data = $data;

        foreach ($this->getPhotosWithDetails() as $dataRow) {
            $details = json_decode($dataRow->details);

            if (!isset($details->crop)) {
                return false;
            }
            if (!$request->{$dataRow->field}) {
                return false;
            }

            $this->cropPhoto($details->crop, $dataRow);
        }

        return true;
    }

    /**
     * Crop photo by coordinates.
     *
     * @param array                                   $crop
     * @param Illuminate\Database\Eloquent\Collection $dataRow
     *
     * @return void
     */
    public function cropPhoto($crop, $dataRow)
    {
        $cropFolder = $this->cropFolder;

        //If a folder is not exists, then make the folder
        if (!Storage::disk($this->filesystem)->exists($cropFolder)) {
            Storage::disk($this->filesystem)->makeDirectory($cropFolder);
        }

        $itemId = $this->data->id;

        foreach ($crop as $cropParam) {
            $inputName = $dataRow->field.'_'.$cropParam->name;
            $params = json_decode($this->request->get($inputName));

            if (!is_object($params)) {
                return false;
            }

            $path = $this->request->{$dataRow->field};
            $img = Image::make(Storage::disk($this->filesystem)->path($path));

            $img->crop(
                (int) $params->w,
                (int) $params->h,
                (int) $params->x,
                (int) $params->y
            );
            $img->resize($cropParam->size->width, $cropParam->size->height);

            $photoName = $cropFolder.'/'.$cropParam->name.'_'.$itemId.'_'.$cropParam->size->name.'.jpg';
            $img->save(Storage::disk($this->filesystem)->path($photoName), $this->quality);

            if (!empty($cropParam->resize)) {
                foreach ($cropParam->resize as $cropParamResize) {
                    $img->resize($cropParamResize->width, $cropParamResize->height, function ($constraint) {
                        $constraint->aspectRatio();
                    });

                    $photoName = $cropFolder.'/'.$cropParam->name.'_'.$itemId.'_'.$cropParam->name.'.jpg';
                    $img->save(Storage::disk($this->filesystem)->path($photoName), $this->quality);
                }
            }
        }
    }

    /**
     * Get the photos with details.
     *
     * @return Illuminate\Database\Eloquent\Collection $dataType
     */
    public function getPhotosWithDetails()
    {
        return $this->dataType
            ->where('type', '=', 'image')
            ->where('details', '!=', null);
    }

    /**
     * Get the cropped photo url.
     *
     * @param string $prefix
     * @param string $suffix
     *
     * @return string
     */
    public function getCroppedPhoto($prefix, $suffix)
    {
        $photoName = config('voyager.images.crop_folder')
            .'/'.str_replace('_', '-', $this->getTable())
            .'/'.$prefix.'_'.$this->id.'_'.$suffix.'.jpg';

        return Storage::disk($this->filesystem)->url($photoName);
    }
}
