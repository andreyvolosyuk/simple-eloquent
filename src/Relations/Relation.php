<?php

namespace Volosyuk\SimpleEloquent\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Volosyuk\SimpleEloquent\Builder;
use Volosyuk\SimpleEloquent\ModelAccessor;

/**
 * Trait SimpleRelation
 * @package Volosyuk\SimpleEloquent
 *
 * @property Builder $query
 * @property Model $parent
 */
trait Relation
{
    /**
     * @param $models
     * @param $name
     * @return array
     */
    public function eagerLoadAndMatchSimple($models, $name)
    {
        $results = $this->getEagerSimple();

        return $this->matchSimple($models, $results, $name);
    }

    /**
     * @param array $models
     * @param $relation
     * @return array
     */
    public function initSimpleRelation(array &$models, $relation)
    {
        foreach ($models as &$model) {
            ModelAccessor::set($model, $relation, null);
        }
        unset($model);

        return $models;
    }

    /**
     * Get the relationship for eager loading.
     *
     * @return Collection
     */
    protected function getEagerSimple()
    {
        return $this->getSimple();
    }

    /**
     * Get all of the primary keys for an array of models.
     *
     * @param  array   $models
     * @param  string  $key
     * @return array
     */
    protected function getKeys(array $models, $key = null)
    {
        return array_unique(array_values(array_map(function ($value) use ($key) {
            return $key ? ModelAccessor::get($value, $key) : ModelAccessor::get($value, $this->parent->getKeyName());
        }, $models)));
    }

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param  array  $models
     * @return void
     */
    public function addEagerConstraintsSimple(array $models)
    {
        $this->query->whereIn($this->getForeignKey(), $this->getKeys($models));
    }
}
