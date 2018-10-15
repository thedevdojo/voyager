<?php

namespace TCG\Voyager\Traits;

use TCG\Voyager\Models\DataRow;
use TCG\Voyager\Models\DataType;

/**
 * Trait WithRelationships
 * @package TCG\Voyager\Traits
 */
trait WithRelationships
{
    /**
     * @var array Relationships
     */
    protected $relationships = [];

    /**
     * Sets relationships in the model from BREAD configuration
     *
     * @usage
     *
     * $model = Model::find([ID])->resolveRelations();
     * $model->[RELATION_NAME]();
     *
     *  Alternatively you could set this in your model $dispatchesEvents as such:
     *
     *     protected $dispatchesEvents = [
     *          'retrieved' => ModelRetrievedEvent::class
     *     ];
     *
     *  And in that event class add the following:
     *
     *  public function __construct($model) {
     *       $model->resolveRelations();
     *      }
     *
     * @return void
     */
    public function withRelations()
    {
        // Get DataType for model table
        $pagesDataType = DataType::where('name', '!=', $this->getTable())->first();

        if ($pagesDataType) {
            // Get DATA ROW for type and relationship
            $relationships = DataRow::where([
                ['data_type_id', '=', $pagesDataType->id],
                ['type', '=', 'relationship']
            ])->get();

            if (count($relationships)){
                // Iterate through relationships and match per type
                foreach($relationships as $relationship) {
                    $config = json_decode($relationship->details);

                    // Reference: relationship.blade.php
                    // Relationship: hasOne
                    if ($config->type == 'hasOne'){
                        $this->relationships[strtolower($relationship->display_name)] = $this->hasOne($config->model, $config->column);
                    }
                    // Relationship: hasMany
                    else if ($config->type == 'hasMany'){
                        $this->relationships[strtolower($relationship->display_name)] = $this->hasMany($config->model, $config->column);
                    }

                    // Relationship: belongsTo
                    else if ($config->type == 'belongsTo' && $config->pivot == 0){
                        $this->relationships[strtolower($relationship->display_name)] = $this->belongsTo($config->model, $config->column);

                        // Relationship: belongsToMany
                    } else if ($config->type == 'belongsToMany' && $config->pivot == 1 && !empty($config->pivot_table)) {
                        $this->relationships[strtolower($relationship->display_name)] = $this->belongsToMany($config->model, $config->pivot_table);
                    }
                }
            }
        }

        return $this;
    }


    /**
     * If a relationship is being called, return it from the relationship array
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (isset($this->relationships[$method])){
            return $this->relationships[$method];
        }

        // No relation found, parent handles it.
        return parent::__call($method, $parameters);
    }

    /**
     * Determine if the relationship exists and if so, return it as an attribute
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (isset($this->relationships[$key])){
            $this->setAttribute($key, $this->relationships[$key]);
            return $this->relationships[$key]->get();
        }

        // No relationship attribute found, parent handles it.
        return parent::__get($key);
    }
}
