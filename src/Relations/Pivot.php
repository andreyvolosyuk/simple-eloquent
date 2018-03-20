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
        $foreign = $this->foreignPivotKey;

        $dictionary = [];

        foreach ($results as $result) {
            $dictionaryKey = ModelAccessor::get(ModelAccessor::get($result, 'pivot'), $foreign);

            $dictionary[$dictionaryKey][] = $result;
        }

        foreach ($models as &$model) {
            $collection = Collection::make();

            if (isset($dictionary[$key = ModelAccessor::get($model, $this->parent->getKeyName())])) {
                $collection = Collection::make($dictionary[$key]);
            }

            ModelAccessor::set($model, $relation, $collection);
        }
        unset($model);

        return $models;
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param array $columns
     *
     * @return Collection
     *
     * @throws \Exception
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
            $models = $builder->eagerLoadRelationsSimple($models);
        }

        return $this->related->newCollection($models);
    }

    /**
     * Get a paginator for the "select" statement.
     *
     * @param  int  $perPage
     * @param  array  $columns
     * @param  string  $pageName
     * @param  int|null  $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        if ($this->query->isSimple()) {
            $this->query->addSelect($this->shouldSelect($columns));

            return tap($this->query->paginate($perPage, $columns, $pageName, $page), function ($paginator) {
                $items = $paginator->items();

                $this->hydrateSimplePivotRelation($items);
            });
        }

        return parent::paginate($perPage, $columns, $pageName, $page);
    }

    /**
     * Paginate the given query into a simple paginator.
     *
     * @param  int  $perPage
     * @param  array  $columns
     * @param  string  $pageName
     * @param  int|null  $page
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function simplePaginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        if ($this->query->isSimple()) {
            $this->query->addSelect($this->shouldSelect($columns));
            return tap($this->query->simplePaginate($perPage, $columns, $pageName, $page), function ($paginator) {
                $items = $paginator->items();

                $this->hydrateSimplePivotRelation($items);
            });
        }

        return parent::simplePaginate($perPage, $columns, $pageName, $page);
    }


    /**
     * Chunk the results of the query.
     *
     * @param  int  $count
     * @param  callable  $callback
     * @return bool
     */
    public function chunk($count, callable $callback)
    {
        if ($this->query->isSimple()) {

            $this->query->addSelect($this->shouldSelect());

            return $this->query->chunk($count, function ($results) use ($callback) {
                $items = $results->all();

                $this->hydrateSimplePivotRelation($items);

                return $callback($results);
            });
        }

        return parent::chunk($count, $callback);
    }

    /**
     * @param array $columns
     *
     * @return Collection
     *
     * @throws \Exception
     */
    public function get($columns = ['*'])
    {
        if ($this->query->isSimple()) {
            return $this->getSimple($columns);
        }

        return parent::get($columns);
    }

    /**
     * Hydrate the pivot table relationship on the models.
     *
     * @param array $models
     *
     * @return void
     *
     * @throws \Exception
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
     * @param  stdClass|array $model
     *
     * @return array
     *
     * @throws \Exception
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
