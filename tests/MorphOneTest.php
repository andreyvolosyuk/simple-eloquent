<?php

class MorphOneTest extends TestCase
{
    public function test_morph_one_returns_morphed_models()
    {
        $article = Article::create(['title' => 'Test article']);
        Like::create([
            'like_for_id' => $article->id,
            'like_for_type' => get_class($article)
        ]);

        $this->assertEquals(
            $article->like->id,
            Article::with('like')->firstSimple()->like->id
        );
    }
}