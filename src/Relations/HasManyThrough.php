<?php

namespace Volosyuk\SimpleEloquent\Relations;

use Illuminate\Support\Collection;
use Volosyuk\SimpleEloquent\ModelAccessor;

/**
 * Class HasManyThroughWithSimple
 * @package App\Eloomi
 */
class HasManyThrough extends \Illuminate\Database\Eloquent\Relations\HasManyThrough
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
    protected function matchSimple(array &$models, Collection $results, $relation)
    {
        $dictionary = [];

        $foreign = $this->firstKey;

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
            $key = ModelAccessor::get($model, $this->parent->getKeyName());

            if (isset($dictionary[$key])) {
                ModelAccessor::set($model, $relation, Collection::make($dictionary[$key]));
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
