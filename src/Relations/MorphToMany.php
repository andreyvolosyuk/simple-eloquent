<?php

namespace Volosyuk\SimpleEloquent\Relations;

/**
 * Class MorphToManyWithSimple
 * @package Volosyuk\SimpleEloquent
 */
class MorphToMany extends \Illuminate\Database\Eloquent\Relations\MorphToMany
{
    use Relation, Pivot;

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param  array  $models
     * @return void
     */
    public function addEagerConstraintsSimple(array $models)
    {
        $this->query->where($this->table.'.'.$this->morphType, $this->morphClass);
    }
}
