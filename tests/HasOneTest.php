<?php


class HasOneTest extends TestCase
{
    /**
     * @var Category
     */
    private $category;

    /**
     * @var Article
     */
    private $article;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'id' => 50,
            'name' => 'Test category'
        ]);

        $this->article = Article::create([
            'id' => 20,
            'title' => 'Test article',
            'category_id' => $this->category->id
        ]);
    }

    public function test_model_should_return_its_child()
    {
        $this->articlesTitlesAreEqual(
            $this->category->article,
            Category::simple()->with('article')->first()->article
        );
    }

    public function test_relational_method_has_one_does_interact_with_simple()
    {
        $this->articlesTitlesAreEqual(
            $this->category->article()->first(),
            $this->category->article()->simple()->first()
        )->articlesTitlesAreEqual(
            $this->category->articles()->find($this->article->id),
            $this->category->articles()->simple()->find($this->article->id)
        )->articlesTitlesAreEqual(
            $this->category->articles()->findMany([$this->article->id])->first(),
            $this->category->articles()->simple()->findMany([$this->article->id])->first()
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
