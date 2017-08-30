<?php

namespace TCG\Voyager\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use TCG\Voyager\Models\DataType;

trait BreadRelationshipParser
{
    protected $relation_field = [];

    protected function removeRelationshipField(DataType $dataType, $bread_type = 'browse')
    {
        $forget_keys = [];
        foreach ($dataType->{$bread_type.'Rows'} as $key => $row) {
            if ($row->type == 'relationship') {
                $options = json_decode($row->details);
                $relationshipField = @$options->column;
                $keyInCollection = key($dataType->{$bread_type.'Rows'}->where('field', '=', $relationshipField)->toArray());
                array_push($forget_keys, $keyInCollection);
            }
        }

        foreach ($forget_keys as $forget_key) {
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
        $relationships = [];

        $dataType->browseRows->each(function ($item) use (&$relationships) {
            $details = json_decode($item->details);
            if (isset($details->relationship) && isset($item->field)) {
                $relation = $details->relationship;
                if (isset($relation->method)) {
                    $method = $relation->method;
                    $this->relation_field[$method] = $item->field;
                } else {
                    $method = camel_case($item->field);
                }

                $relationships[$method] = function ($query) use ($relation) {
                    // select only what we need
                    if (isset($relation->method)) {
                        return $query;
                    } else {
                        $query->select($relation->key, $relation->label);
                    }
                };
            }
        });

        return $relationships;
    }

    /**
     * Replace relationships' keys for labels and create READ links if a slug is provided.
     *
     * @param  $dataTypeContent     Can be either an eloquent Model, Collection or LengthAwarePaginator instance.
     * @param DataType $dataType
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

        return $dataTypeContent instanceof LengthAwarePaginator ? $dataTypeContent->setCollection($dataTypeCollection) : $dataTypeCollection;
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
        $relations = $item->getRelations();

        if (!empty($relations) && array_filter($relations)) {
            foreach ($relations as $field => $relation) {
                if (isset($this->relation_field[$field])) {
                    $field = $this->relation_field[$field];
                } else {
                    $field = snake_case($field);
                }

                $bread_data = $dataType->browseRows->where('field', $field)->first();
                $relationData = json_decode($bread_data->details)->relationship;

                if ($bread_data->type == 'select_multiple') {
                    $relationItems = [];
                    foreach ($relation as $model) {
                        $relationItem = new \stdClass();
                        $relationItem->{$field} = $model[$relationData->label];
                        if (isset($relationData->page_slug)) {
                            $id = $model->id;
                            $relationItem->{$field.'_page_slug'} = url($relationData->page_slug, $id);
                        }
                        $relationItems[] = $relationItem;
                    }
                    $item[$field] = $relationItems;
                    continue; // Go to the next relation
                }

                if (!is_object($item[$field])) {
                    $item[$field] = $relation[$relationData->label];
                } else {
                    $tmp = $item[$field];
                    $item[$field] = $tmp;
                }
                if (isset($relationData->page_slug) && $relation) {
                    $id = $relation->id;
                    $item[$field.'_page_slug'] = url($relationData->page_slug, $id);
                }
            }
        }

        return $item;
    }
}
