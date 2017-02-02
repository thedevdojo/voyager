<?php

namespace TCG\Voyager\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use TCG\Voyager\Models\DataType;

trait BreadRelationshipParser
{
    /**
     * Build the relationships array for the model's eager load.
     * 
     * @param  DataType $dataType 
     * 
     * @return Array
     */
    protected function getRelationships(DataType $dataType)
    {
        $relationships = [];

        $dataType->browseRows->each(function ($item) use (&$relationships) {
            $details = json_decode($item->details);
            if (isset($details->relationship) && isset($item->field)) {
                $relation = $details->relationship;
                $relationships[camel_case($item->field)] = function ($query) use ($relation) {
                    // select only what we need
                    $query->select($relation->key, $relation->label);
                };
            }
        });

        return $relationships;
    }

    /**
     * Replace relationships' keys for labels and create READ links if a slug is provided.
     * 
     * @param  $dataTypeContent     Can be either an eloquent Model, Collection or LengthAwarePaginator instance.
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
        else if ($dataTypeContent instanceof Model) {

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
     * Create the URL for relationship's anchors in BROWSE and READ views
     * 
     * @param  Model    $item       Object to modify
     * @param  DataType $dataType  
     *  
     * @return Model    $item
     */
    protected function relationToLink(Model $item, DataType $dataType)
    {
        $relations = $item->getRelations();
        // If there are not-null relations
        if (! empty($relations) && array_filter($relations)) {
            foreach ($relations as $field => $relation) {
                $field = snake_case($field);
                $bread_data = $dataType->browseRows->where('field', $field)->first();
                $relationData = json_decode($bread_data->details)->relationship;
                $id = $item[$field];
                $item[$field] = $relation[$relationData->label];
                if (isset($relationData->page_slug)) {
                    $item[$field.'_page_slug'] = url($relationData->page_slug, $id);
                }

            }
        }

        return $item;
        
    }
}
