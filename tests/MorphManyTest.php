<?php

use Illuminate\Database\Capsule\Manager as DB;

class MorphManyTest extends TestCase
{
    public function test_morph_many_returns_related_models()
    {
        $article = Article::create(['title' => 'First test article']);

        $this->assertCount(0, Article::simple()->with('likesMany')->first()->likesMany);

        DB::table('likable')->insert([
            ['likable_id' => $article->id, 'likable_type' => Article::class, 'like_id' => 222],
            ['likable_id' => $article->id, 'likable_type' => Article::class, 'like_id' => 222],
        ]);

        $simpleArticle = Article::simple()->with('likesMany')->first();

        $this->assertEquals($article->likesMany->count(), $simpleArticle->likesMany->count());
        $this->assertEquals(
            $article->likesMany->first()->id,
            $simpleArticle->likesMany->first()->id
        );
    }
}