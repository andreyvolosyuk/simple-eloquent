<?php

class HasManyTest extends TestCase
{
    /**
     * @var Category
     */
    private $category;

    protected function setUp()
    {
        parent::setUp();

        $this->category = Category::create([
            'id' => 20,
            'name' => 'Test category'
        ]);
    }

    public function test_model_should_return_its_child()
    {
        $this->assertCount(0, Category::with('articles')->simple()->first()->articles);

        Article::create([
            'id' => 50,
            'title' => 'Test article',
            'category_id' => $this->category->id
        ]);

        $this->articlesTitlesAreEqual(
            $this->category->articles->first(),
            Category::with('articles')->simple()->first()->articles->first()
        );
    }

    public function test_relational_method_has_many_does_interact_with_simple()
    {
        $article = Article::create([
            'id' => 50,
            'title' => 'Test article',
            'category_id' => $this->category->id
        ]);

        $this->articlesTitlesAreEqual(
            $this->category->articles()->first(),
            $this->category->articles()->simple()->first()
        )->articlesTitlesAreEqual(
            $this->category->articles()->find($article->id),
            $this->category->articles()->simple()->find($article->id)
        )->articlesTitlesAreEqual(
            $this->category->articles()->findMany([$article->id])->first(),
            $this->category->articles()->simple()->findMany([$article->id])->first()
        )->articlesTitlesAreEqual(
            $this->category->articles()->get()->first(),
            $this->category->articles()->simple()->get()->first()
        )->articlesTitlesAreEqual(
            $this->category->articles()->paginate()->getCollection()->first(),
            $this->category->articles()->simple()->paginate()->getCollection()->first()
        )->articlesTitlesAreEqual(
            $this->category->articles()->simplePaginate()->getCollection()->first(),
            $this->category->articles()->simple()->simplePaginate()->getCollection()->first()
        );
    }
}