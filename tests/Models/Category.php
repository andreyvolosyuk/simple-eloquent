<?php

use Volosyuk\SimpleEloquent\Relations\BelongsToMany;
use Volosyuk\SimpleEloquent\Relations\HasMany;
use Volosyuk\SimpleEloquent\Relations\HasManyThrough;
use Volosyuk\SimpleEloquent\Relations\HasOne;
use Volosyuk\SimpleEloquent\SimpleEloquent;

/**
 * Class Category
 *
 * @property string $name
 */
class Category extends \Illuminate\Database\Eloquent\Model
{
    use SimpleEloquent;

    protected $guarded = [];

    /**
     * @return HasOne
     */
    public function article()
    {
        return $this->hasOne(Article::class);
    }

    /**
     * @return HasMany
     */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    /**
     * @return BelongsToMany
     */
    public function multipleArticles()
    {
        return $this->belongsToMany(Article::class, 'article_categories', 'article_id', 'cat_id');
    }

    /**
     * @return HasManyThrough
     */
    public function comments()
    {
        return $this->hasManyThrough(Comment::class, Article::class);
    }
}
