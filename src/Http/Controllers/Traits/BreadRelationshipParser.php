<?php

namespace TCG\Voyager\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use TCG\Voyager\Models\DataType;

trait BreadRelationshipParser
{
    protected function removeRelationshipField(DataType $dataType, $bread_type = 'browse')
    {
        $forget_keys = [];
        foreach ($dataType->{$bread_type.'Rows'} as $key => $row) {
            if ($row->type == 'relationship') {
                if ($row->details->type == 'belongsTo') {
                    $relationshipField = @$row->details->column;
                    $keyInCollection = key($dataType->{$bread_type.'Rows'}->where('field', '=', $relationshipField)->toArray());
                    array_push($forget_keys, $keyInCollection);
                }
            }
        }

        foreach ($forget_keys as $forget_key) {
            $dataType->{$bread_type.'Rows'}->forget($forget_key);
        }

        // Reindex collection
        $dataType->{$bread_type.'Rows'} = $dataType->{$bread_type.'Rows'}->values();
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
            return $dataTypeContent;
        }
        // Or we assume it's a Collection
        else {
            $dataTypeCollection = $dataTypeContent;
        }

        return $dataTypeContent instanceof LengthAwarePaginator ? $dataTypeContent->setCollection($dataTypeCollection) : $dataTypeCollection;
    }

    /**
     * Eagerload relationships.
     *
     * @param mixed    $dataTypeContent     Can be either an eloquent Model or Collection.
     * @param DataType $dataType
     * @param string   $action
     * @param bool     $isModelTranslatable
     *
     * @return void
     */
    protected function eagerLoadRelations($dataTypeContent, DataType $dataType, string $action, bool $isModelTranslatable)
    {
        // Eagerload Translations
        if (config('voyager.multilingual.enabled')) {
            // Check if BREAD is Translatable
            if ($isModelTranslatable) {
                $dataTypeContent->load('translations');
            }

            // DataRow is translatable so it will always try to load translations
            // even if current Model is not translatable
            $dataType->{$action.'Rows'}->load('translations');
        }
    }
}
