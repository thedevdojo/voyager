<?php

namespace TCG\Voyager;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use TCG\Voyager\Classes\Bread as BreadClass;

class Voyager
{
    protected $breads;
    protected $breadPath;
    protected $formfields;
    protected $messages = [];

    /**
     * Get Voyagers reoutes.
     *
     * @return array an array of routes
     */
    public function routes()
    {
        require __DIR__.'/../routes/voyager.php';
    }

    /**
     * Generate an absolute URL for an asset-file.
     *
     * @param string $path the relative path, e.g. js/voyager.js
     *
     * @return string
     */
    public function assetUrl($path)
    {
        return route('voyager.voyager_assets').'?path='.urlencode($path);
    }

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
            // TODO: Cache BREADs
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
        if (!$this->breads) {
            $this->getBreads();
        }

        return $this->breads->where('table', $table)->first();
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
        if (!$this->breads) {
            $this->getBreads();
        }

        return $this->breads->where('slug', $slug)->first();
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
        return File::put(Str::finish($this->breadPath, '/').$bread->table.'.json', json_encode($bread, JSON_PRETTY_PRINT));
    }

    /**
     * Create a BREAD-file.
     *
     * @param string $table
     *
     * @return int|bool success
     */
    public function createBread($table)
    {
        $bread = [
            'table'         => $table,
            'slug'          => (object) [],
            'name_singular' => (object) [],
            'name_plural'   => (object) [],
            'layouts'       => (object) [],

        ];

        // TODO: Validate if BREAD already exists and throw exception?

        return $this->storeBread((object) $bread);
    }

    /**
     * Flash a message to the UI.
     *
     * @param string $message The message
     * @param string $type    The type of the exception: info, warning, error or debug
     */
    public function flashMessage($message, $type)
    {
        $this->messages = [
            'message' => $message,
            'type'    => $type,
        ];
    }

    /**
     * Get all messages.
     *
     * @return array The messages
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
