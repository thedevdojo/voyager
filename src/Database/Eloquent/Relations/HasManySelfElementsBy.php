<?php

namespace TCG\Voyager\Database\Eloquent\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HasManySelfElementsBy extends HasMany
{
    protected $foreignIdentifier;
    protected $localIdentifier;

    /**
     * Create a new has one or many relationship instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @param  string  $foreignIdentifier
     * @param  string  $localIdentifier
     */
    public function __construct(Builder $query, Model $parent, $foreignKey, $localKey, $foreignIdentifier, $localIdentifier)
    {
        $this->foreignIdentifier = $foreignIdentifier;
        $this->localIdentifier = $localIdentifier;

        parent::__construct($query, $parent, $foreignKey, $localKey);
    }

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  array   $models
     * @param  \Illuminate\Database\Eloquent\Collection  $results
     * @param  string  $relation
     * @return array
     */
    public function match(array $models, Collection $results, $relation)
    {
        foreach ($models as $model) {
            $identifier = $model->getAttribute($this->localIdentifier);
            $matches = $results->where($this->foreignIdentifier, $identifier);

            $matches = $this->match($matches->all(), new Collection($results), $relation);

            $model->setRelation($relation, $matches);
        }

        return $models;
    }
}