<?php

namespace Volosyuk\SimpleEloquent\Relations;

/**
 * Class MorphToManyWithSimple
 * @package Volosyuk\SimpleEloquent
 */
class MorphToMany extends \Illuminate\Database\Eloquent\Relations\MorphToMany
{
    use Relation, Pivot;
}
