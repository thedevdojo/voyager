<?php

namespace TCG\Voyager\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use TCG\Voyager\Models\DataType;

trait BreadRelationshipParser
{
    protected $relation_field = [];

    /**
     * Remove Relationship Field
     *
     * @param  DataType $dataType
     * @param  string   $bread_type BREAD type. Default 'browse'
     */
    protected function removeRelationshipField(DataType $dataType, $bread_type = 'browse')
    {
        $_keys = [];  // Forget keys
        foreach ($dataType->{$bread_type.'Rows'} as $key => $row) {
            if ($row->type == 'relationship') {
                $options   = json_decode($row->details);
                $relField  = @$options->column;      // Relationship Field
                $keyInColl = key($dataType->{$bread_type.'Rows'}->where('field', '=', $relField)->toArray());

                array_push($_keys, $keyInColl);
            }
        }

        foreach ($_keys as $forget_key) {
            $dataType->{$bread_type.'Rows'}->forget($forget_key);
        }
    }

    /**
     * Build the relationships array for the model's eager load.
     *
     * @param DataType $dataType
     *
     * @return array
     */
    protected function getRelationships(DataType $dataType)
    {
        $rs = [];   // Relationships

        $dataType->browseRows->each(function ($item) use (&$rs) {
            $details = json_decode($item->details);
            if (isset($details->relationship) && isset($item->field)) {
                $rel = $details->relationship;
                if (isset($rel->method)) {
                    $method = $rel->method;
                    $this->relation_field[$method] = $item->field;
                } else {
                    $method = camel_case($item->field);
                }

                $rs[$method] = function ($query) use ($rel) {
                    // select only what we need
                    if (isset($rel->method)) {
                        return $query;
                    } else {
                        $query->select($rel->key, $rel->label);
                    }
                };
            }
        });

        return $rs;
    }

    /**
     * Replace relationships' keys for labels and create READ links if a slug is provided.
     *
     * @param  $dataTypeContent  Can be either an eloquent Model, Collection or LengthAwarePaginator instance.
     * @param  DataType $dataType
     *
     * @return $dataTypeContent
     */
    protected function resolveRelations($dataTypeContent, DataType $dataType)
    {
        // In case of using server-side pagination, we need to work on the Collection (BROWSE)
        if ($dataTypeContent instanceof LengthAwarePaginator) {
            $dataTypeCollection = $dataTypeContent->getCollection();
        }
        // If it's a model just make the changes directly on it (READ / EDIT)
        elseif ($dataTypeContent instanceof Model) {
            return $this->relationToLink($dataTypeContent, $dataType);
        }
        // Or we assume it's a Collection
        else {
            $dataTypeCollection = $dataTypeContent;
        }

        $dataTypeCollection->transform(function ($item) use ($dataType) {
            return $this->relationToLink($item, $dataType);
        });

        return $dataTypeContent instanceof LengthAwarePaginator
                                ? $dataTypeContent->setCollection($dataTypeCollection)
                                : $dataTypeCollection;
    }

    /**
     * Create the URL for relationship's anchors in BROWSE and READ views.
     *
     * @param Model    $item     Object to modify
     * @param DataType $dataType
     *
     * @return Model $item
     */
    protected function relationToLink(Model $item, DataType $dataType)
    {
        $rs = $item->getRelations();

        if (!empty($rs) && array_filter($rs)) {
            foreach ($rs as $field => $relation) {
                if (isset($this->relation_field[$field])) {
                    $field = $this->relation_field[$field];
                } else {
                    $field = snake_case($field);
                }

                $bread_data = $dataType->browseRows->where('field', $field)->first();
                $relData = json_decode($bread_data->details)->relationship;

                if ($bread_data->type == 'select_multiple') {
                    $relItems = [];
                    foreach ($relation as $model) {
                        $relItem = new \stdClass();
                        $relItem->{$field} = $model[$relData->label];
                        if (isset($relData->page_slug)) {
                            $id = $model->id;
                            $relItem->{$field.'_page_slug'} = url($relData->page_slug, $id);
                        }
                        $relItems[] = $relItem;
                    }
                    $item[$field] = $relItems;
                    continue; // Go to the next relation
                }

                if (!is_object($item[$field])) {
                    $item[$field] = $relation[$relData->label];
                } else {
                    $tmp = $item[$field];
                    $item[$field] = $tmp;
                }
                if (isset($relData->page_slug) && $relation) {
                    $id = $relation->id;
                    $item[$field.'_page_slug'] = url($relData->page_slug, $id);
                }
            }
        }

        return $item;
    }
}
