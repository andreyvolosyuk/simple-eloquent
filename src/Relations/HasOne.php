<?php

namespace Volosyuk\SimpleEloquent\Relations;

use Illuminate\Support\Collection;

/**
 * Class HasOneWithSimple
 * @package app\Eloomi
 */
class HasOne extends \Illuminate\Database\Eloquent\Relations\HasOne
{
    use Relation, HasOneOrMany;

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  array  $models
     * @param  Collection  $results
     * @param  string  $relation
     * @return array
     */
    protected function matchSimple(array $models, Collection $results, $relation)
    {
        return $this->matchOneSimple($models, $results, $relation);
    }
}
