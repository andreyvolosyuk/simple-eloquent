<?php

namespace Volosyuk\SimpleEloquent\Relations;

use Illuminate\Support\Collection;
use Volosyuk\SimpleEloquent\ModelAccessor;

/**
 * Class BelongsToWithSimple
 * @package Eloomi
 */
class BelongsTo extends \Illuminate\Database\Eloquent\Relations\BelongsTo
{
    use Relation;

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  array   $models
     * @param  Collection  $results
     * @param  string  $relation
     * @return array
     */
    protected function matchSimple(array $models, Collection $results, $relation)
    {
        $foreign = $this->foreignKey;

        $other = $this->otherKey;

        $dictionary = [];

        foreach ($results as $result) {
            $dictionary[ModelAccessor::get($result, $other)] = $result;
        }

        foreach ($models as &$model) {
            if (isset($dictionary[ModelAccessor::get($model, $foreign)])) {
                ModelAccessor::set($model, $relation, $dictionary[ModelAccessor::get($model, $foreign)]);
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
        // We'll grab the primary key name of the related models since it could be set to
        // a non-standard name and not "id". We will then construct the constraint for
        // our eagerly loading query so it returns the proper models from execution.
        $key = $this->related->getTable().'.'.$this->otherKey;

        $this->query->whereIn($key, $this->getEagerModelKeysSimple($models));
    }

    /**
     * Gather the keys from an array of related models.
     *
     * @param  array  $models
     * @return array
     */
    protected function getEagerModelKeysSimple(array $models)
    {
        $keys = [];

        // First we need to gather all of the keys from the parent models so we know what
        // to query for via the eager loading query. We will add them to an array then
        // execute a "where in" statement to gather up all of those related records.
        foreach ($models as $model) {
            if (! is_null($value = ModelAccessor::get($model, $this->foreignKey))) {
                $keys[] = $value;
            }
        }

        // If there are no keys that were not null we will just return an array with either
        // null or 0 in (depending on if incrementing keys are in use) so the query wont
        // fail plus returns zero results, which should be what the developer expects.
        if (count($keys) === 0) {
            return [$this->related->getIncrementing() &&
            $this->related->getKeyType() === 'int' ? 0 : null, ];
        }

        return array_values(array_unique($keys));
    }
}
