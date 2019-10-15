<?php

namespace TCG\Voyager\Traits;

use Illuminate\Support\Str;

trait Resizable
{
    /**
     * Method for returning specific thumbnail for model.
     *
     * @param string $type
     * @param string $attribute
     *
     * @return string
     */
    public function thumbnail($type, $attribute = 'image')
    {
        // Return empty string if the field not found
        if (!isset($this->attributes[$attribute])) {
            return '';
        }

        // We take image from posts field
        $image = $this->attributes[$attribute];

        return $this->getThumbnail($image, $type);
    }

    /**
     * Method for returning specific thumbnail of a given image format(extension) for model.
     *
     * @param string $type
     * @param string format
     * @param string $attribute
     *
     * @return string
     */
    public function thumbnailWithFormat($type, $format = null, $attribute = 'image')
    {
        // Return empty string if the field not found
        if (!isset($this->attributes[$attribute])) {
            return '';
        }

        // We take image from posts field
        $image = $this->attributes[$attribute];

        return $this->getThumbnail($image, $type, $format);
    }

    /**
     * Generate thumbnail URL.
     *
     * @param $image
     * @param $type
     *
     * @return string
     */
    public function getThumbnail($image, $type, $format = null)
    {
        // We need to get extension type ( .jpeg , .png ...)
        $default_ext = pathinfo($image, PATHINFO_EXTENSION);

        if (!isset($format)) {
            $format = $default_ext;
        }

        // We remove extension from file name so we can append thumbnail type
        $name = Str::replaceLast('.'.$default_ext, '', $image);

        // We merge original name + type + extension
        return $name.'-'.$type.'.'.$format;
    }
}
