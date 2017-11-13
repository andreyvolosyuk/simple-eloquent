<?php

namespace Volosyuk\SimpleEloquent\Relations;

use Illuminate\Support\Collection;
use Volosyuk\SimpleEloquent\ModelAccessor;

/**
 * Class MorphManyWIthSimple
 * @package App\Eloomi
 */
class MorphMany extends \Illuminate\Database\Eloquent\Relations\MorphMany
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
                $value = $this->getRelationValue($dictionary, $key, $type);

                ModelAccessor::set($model, $relation, $value);
            }
        }

        return $models;
    }
}
