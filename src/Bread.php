<?php

namespace TCG\Voyager;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use TCG\Voyager\Classes\Bread as BreadClass;

class Bread
{
    protected $breadPath;
    protected $breads = null;

    /**
     * Sets the path where the BREAD-files are stored.
     *
     * @param string $path
     *
     * @return string the current pat
     */
    public function breadPath($path = null)
    {
        if ($path) {
            $this->breadPath = Str::finish($path, '/');
        }

        return $this->breadPath;
    }

    /**
     * Get all BREADs from storage and validate.
     *
     * @return \TCG\Voyager\Classes\Bread
     */
    public function getBreads()
    {
        if (!$this->breads) {
            if (!File::isDirectory($this->breadPath)) {
                File::makeDirectory($this->breadPath);
            }
    
            $this->breads = collect(File::files($this->breadPath))->transform(function ($bread) {
                return new BreadClass($bread->getPathName());
            })->filter(function ($bread) {
                if (!$bread->parse_failed && !$bread->isValid()) {
                    $this->flashMessage('BREAD "'.$bread->slug.'" is not valid!', 'debug');
                }
    
                return $bread->isValid();
            });
        }

        return $this->breads;
    }

    /**
     * Get a BREAD by the table name.
     *
     * @param string $table
     *
     * @return \TCG\Voyager\Classes\Bread
     */
    public function getBread($table)
    {
        return $this->getBreads()->where('table', $table)->first();
    }

    /**
     * Get a BREAD by the slug.
     *
     * @param string $slug
     *
     * @return \TCG\Voyager\Classes\Bread
     */
    public function getBreadBySlug($slug)
    {
        return $this->getBreads()->filter(function ($bread) use ($slug) {
            return $bread->slug == $slug;
        })->first();
    }

    /**
     * Store a BREAD-file.
     *
     * @param string $bread
     *
     * @return int|bool success
     */
    public function storeBread($bread)
    {
        $this->clearBreads();

        return File::put(Str::finish($this->breadPath, '/').$bread->table.'.json', json_encode($bread, JSON_PRETTY_PRINT));
    }

    /**
     * Create a BREAD-object.
     *
     * @param string $table
     *
     * @return int|bool success
     */
    public function createBread($table)
    {
        $bread = [
            'table'         => $table,
            'slug'          => Str::slug($table),
            'name_singular' => Str::singular(Str::title($table)),
            'name_plural'   => Str::plural(Str::title($table)),
            'layouts'       => (object) [],
        ];

        return new BreadClass(null, $bread);
    }

    /**
     * Clears all BREAD-objects.
     */
    public function clearBreads()
    {
        $this->breads = null;
    }

    /**
     * Delete a BREAD from the filesystem.
     *
     * @param string $table The table of the BREAD
     */
    public function deleteBread($table)
    {
        $ret = File::delete(Str::finish($this->breadPath, '/').$table.'.json');
        $this->clearBreads();

        return $ret;
    }
}
