<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Volosyuk\SimpleEloquent\Relations\BelongsToMany;
use Volosyuk\SimpleEloquent\Relations\HasMany;
use Volosyuk\SimpleEloquent\Relations\HasManyThrough;
use Volosyuk\SimpleEloquent\Relations\HasOne;
use Volosyuk\SimpleEloquent\Relations\HasOneThrough;
use Volosyuk\SimpleEloquent\SimpleEloquent;

/**
 * Class Category
 *
 * @property string $name
 *
 * * * related * * *
 *
 * @property Article $article
 * @property Comment $comment
 * @property Comment[]|Collection $comments
 */
class Category extends Model
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
        return $this->belongsToMany(Article::class, null, 'article_id', 'cat_id');
    }

    /**
     * @return HasManyThrough
     */
    public function comments()
    {
        return $this->hasManyThrough(Comment::class, Article::class, 'category_id', 'article_id', 'id', 'id');
    }

    /**
     * @return HasOneThrough
     */
    public function comment()
    {
        return $this->hasOneThrough(Comment::class, Article::class, 'category_id', 'article_id', 'id', 'id');
    }
}
