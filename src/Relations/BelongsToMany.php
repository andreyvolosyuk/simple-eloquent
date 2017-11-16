<?php

namespace Volosyuk\SimpleEloquent\Relations;

/**
 * Class BelongsToManyWithSimple
 * @package Volosyuk\SimpleEloquent
 */
class BelongsToMany extends \Illuminate\Database\Eloquent\Relations\BelongsToMany
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
        $this->query->whereIn($this->getQualifiedForeignPivotKeyName(), $this->getKeys($models, $this->parentKey));
    }
}
