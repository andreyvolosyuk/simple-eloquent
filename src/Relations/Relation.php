<?php

namespace Volosyuk\SimpleEloquent\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use stdClass;
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
     * An array to map class names to their morph names in database.
     *
     * @var array
     */
    public static $morphMap = [];

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
     * @return array|stdClass[]
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
        $this->query->whereIn($this->getQualifiedForeignKeyName(), $this->getKeys($models));
    }


    /**
     * Set or get the morph map for polymorphic relations.
     *
     * @param  array|null  $map
     * @param  bool  $merge
     * @return array
     */
    public static function morphMap(array $map = null, $merge = true)
    {
        $map = static::buildMorphMapFromModels($map);

        if (is_array($map)) {
            static::$morphMap = $merge && static::$morphMap
                ? $map + static::$morphMap : $map;
        }

        return static::$morphMap;
    }


    /**
     * Builds a table-keyed array from model class names.
     *
     * @param  string[]|null  $models
     * @return array|null
     */
    protected static function buildMorphMapFromModels(array $models = null)
    {
        if (is_null($models) || Arr::isAssoc($models)) {
            return $models;
        }

        return array_combine(array_map(function ($model) {
            return (new $model)->getTable();
        }, $models), $models);
    }
}
