<?php

namespace Volosyuk\SimpleEloquent\Relations;

use Illuminate\Database\Eloquent\Relations\BelongsToMany as BaseBelongsToMany;

/**
 * Class BelongsToManyWithSimple
 * @package Volosyuk\SimpleEloquent
 */
class BelongsToMany extends BaseBelongsToMany
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
