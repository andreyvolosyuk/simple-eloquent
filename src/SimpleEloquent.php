<?php

namespace Volosyuk\SimpleEloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Volosyuk\SimpleEloquent\Relations\HasRelationships;

/**
 * Trait SimpleEloquent
 * @package Volosyuk\SimpleEloquent
 */
trait SimpleEloquent
{
    use HasRelationships;

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  Builder  $query
     * @return Builder|static
     */
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }

    /**
     * Get all of the models from the database.
     *
     * @param  array|mixed  $columns
     * @return Collection|static[]
     */
    public static function allSimple($columns = ['*'])
    {
        $columns = is_array($columns) ? $columns : func_get_args();

        /**
         * @var Model $instance
         */
        $instance = new static;

        return $instance->newQuery()->getSimple($columns);
    }
}
