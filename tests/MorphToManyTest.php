<?php

use Illuminate\Database\Capsule\Manager as DB;

class MorphToManyTest extends TestCase
{
    public function test_morphed_by_many_returns_related_models()
    {
        $firstArticle = Article::create([
            'id' => 15,
            'title' => 'First test article'
        ]);
        $secondArticle = Article::create([
            'id' => 16,
            'title' => 'Second test article'
        ]);
        $like = Like::create([
            'id' => 30,
            'like_for_id' => 1,
            'like_for_type' => 'test'
        ]);

        DB::table('likable')->insert([
            [
                'id' => 50,
                'like_id' => $like->id,
                'likable_id' => $firstArticle->id,
                'likable_type' => Article::class
            ],
            [
                'id' => 51,
                'like_id' => $like->id,
                'likable_id' => $secondArticle->id,
                'likable_type' => Article::class
            ]
        ]);

        $likeSimple = Like::with('articles')->firstSimple();

        $this->articlesTitlesAreEqual($like->articles->first(), $likeSimple->articles->first())
            ->assertEquals($like->articles->count(), $likeSimple->articles->count());
    }

    public function test_relational_method_morphed_by_many_does_interact_with_simple()
    {
        $article = Article::create([
            'id' => 30,
            'title' => 'First test article'
        ]);
        $like = Like::create([
            'id' => 60,
            'like_for_id' => 1,
            'like_for_type' => 'test'
        ]);

        DB::table('likable')->insert([
            'id' => 30,
            'like_id' => $like->id,
            'likable_id' => $article->id,
            'likable_type' => Article::class
        ]);

        $this->articlesTitlesAreEqual(
            $like->articles()->first(),
            $like->articles()->simple()->first()
        );
    }

    public function test_morph_to_many_returns_related_models()
    {
        $article = Article::create([
            'id' => 40,
            'title' => 'First test article'
        ]);
        $firstLike = Like::create([
            'id' => 30,
            'like_for_id' => 1,
            'like_for_type' => 'test'
        ]);
        $secondLike = Like::create([
            'id' => 31,
            'like_for_id' => 1,
            'like_for_type' => 'test'
        ]);

        $this->assertCount(0, Article::simple()->with('likes')->first()->likes);

        DB::table('likable')->insert([
            [
                'id' => 35,
                'like_id' => $firstLike->id,
                'likable_id' => $article->id,
                'likable_type' => Article::class
            ],
            [
                'id' => 36,
                'like_id' => $secondLike->id,
                'likable_id' => $article->id,
                'likable_type' => Article::class
            ]
        ]);

        $articleSimple = Article::simple()->with('likes')->first();

        $this->compareLikes($article->likes->first(), $articleSimple->likes->first())
            ->assertEquals($article->likes->count(), $articleSimple->likes->count());
    }

    public function test_relational_method_morph_to_many_does_interact_with_simple()
    {
        $article = Article::create([
            'id' => 50,
            'title' => 'First test article'
        ]);
        $like = Like::create([
            'id' => 30,
            'like_for_id' => 1,
            'like_for_type' => 'test'
        ]);
        DB::table('likable')->insert([
            [
                'id' => 80,
                'like_id' => $like->id,
                'likable_id' => $article->id,
                'likable_type' => Article::class
            ]
        ]);

        $this->compareLikes(
            $article->likes()->first(),
            $article->likes()->simple()->first()
        )->compareLikes(
            $article->likes()->find($like->id),
            $article->likes()->simple()->find($like->id)
        )->compareLikes(
            $article->likes()->findMany([$like->id])->first(),
            $article->likes()->simple()->findMany([$like->id])->first()
        )->compareLikes(
            $article->likes()->get()->first(),
            $article->likes()->simple()->get()->first()
        )->compareLikes(
            $article->likes()->paginate()->getCollection()->first(),
            $article->likes()->simple()->paginate()->getCollection()->first()
        )->compareLikes(
            $article->likes()->simplePaginate()->getCollection()->first(),
            $article->likes()->simple()->simplePaginate()->getCollection()->first()
        );
    }

    private function compareLikes(Like $like, stdClass $primitiveLike)
    {
        $this->assertEquals(
            $like->id,
            $primitiveLike->id
        );

        return $this;
    }
}
