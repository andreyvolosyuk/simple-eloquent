<?php

class HasManyThroughTest extends TestCase
{
    public function test_has_many_through_relation_returns_models_trough_another_one()
    {
        $category = Category::create(['name' => 'Test category']);
        $article = Article::create(['title' => 'Test article', 'category_id' => $category->id]);

        $this->assertCount(0, Category::simple()->with('comments')->first()->comments);

        Comment::insert([
            ['body' => 'first comment', 'article_id' => $article->id],
            ['body' => 'second comment', 'article_id' => $article->id],
        ]);

        $this->assertEquals(
            $category->comments->count(),
            Category::with('comments')->simple()->first()->comments->count()
        );

        $this->assertEquals(
            $category->comments->first()->body,
            Category::with('comments')->simple()->first()->comments->first()->body
        );
    }
}
