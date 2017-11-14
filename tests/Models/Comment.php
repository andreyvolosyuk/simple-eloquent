<?php

use Volosyuk\SimpleEloquent\SimpleEloquent;

/**
 * Class Comment
 *
 * @var string $body
 * @property int $article_id
 */
class Comment extends \Illuminate\Database\Eloquent\Model
{
    use SimpleEloquent;

    protected $guarded = ['id'];
}
