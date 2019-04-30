<?php

namespace Volosyuk\SimpleEloquent\Relations;

use Illuminate\Database\Eloquent\Relations\MorphMany as BaseMorphMany;
use Illuminate\Support\Collection;
use Volosyuk\SimpleEloquent\ModelAccessor;

/**
 * Class MorphManyWIthSimple
 * @package Volosyuk\SimpleEloquent
 */
class MorphMany extends BaseMorphMany
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
        return $this->matchOneOrManySimple($models, $results, $relation, 'many');
    }

    /**
     * Match the eagerly loaded results to their many parents.
     *
     * @param  array   $models
     * @param  Collection  $results
     * @param  string  $relation
     * @param  string  $type
     * @return array
     */
    protected function matchOneOrManySimple(array &$models, Collection $results, $relation, $type)
    {
        $dictionary = [];

        $foreign = $this->getForeignKeyName();

        foreach ($results as $result) {
            $dictionary[ModelAccessor::get($result, $foreign)][] = $result;
        }

        foreach ($models as &$model) {
            $value = Collection::make();

            if (isset($dictionary[$key = ModelAccessor::get($model, $this->localKey)])) {
                $value = $this->getRelationValue($dictionary, $key, $type);
            }

            ModelAccessor::set($model, $relation, $value);
        }

        return $models;
    }
}
