<?php

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Volosyuk\SimpleEloquent\Relations\MorphToMany;
use Volosyuk\SimpleEloquent\SimpleEloquent;

class Like extends \Illuminate\Database\Eloquent\Model
{
    use SimpleEloquent;

    protected $guarded = ['id'];

    public $timestamps = [];

    /**
     * @return MorphTo
     */
    public function likable()
    {
        return $this->morphTo('like_for');
    }

    /**
     * That's really stupid exapmle when one like can be related to various articles :)
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany|MorphToMany
     */
    public function articles()
    {
        return $this->morphedByMany(Article::class, 'likable', 'likable', 'like_id', 'likable_id');
    }
}
