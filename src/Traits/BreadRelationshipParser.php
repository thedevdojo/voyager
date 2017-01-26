<?php

namespace TCG\Voyager\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use TCG\Voyager\Models\DataType;

trait BreadRelationshipParser
{
    /**
     * Build the relationships array for the model's eager load.
     * @param  DataType $dataType 
     * @return Array
     */
    protected function getRelationships(DataType $dataType)
    {
        $relationships = array();
        $dataType->browseRows->each(function ($item) use (&$relationships) {
            $details = json_decode( $item->details );
            if (isset($details->relationship) && isset($item->field)) {
                $relation = $details->relationship;
                $relationships[$item->field] = function ($query) use ($relation) {
                    // select only what we need
                    $query->select($relation->key, $relation->label);
                };
            }
        });

        return $relationships;
    }

    /**
     * Replace relationships' keys for labels and create READ links if a slug is provided.
     * @param  $dataTypeContent     Can be either an eloquent Model, Collection or LengthAwarePaginator instance.
     * @param  DataType $dataType
     * @return $dataTypeContent
     */
    protected function resolveRelations($dataTypeContent, DataType $dataType)
    {
        // In case of using server-side pagination, we need to work on the Collection
        if ($dataTypeContent instanceof LengthAwarePaginator) {
            $dataTypeCollection = $dataTypeContent->getCollection();
        }
        // If it's a model just make the changes directly on it
        else if ($dataTypeContent instanceof Model) {
            $relations = $dataTypeContent->getRelations();
            if (! empty($relations)) {
                foreach ($relations as $field => $value) {
                    $dataTypeContent = $this->relationToLink($dataTypeContent, $field, $value, $dataType);
                }
            }

            return $dataTypeContent;
        }
        // Or we assume it's a Collection
        else {
            $dataTypeCollection = $dataTypeContent;
        }

        $dataTypeCollection->transform(function ($item) use ($dataType) {
            $relations = $item->getRelations();
            if (! empty($relations)) {
                foreach ($relations as $field => $value) {
                    $item = $this->relationToLink($item, $field, $value, $dataType);
                }
            }

            return $item;
        });

        return $dataTypeContent instanceof LengthAwarePaginator ? $dataTypeContent->setCollection($dataTypeCollection) : $dataTypeCollection;
    }

    /**
     * Create the URL for relationship's anchors in BROWSE and READ views
     * @param  Model    $item       Object to modify
     * @param  String   $field      Relation field to modify (must match the relationship's method)
     * @param  Model    $relation   Relation model
     * @param  DataType $dataType   
     * @return Model    $item
     */
    protected function relationToLink(Model $item, $field, Model $relation, DataType $dataType)
    {
        $bread_data = $dataType->browseRows->where('field', $field)->first();
        $relationData = json_decode($bread_data->details)->relationship;
        $id = $item[$field];
        $item[$field] = $relation[$relationData->label];
        if (isset($relationData->page_slug)) {
            $item[$field . '_page_slug'] = url($relationData->page_slug, $id);
        }

        return $item;
    }
}
