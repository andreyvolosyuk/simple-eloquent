<?php

namespace Volosyuk\SimpleEloquent;

use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorInterface;
use Illuminate\Contracts\Pagination\Paginator as PaginatorInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use stdClass;
use Volosyuk\SimpleEloquent\Relations\Relation;

/**
 * Class Builder
 * @package Volosyuk\SimpleEloquent
 */
class Builder extends \Illuminate\Database\Eloquent\Builder
{
    /**
     * @var bool
     */
    protected $simple = false;

    /**
     * @return $this
     */
    public function simple()
    {
        $this->simple = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSimple()
    {
        return $this->simple === true;
    }

    /**
     * @param array $columns
     * @return EloquentCollection|Collection|static[]
     */
    public function get($columns = ['*'])
    {
        if ($this->isSimple()) {
            return $this->getSimple($columns);
        }

        return parent::get($columns);
    }

    /**
     * @param mixed $id
     * @param array $columns
     * @return Collection|stdClass|array|null
     */
    public function find($id, $columns = ['*'])
    {
        if ($this->isSimple()) {
            return $this->findSimple($id, $columns);
        }

        return parent::find($id, $columns);
    }

    /**
     * @param mixed $id
     * @param array $columns
     * @return array|EloquentCollection|Model|Collection|stdClass
     */
    public function findOrFail($id, $columns = ['*'])
    {
        if ($this->isSimple()) {
            return $this->findSimpleOrFail($id, $columns);
        }

        return parent::findOrFail($id, $columns);
    }

    /**
     * @param array $columns
     * @return array|Model|null|object|stdClass|static
     */
    public function first($columns = ['*'])
    {
        if ($this->isSimple()) {
            return $this->firstSimple($columns);
        }

        return parent::first($columns);
    }

    /**
     * @param array $columns
     * @return array|Model|stdClass|static
     */
    public function firstOrFail($columns = ['*'])
    {
        if ($this->isSimple()) {
            return $this->firstSimpleOrFail($columns);
        }

        return parent::firstOrFail($columns);
    }

    /**
     * @param array|Arrayable $ids
     * @param array $columns
     * @return EloquentCollection|Collection
     */
    public function findMany($ids, $columns = ['*'])
    {
        if ($this->isSimple()) {
            return $this->findManySimple($ids, $columns);
        }

        return parent::findMany($ids, $columns);
    }

    /**
     * @param null $perPage
     * @param array $columns
     * @param string $pageName
     * @param null $page
     * @return LengthAwarePaginatorInterface
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        if ($this->isSimple()) {
            return $this->paginateSimple($perPage, $columns, $pageName, $page);
        }

        return parent::paginate($perPage, $columns, $pageName, $page);
    }

    /**
     * @param null $perPage
     * @param array $columns
     * @param string $pageName
     * @param null $page
     * @return PaginatorInterface
     */
    public function simplePaginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        if ($this->isSimple()) {
            return $this->simplePaginateSimple($perPage, $columns, $pageName, $page);
        }

        return parent::simplePaginate($perPage, $columns, $pageName, $page);
    }

    /**
     * @param array $columns
     * @return Collection
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
     * @return Collection|stdClass|array|null
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
     * Find a model by its primary key or throw an exception.
     *
     * @param  mixed  $id
     * @param  array  $columns
     * @return stdClass|array|Collection
     *
     * @throws ModelNotFoundException
     */
    public function findSimpleOrFail($id, $columns = ['*'])
    {
        $result = $this->findSimple($id, $columns);

        if (is_array($id)) {
            if (count($result) == count(array_unique($id))) {
                return $result;
            }
        } elseif (! is_null($result)) {
            return $result;
        }

        throw (new ModelNotFoundException)->setModel(
            get_class($this->model), $id
        );
    }

    /**
     * Execute the query and get the first result.
     *
     * @param  array  $columns
     * @return stdClass|array|null
     */
    public function firstSimple($columns = ['*'])
    {
        return $this->take(1)->getSimple($columns)->first();
    }

    /**
     * Execute the query and get the first result or throw an exception.
     *
     * @param  array  $columns
     * @return array|stdClass
     *
     * @throws ModelNotFoundException
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
     * @return LengthAwarePaginatorInterface
     *
     * @throws InvalidArgumentException
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
     * Get a paginator only supporting simple next and previous links.
     *
     * This is more efficient on larger data-sets, etc.
     *
     * @param  int  $perPage
     * @param  array  $columns
     * @param  string  $pageName
     * @param  int|null  $page
     * @return PaginatorInterface
     */
    public function simplePaginateSimple($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $this->skip(($page - 1) * $perPage)->take($perPage + 1);

        return $this->simplePaginator($this->getSimple($columns), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }

    /**
     * Get simple models without eager loading.
     *
     * @param  array  $columns
     * @return stdClass[]|array
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
     * @param Closure $constraints
     * @return array
     */
    protected function loadSimpleRelation(array $models, $name, Closure $constraints)
    {
        /**
         * @var Relation $relation
         */
        $relation = $this->getRelation($name);

        $relation->addEagerConstraintsSimple($models);

        $constraints($relation);

        $models = $relation->initSimpleRelation($models, $name);

        return $relation->eagerLoadAndMatchSimple($models, $name);
    }

    /**
     * Eager load the relationships for the models.
     *
     * @param  array  $models
     * @return array
     */
    public function eagerLoadRelationsSimple(array $models)
    {
        foreach ($this->eagerLoad as $name => $constraints) {
            if (strpos($name, '.') === false) {
                $models = $this->loadSimpleRelation($models, $name, $constraints);
            }
        }

        return $models;
    }
}
