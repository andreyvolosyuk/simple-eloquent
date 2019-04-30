<?php

namespace Volosyuk\SimpleEloquent\Relations;

use Illuminate\Database\Eloquent\Relations\MorphOne as BaseMorphOne;
use Illuminate\Support\Collection;

/**
 * Class MorphOneWithSimple
 * @package Volosyuk\SimpleEloquent
 */
class MorphOne extends BaseMorphOne
{
    use Relation, HasOneOrMany;

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
        return $this->matchOneSimple($models, $results, $relation);
    }
}
