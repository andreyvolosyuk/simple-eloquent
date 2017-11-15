<?php

namespace Volosyuk\SimpleEloquent\Relations;

use Illuminate\Support\Collection;
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
     * @return array
     */
    protected function matchSimple(array &$models, Collection $results, $relation)
    {
        $dictionary = [];

        $foreign = $this->getPlainForeignKey();

        // First we will create a dictionary of models keyed by the foreign key of the
        // relationship as this will allow us to quickly access all of the related
        // models without having to do nested looping which will be quite slow.
        foreach ($results as $result) {
            $dictionary[ModelAccessor::get($result, $foreign)][] = $result;
        }

        // Once we have the dictionary we can simply spin through the parent models to
        // link them up with their children using the keyed dictionary to make the
        // matching very convenient and easy work. Then we'll just return them.
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
