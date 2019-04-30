<?php

use Illuminate\Database\Eloquent\Model;
use Volosyuk\SimpleEloquent\SimpleEloquent;

/**
 * Class Comment
 *
 * @var string $body
 * @property int $article_id
 */
class Comment extends Model
{
    use SimpleEloquent;

    protected $guarded = [];
}
