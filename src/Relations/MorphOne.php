<?php

namespace Volosyuk\SimpleEloquent\Relations;

use Illuminate\Support\Collection;

/**
 * Class MorphOneWithSimple
 * @package App\Eloomi
 */
class MorphOne extends \Illuminate\Database\Eloquent\Relations\MorphOne
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
