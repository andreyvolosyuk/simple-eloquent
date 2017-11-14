<?php

class MorphToTest extends TestCase
{
    public function test_morph_to_relation_returns_related_models()
    {
        $article = Article::create(['title' => 'Test article']);
        $like = Like::create([
            'like_for_id' => $article->id,
            'like_for_type' => Article::class
        ]);

        $this->assertEquals(
            $like->likable->title,
            Like::with('likable')->firstSimple()->like_for->title
        );
    }
}
