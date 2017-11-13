<?php

namespace Volosyuk\SimpleEloquent;

use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Volosyuk\SimpleEloquent\Relations\Relation;

/**
 * Class Builder
 * @package Eloomi
 */
class Builder extends \Illuminate\Database\Eloquent\Builder
{
    /**
     * @param array $columns
     * @return \Illuminate\Support\Collection
     */
    public function getSimple($columns = ['*'])
    {
        $builder = $this->applyScopes();

        $models = $builder->getSimpleModels($columns);

        if (count($models) > 0) {
            $models = $builder->eagerLoadRelationsSimple($models);
        }

        return collect($models);
    }

    /**
     * @param mixed $id
     * @param array $columns
     * @return Collection|\Illuminate\Database\Eloquent\Model|null|static
     */
    public function findSimple($id, $columns = ['*'])
    {
        if (is_array($id)) {
            return $this->findManySimple($id, $columns);
        }

        $this->query->where($this->model->getQualifiedKeyName(), '=', $id);

        return $this->firstSimple($columns);
    }

    /**
     * Execute the query and get the first result.
     *
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Model|static|null
     */
    public function firstSimple($columns = ['*'])
    {
        return $this->take(1)->getSimple($columns)->first();
    }

    /**
     * Execute the query and get the first result or throw an exception.
     *
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Model|static
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function firstSimpleOrFail($columns = ['*'])
    {
        if (! is_null($model = $this->firstSimple($columns))) {
            return $model;
        }

        throw (new ModelNotFoundException)->setModel(get_class($this->model));
    }

    /**
     * Find multiple models by their primary keys.
     *
     * @param  array  $ids
     * @param  array  $columns
     * @return Collection
     */
    public function findManySimple($ids, $columns = ['*'])
    {
        if (empty($ids)) {
            return Collection::make([]);
        }

        $this->query->whereIn($this->model->getQualifiedKeyName(), $ids);

        return $this->getSimple($columns);
    }

    /**
     * Paginate the given query.
     *
     * @param  int  $perPage
     * @param  array  $columns
     * @param  string  $pageName
     * @param  int|null  $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     *
     * @throws \InvalidArgumentException
     */
    public function paginateSimple($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $perPage = $perPage ?: $this->model->getPerPage();

        $query = $this->toBase();

        $total = $query->getCountForPagination();

        $results = $total
            ? $this->forPage($page, $perPage)->getSimple($columns)
            : $this->model->newCollection();

        return new LengthAwarePaginator($results, $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }



    /**
     * Get simple models without eager loading.
     *
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Model[]
     */
    public function getSimpleModels($columns = ['*'])
    {
        return $this->query->get($columns)->all();
    }

    /**
     * Eagerly load the relationship on a set of models.
     *
     * @param  array  $models
     * @param  string  $name
     * @param  \Closure  $constraints
     * @return array
     */
    protected function loadSimpleRelation(array $models, $name, Closure $constraints)
    {
        /**
         * First we will "back up" the existing where conditions on the query so we can
         * add our eager constraints. Then we will merge the wheres that were on the
         * query back to it in order that any where conditions might be specified.
         * @var Relation|\Illuminate\Database\Eloquent\Relations\Relation $relation
         */
        $relation = $this->getRelation($name);

        $relation->addEagerConstraintsSimple($models);

        $constraints($relation);

        $models = $relation->initSimpleRelation($models, $name);

        // Once we have the results, we just match those back up to their parent models
        // using the relationship instance. Then we just return the finished arrays
        // of models which have been eagerly hydrated and are readied for return.
        return $relation->eagerLoadAndMatchSimple($models, $name);
    }

    /**
     * Eager load the relationships for the models.
     *
     * @param  array  $models
     * @return array
     */
    protected function eagerLoadRelationsSimple(array $models)
    {
        foreach ($this->eagerLoad as $name => $constraints) {
            if (strpos($name, '.') === false) {
                $models = $this->loadSimpleRelation($models, $name, $constraints);
            }
        }

        return $models;
    }
}
