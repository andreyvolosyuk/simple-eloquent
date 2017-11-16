<?php

namespace Volosyuk\SimpleEloquent\Relations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use stdClass;
use Volosyuk\SimpleEloquent\ModelAccessor;

/**
 * @package Volosyuk\SimpleEloquent
 *
 * @property Builder|\Volosyuk\SimpleEloquent\Builder $query
 * @property Model $related
 * @property Model $parent
 */
trait Pivot
{
    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  array   $models
     * @param  Collection  $results
     * @param  string  $relation
     * @return array
     */
    public function matchSimple(array &$models, Collection $results, $relation)
    {
        $foreign = $this->foreignKey;

        $dictionary = [];

        foreach ($results as $result) {
            $dictionaryKey = ModelAccessor::get(ModelAccessor::get($result, 'pivot'), $foreign);

            $dictionary[$dictionaryKey][] = $result;
        }

        foreach ($models as &$model) {
            if (isset($dictionary[$key = ModelAccessor::get($model, $this->parent->getKeyName())])) {
                $collection = Collection::make($dictionary[$key]);

                ModelAccessor::set($model, $relation, $collection);
            }
        }
        unset($model);

        return $models;
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param  array  $columns
     * @return Collection
     */
    public function getSimple($columns = ['*'])
    {
        $columns = $this->query->getQuery()->columns ? [] : $columns;

        $builder = $this->query->applyScopes();

        $models = $builder->addSelect(
            $this->shouldSelect($columns)
        )->getSimpleModels();

        $this->hydrateSimplePivotRelation($models);

        if (count($models) > 0) {
            $models = $builder->eagerLoadRelations($models);
        }

        return $this->related->newCollection($models);
    }

    /**
     * Hydrate the pivot table relationship on the models.
     *
     * @param  array  $models
     * @return void
     */
    protected function hydrateSimplePivotRelation(array &$models)
    {
        foreach ($models as &$model) {
            $pivot = $this->cleanSimplePivotAttributes($model);

            ModelAccessor::set($model, 'pivot', $pivot);
        }
        unset($model);
    }

    /**
     * Get the pivot attributes from a model.
     *
     * @param  stdClass|array  $model
     * @return array
     */
    protected function cleanSimplePivotAttributes(&$model)
    {
        $values = ModelAccessor::createBasedOnModel($model);

        foreach ($model as $key => &$value) {
            if (strpos($key, 'pivot_') === 0) {
                ModelAccessor::set($values, substr($key, 6), $value);

                ModelAccessor::delete($model, $key);
            }
        }
        unset($value);

        return $values;
    }
}
