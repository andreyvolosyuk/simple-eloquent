<?php

namespace Volosyuk\SimpleEloquent\Relations;

use Illuminate\Database\Eloquent\Relations\HasOneThrough as BaseHasOneThrough;
use Illuminate\Support\Collection;
use Volosyuk\SimpleEloquent\ModelAccessor;

class HasOneThrough extends BaseHasOneThrough
{
    use Relation, Pivot;

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
        $dictionary = [];

        foreach ($results as $result) {
            $dictionary[ModelAccessor::get($result, 'laravel_through_key')][] = $result;
        }

        foreach ($models as &$model) {
            $keyName = $this->parent->getKeyName();

            if (isset($dictionary[$key = ModelAccessor::get($model, $keyName)])) {
                $value = $dictionary[$key];

                ModelAccessor::set($model, $relation, reset($value));
            }
        }
        unset($model);

        return $models;
    }

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param  array  $models
     * @return void
     */
    public function addEagerConstraintsSimple(array $models)
    {
        $table = $this->parent->getTable();

        $this->query->whereIn($table.'.'.$this->firstKey, $this->getKeys($models, $this->localKey));
    }
}
