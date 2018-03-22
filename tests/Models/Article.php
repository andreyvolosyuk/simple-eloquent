<?php

use Volosyuk\SimpleEloquent\Relations\BelongsTo;
use Volosyuk\SimpleEloquent\Relations\BelongsToMany;
use Volosyuk\SimpleEloquent\Relations\MorphMany;
use Volosyuk\SimpleEloquent\Relations\MorphOne;
use Volosyuk\SimpleEloquent\Relations\MorphToMany;
use Volosyuk\SimpleEloquent\SimpleEloquent;

/**
 * Class Article
 *
 * @property string $title
 * @property int $category_id
 */
class Article extends \Illuminate\Database\Eloquent\Model
{
    use SimpleEloquent;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @return BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsToMany
     */
    public function multipleCategories()
    {
        return $this->belongsToMany(Category::class, 'article_categories', 'cat_id', 'article_id');
    }

    /**
     * @return MorphOne
     */
    public function like()
    {
        return $this->morphOne(Like::class, 'like_for');
    }

    /**
     * @return MorphToMany
     */
    public function likes()
    {
        return $this->morphToMany(Like::class, 'likable', 'likable', 'likable_id', 'like_id');
    }

    /**
     * @return MorphMany
     */
    public function likesMany()
    {
        return $this->morphMany(Likable::class, 'likable');
    }

    /**
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'article_users');
    }
}
