<?php

namespace Volosyuk\SimpleEloquent\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Volosyuk\SimpleEloquent\Builder;
use Volosyuk\SimpleEloquent\ModelAccessor;

/**
 * Class MorphToWithSimple
 * @package Volosyuk\SimpleEloquent
 */
class MorphTo extends \Illuminate\Database\Eloquent\Relations\MorphTo
{
    use Relation;

    /**
     * Get the results of the relationship.
     *
     * Called via eager load method of Eloquent query builder.
     *
     * @return mixed
     */
    protected function getEagerSimple()
    {
        foreach (array_keys($this->dictionary) as $type) {
            $this->matchSimpleToMorphParents($type, $this->getSimpleResultsByType($type));
        }

        return $this->models;
    }

    /**
     * @param $models
     * @param $name
     * @return mixed
     */
    public function eagerLoadAndMatchSimple($models, $name)
    {
        $this->addEagerConstraintsSimple($models);

        return $this->getEagerSimple();
    }

    /**
     * Get all of the relation results for a type.
     *
     * @param  string  $type
     * @return Collection
     */
    protected function getSimpleResultsByType($type)
    {
        /**
         * @var Model $instance
         */
        $instance = $this->createModelByType($type);

        /**
         * @var Builder $query
         */
        $query = $this->replayMacros($instance->newQuery())
            ->mergeConstraintsFrom($this->getQuery())
            ->with($this->getQuery()->getEagerLoads());

        return $query->whereIn(
            $instance->getTable().'.'.$instance->getKeyName(), $this->gatherKeysByType($type)
        )->getSimple();
    }

    /**
     * Match the results for a given type to their parents.
     *
     * @param  string  $type
     * @param  Collection  $results
     * @return void
     */
    protected function matchSimpleToMorphParents($type, Collection $results)
    {
        foreach ($results as $result) {
            foreach ($this->models as &$model) {
                if (
                    ModelAccessor::get($model, $this->morphType) == $type
                    &&
                    ModelAccessor::get($model, $this->foreignKey) == ModelAccessor::get($result, $this->parent->getKeyName())
                ) {
                    ModelAccessor::set($model, $this->relation, $result);
                }
            }
            unset($model);
        }
    }


    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param  array  $models
     * @return void
     */
    public function addEagerConstraintsSimple(array $models)
    {
        $this->buildDictionarySimple($this->models = $models);
    }


    /**
     * Build a dictionary with the models.
     *
     * @param  array  $models
     * @return void
     */
    protected function buildDictionarySimple(array $models)
    {
        foreach ($models as $model) {
            if (ModelAccessor::get($model, $this->morphType)) {
                $this->dictionary[ModelAccessor::get($model, $this->morphType)][ModelAccessor::get($model, $this->foreignKey)][] = $model;
            }
        }
    }

    /**
     * Gather all of the foreign keys for a given type.
     *
     * @param  string  $type
     * @return array
     */
    protected function gatherSimpleKeysByType($type)
    {
        $foreign = $this->foreignKey;

        return collect($this->dictionary[$type])->map(function ($models) use ($foreign) {
            return ModelAccessor::get(head($models), $foreign);
        })->values()->unique();
    }
}
