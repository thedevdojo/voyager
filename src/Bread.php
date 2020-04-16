<?php

namespace TCG\Voyager;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use TCG\Voyager\Classes\Bread as BreadClass;
use TCG\Voyager\Facades\Voyager as VoyagerFacade;

class Bread
{
    protected $formfields;
    protected $breadPath;
    protected $breads = null;

    /**
     * Sets the path where the BREAD-files are stored.
     *
     * @param string $path
     *
     * @return string the current path
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
                File::makeDirectory($this->breadPath, 0755, true);
            }

            $this->breads = collect(File::files($this->breadPath))->transform(function ($bread) {
                // Exclude backups
                if (Str::endsWith($bread->getPathName(), '.backup.json')) {
                    return null;
                }
                $content = File::get($bread->getPathName());
                $json = @json_decode($content);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    VoyagerFacade::flashMessage('BREAD-file "'.basename($bread->getPathName()).'" does contain invalid JSON: '.json_last_error_msg(), 'debug');
                    return;
                }

                return new BreadClass($json);
            })->filter(function ($bread) {
                return $bread !== null;
            })->values();
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
            'layouts'       => [],
        ];

        return new BreadClass($bread);
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

    /**
     * Backup a BREAD (copy table.json to table.backup.json).
     *
     * @param string $table The table of the BREAD
     */
    public function backupBread($table)
    {
        $old = $this->breadPath.$table.'.json';
        $new = $this->breadPath.$table.'.backup.json';

        if (File::exists($old)) {
            return File::copy($old, $new);
        }

        return true;
    }

    /**
     * Get the search placeholder (Search for Users, Posts, etc...)
     *
     * @param string $placeholder The placeholder
     */
    public function getBreadSearchPlaceholder()
    {
        $breads =  $this->getBreads();

        if ($breads->count() > 1) {
            return __('voyager::generic.search_for_breads', [
                'bread' => $breads[rand(0, (count($breads) - 1))]->name_plural,
                'bread2' => $breads[rand(0, (count($breads) - 1))]->name_plural
            ]);
        } elseif ($breads->count() == 1) {
            return __('voyager::generic.search_for_bread', [
                'bread' => $breads[0]->name_plural
            ]);
        }

        return __('voyager::generic.search');
    }

    /**
     * Add a formfield.
     *
     * @param string $class The class of the formfield
     */
    public function addFormfield($class)
    {
        if (!$this->formfields) {
            $this->formfields = collect();
        }
        $class = new $class();
        $this->formfields->push($class);
    }

    /**
     * Get formfields.
     *
     * @return Illuminate\Support\Collection The formfields
     */
    public function getFormfields()
    {
        return $this->formfields;
    }

    /**
     * Get a formfield by type.
     *
     * @param string $type The type of the formfield
     *
     * @return object The formfield
     */
    public function getFormfield(string $type)
    {
        return $this->formfields->filter(function ($formfield) use ($type) {
            return $formfield->type == $type;
        })->first();
    }

    /**
     * Get the reflection class for a model.
     *
     * @param string $model The fully qualified model name
     *
     * @return ReflectionClass The reflection object
     */
    public function getModelReflectionClass(string $model): \ReflectionClass
    {
        return new \ReflectionClass($model);
    }

    public function getModelScopes(\ReflectionClass $reflection): Collection
    {
        return collect($reflection->getMethods())->filter(function ($method) {
            return Str::startsWith($method->name, 'scope');
        })->whereNotIn('name', ['scopeWithTranslations', 'scopeWithTranslation', 'scopeWhereTranslation'])->transform(function ($method) {
            return lcfirst(Str::replaceFirst('scope', '', $method->name));
        });
    }

    public function getModelComputedProperties(\ReflectionClass $reflection): Collection
    {
        return collect($reflection->getMethods())->filter(function ($method) {
            return Str::startsWith($method->name, 'get') && Str::endsWith($method->name, 'Attribute');
        })->transform(function ($method) {
            $name = Str::replaceFirst('get', '', $method->name);
            $name = Str::replaceLast('Attribute', '', $name);

            return lcfirst($name);
        })->filter();
    }

    public function getModelRelationships(\ReflectionClass $reflection, Model $model, bool $resolve = false): Collection
    {
        $types = [
            BelongsTo::class,
            BelongsToMany::class,
            HasMany::class,
            HasOne::class,
        ];

        return collect($reflection->getMethods())->transform(function ($method) use ($types, $model, $resolve) {
            $type = $method->getReturnType();
            if ($type && in_array(strval($type->getName()), $types)) {
                $columns = [];
                $pivot = [];
                if ($resolve) {
                    $relationship = $model->{$method->getName()}();
                    $table = $relationship->getRelated()->getTable();
                    if ($type->getName() == BelongsToMany::class) {
                        // TODO: Might need to wrap this in array_values()
                        $pivot = array_diff(VoyagerFacade::getColumns($relationship->getTable()), [
                            $relationship->getForeignPivotKeyName(),
                            $relationship->getRelatedPivotKeyName(),
                        ]);
                    }

                    $columns = VoyagerFacade::getColumns($table);
                }

                return [
                    'method'  => $method->getName(),
                    'type'    => class_basename($type->getName()),
                    'columns' => $columns,
                    'pivot'   => $pivot,
                ];
            }
            
            return null;
        })->filter();
    }
}
