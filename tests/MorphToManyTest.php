<?php

use Illuminate\Database\Capsule\Manager as DB;

class MorphToManyTest extends TestCase
{
    public function test_morphed_by_many_returns_related_models()
    {
        $firstArticle = Article::create(['title' => 'First test article']);
        $secondArticle = Article::create(['title' => 'Second test article']);
        $like = Like::create(['like_for_id' => 1, 'like_for_type' => 'test']);

        DB::table('likable')->insert([
            ['like_id' => $like->id, 'likable_id' => $firstArticle->id, 'likable_type' => Article::class],
            ['like_id' => $like->id, 'likable_id' => $secondArticle->id, 'likable_type' => Article::class]
        ]);

        $likeSimple = Like::with('articles')->firstSimple();
        $this->assertEquals($like->articles->count(), $likeSimple->articles->count());
        $this->assertEquals(
            $like->articles->first()->title,
            $likeSimple->articles->first()->title
        );
    }

    public function test_morph_to_many_returns_related_models()
    {
        $article = Article::create(['title' => 'First test article']);
        $firstLike = Like::create(['like_for_id' => 1, 'like_for_type' => 'test']);
        $secondLike = Like::create(['like_for_id' => 1, 'like_for_type' => 'test']);

        DB::table('likable')->insert([
            ['like_id' => $firstLike->id, 'likable_id' => $article->id, 'likable_type' => Article::class],
            ['like_id' => $secondLike->id, 'likable_id' => $article->id, 'likable_type' => Article::class]
        ]);

        $articleSimple = Article::with('likes')->firstSimple();
        $this->assertEquals($article->likes->count(), $articleSimple->likes->count());

        $this->assertEquals(
            $article->likes->first()->id,
            $articleSimple->likes->first()->id
        );
    }
}
