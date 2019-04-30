<?php


class HasOneThroughTest extends TestCase
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

        $this->category = Category::create([
            'id' => 20,
            'name' => 'Test category'
        ]);
        $this->article = Article::create([
            'id' => 50,
            'title' => 'Test article',
            'category_id' => $this->category->id
        ]);
    }

    public function test_has_one_through_relation_returns_model_through_another_one()
    {
        Comment::insert([
            [
                'id' => 99,
                'body' => 'first comment',
                'article_id' => $this->article->id
            ],
            [
                'id' => 100,
                'body' => 'second comment',
                'article_id' => $this->article->id
            ],
        ]);

        $this->checkCommentsBodies(
            $this->category->comment,
            Category::with('comment')->simple()->first()->comment
        );
    }

    public function test_relational_method_has_one_through_does_interact_with_simple()
    {
        $comment = Comment::create([
            'id' => 100,
            'body' => 'first comment',
            'article_id' => $this->article->id
        ]);

        $this->checkCommentsBodies(
            $this->category->comment()->first(),
            $this->category->comment()->simple()->first()
        )->checkCommentsBodies(
            $this->category->comment()->find($comment->id),
            $this->category->comment()->simple()->find($comment->id)
        )->checkCommentsBodies(
            $this->category->comment()->findMany([$comment->id])->first(),
            $this->category->comment()->simple()->findMany([$comment->id])->first()
        )->checkCommentsBodies(
            $this->category->comment()->get()->first(),
            $this->category->comment()->simple()->get()->first()
        )->checkCommentsBodies(
            $this->category->comment()->paginate()->getCollection()->first(),
            $this->category->comment()->simple()->paginate()->getCollection()->first()
        )->checkCommentsBodies(
            $this->category->comment()->simplePaginate()->getCollection()->first(),
            $this->category->comment()->simple()->simplePaginate()->getCollection()->first()
        );
    }
}