<?php

class HasManyThroughTest extends TestCase
{
    /**
     * @var Category
     */
    private $category;

    /**
     * @var Article
     */
    private $article;

    protected function setUp()
    {
        parent::setUp();

        $this->category = Category::create(['name' => 'Test category']);
        $this->article = Article::create(['title' => 'Test article', 'category_id' => $this->category->id]);
    }

    public function test_has_many_through_relation_returns_models_trough_another_one()
    {
        $this->assertCount(0, Category::simple()->with('comments')->first()->comments);

        Comment::insert([
            ['body' => 'first comment', 'article_id' => $this->article->id],
            ['body' => 'second comment', 'article_id' => $this->article->id],
        ]);

        $this->checkCommentsBodies(
            $this->category->comments->first(),
            Category::with('comments')->simple()->first()->comments->first()
        )->assertEquals(
            $this->category->comments->count(),
            Category::with('comments')->simple()->first()->comments->count()
        );
    }

    public function test_relational_method_has_many_through_does_interact_with_simple()
    {
        $comment = Comment::create(['body' => 'first comment', 'article_id' => $this->article->id]);

        $this->checkCommentsBodies(
            $this->category->comments()->first(),
            $this->category->comments()->simple()->first()
        )->checkCommentsBodies(
            $this->category->comments()->find($comment->id),
            $this->category->comments()->simple()->find($comment->id)
        )->checkCommentsBodies(
            $this->category->comments()->findMany([$comment->id])->first(),
            $this->category->comments()->simple()->findMany([$comment->id])->first()
        )->checkCommentsBodies(
            $this->category->comments()->get()->first(),
            $this->category->comments()->simple()->get()->first()
        )->checkCommentsBodies(
            $this->category->comments()->paginate()->getCollection()->first(),
            $this->category->comments()->simple()->paginate()->getCollection()->first()
        )->checkCommentsBodies(
            $this->category->comments()->simplePaginate()->getCollection()->first(),
            $this->category->comments()->simple()->simplePaginate()->getCollection()->first()
        );
    }

    private function checkCommentsBodies(Comment $comment, stdClass $primitiveComment)
    {
        $this->assertEquals($comment->body, $primitiveComment->body);

        return $this;
    }
}
