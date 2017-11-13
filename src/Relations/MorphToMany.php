<?php

namespace Volosyuk\SimpleEloquent\Relations;

/**
 * Class MorphToManyWithSimple
 * @package App\Eloomi
 */
class MorphToMany extends \Illuminate\Database\Eloquent\Relations\MorphToMany
{
    use Relation, Pivot;
}
