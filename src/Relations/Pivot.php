<?php

namespace Volosyuk\SimpleEloquent\Relations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Volosyuk\SimpleEloquent\ModelAccessor;

/**
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

        // First we will build a dictionary of child models keyed by the foreign key
        // of the relation so that we will easily and quickly match them to their
        // parents without having a possibly slow inner loops for every models.
        $dictionary = [];

        foreach ($results as $result) {
            $dictionaryKey = ModelAccessor::get(ModelAccessor::get($result, 'pivot'), $foreign);

            $dictionary[$dictionaryKey][] = $result;
        }

        // Once we have an array dictionary of child objects we can easily match the
        // children back to their parent using the dictionary and the keys on the
        // the parent models. Then we will return the hydrated models back out.
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
        // First we'll add the proper select columns onto the query so it is run with
        // the proper columns. Then, we will get the results and hydrate out pivot
        // models with the result of those columns as a separate model relation.
        $columns = $this->query->getQuery()->columns ? [] : $columns;

        $select = $this->getSelectColumns($columns);

        $builder = $this->query->applyScopes();

        $models = $builder->addSelect($select)->getSimpleModels();

        $this->hydrateSimplePivotRelation($models);

        // If we actually found models we will also eager load any relationships that
        // have been specified as needing to be eager loaded. This will solve the
        // n + 1 query problem for the developer and also increase performance.
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
        // To hydrate the pivot relationship, we will just gather the pivot attributes
        // and create a new Pivot model, which is basically a dynamic model that we
        // will set the attributes, table, and connections on so it they be used.
        foreach ($models as &$model) {
            $pivot = $this->cleanSimplePivotAttributes($model);

            ModelAccessor::set($model, 'pivot', $pivot);
        }
        unset($model);
    }

    /**
     * Get the pivot attributes from a model.
     *
     * @param  \stdClass  $model
     * @return array
     */
    protected function cleanSimplePivotAttributes(&$model)
    {
        $values = ModelAccessor::createBasedOnModel($model);

        foreach ($model as $key => &$value) {
            // To get the pivots attributes we will just take any of the attributes which
            // begin with "pivot_" and add those to this arrays, as well as unsetting
            // them from the parent's models since they exist in a different table.
            if (strpos($key, 'pivot_') === 0) {
                ModelAccessor::set($values, substr($key, 6), $value);

                ModelAccessor::delete($model, $key);
            }
        }
        unset($value);

        return $values;
    }
}
