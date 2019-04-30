<?php

namespace Volosyuk\SimpleEloquent\Relations;

use Illuminate\Database\Eloquent\Relations\MorphToMany as BaseMorphToMany;

/**
 * Class MorphToManyWithSimple
 * @package Volosyuk\SimpleEloquent
 */
class MorphToMany extends BaseMorphToMany
{
    use Relation, Pivot;

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @return void
     */
    public function addEagerConstraintsSimple()
    {
        $this->query->where($this->table.'.'.$this->morphType, $this->morphClass);
    }
}
