<?php

namespace Volosyuk\SimpleEloquent\Relations;

use Illuminate\Support\Collection;
use stdClass;
use Volosyuk\SimpleEloquent\ModelAccessor;

/**
 * Class HasManyWithSimple
 * @package Volosyuk\SimpleEloquent
 */
class HasMany extends \Illuminate\Database\Eloquent\Relations\HasMany
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
            $key = ModelAccessor::get($model, $this->localKey);

            if (isset($dictionary[$key])) {
                $value = $dictionary[$key];

                ModelAccessor::set($model, $relation, Collection::make($value));
            }
        }
        unset($model);

        return $models;
    }
}
