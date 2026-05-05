<?php

namespace Volosyuk\SimpleEloquent;

use Illuminate\Support\Collection;
use stdClass;
use Volosyuk\SimpleEloquent\Relations\HasRelationships;

/**
 * Trait SimpleEloquent
 *
 * @package Volosyuk\SimpleEloquent
 * @authour Volosyuk Andrey <valasiuk.andrei@gmail.com>
 * @mixin Builder
 */
trait SimpleEloquent
{
    use HasRelationships;

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param Builder $query
     * @return Builder
     */
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }

    /**
     * Get all of the models from the database (simple mode).
     *
     * @param  array|mixed  $columns
     * @return Collection<int,stdClass|array>
     */
    public static function allSimple($columns = ['*'])
    {
        $columns = is_array($columns) ? $columns : func_get_args();

        /**
         * @var Builder $query
         */
        $query = (new static)->newQuery();

        return $query->simple()->get($columns);
    }
}
