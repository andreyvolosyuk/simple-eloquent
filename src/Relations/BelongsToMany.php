<?php

namespace Volosyuk\SimpleEloquent\Relations;

/**
 * Class BelongsToManyWithSimple
 * @package Eloomi
 */
class BelongsToMany extends \Illuminate\Database\Eloquent\Relations\BelongsToMany
{
    use Relation, Pivot;
}
