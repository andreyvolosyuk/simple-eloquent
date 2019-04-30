<?php

namespace Volosyuk\SimpleEloquent\Relations;

use Illuminate\Database\Eloquent\Relations\HasMany as BaseHasMany;
use Illuminate\Support\Collection;
use stdClass;
use Volosyuk\SimpleEloquent\ModelAccessor;

/**
 * Class HasManyWithSimple
 * @package Volosyuk\SimpleEloquent
 */
class HasMany extends BaseHasMany
{
    use Relation;

    /**
     * @param array $models
     * @param Collection $results
     * @param $relation
     * @return array|stdClass[]
     */
    protected function matchSimple(array &$models, Collection $results, $relation)
    {
        $dictionary = [];

        $foreign = $this->getForeignKeyName();

        foreach ($results as $result) {
            $dictionary[ModelAccessor::get($result, $foreign)][] = $result;
        }

        foreach ($models as &$model) {
            $value = [];

            if (isset($dictionary[$key = ModelAccessor::get($model, $this->localKey)])) {
                $value = $dictionary[$key];
            }

            ModelAccessor::set($model, $relation, Collection::make($value));
        }
        unset($model);

        return $models;
    }
}
