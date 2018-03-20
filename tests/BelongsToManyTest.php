<?php

use Illuminate\Support\Collection;

class BelongsToManyTest extends TestCase
{
    /**
     * @var Article
     */
    private $article;

    /**
     * @var Category
     */
    private $category;

    protected function setUp()
    {
        parent::setUp();

        $this->article = Article::create([
            'id' => 5,
            'title' => 'Test title'
        ]);

        $this->category = Category::create([
            'id' => 10,
            'name' => 'Test article'
        ]);
    }


    public function test_belongs_to_many_relation_should_return_related_models()
    {
        $this->assertCount(0, Article::with('multipleCategories')->simple()->first()->multipleCategories);
        $this->assertCount(0, Category::with('multipleArticles')->simple()->first()->multipleArticles);

        $this->article->multipleCategories()->attach($this->category->id);

        $this->assertEquals(
            $this->article->multipleCategories->first()->name,
            Article::with('multipleCategories')->simple()->first()->multipleCategories->first()->name
        );

        $this->articlesTitlesAreEqual(
            $this->category->multipleArticles->first(),
            Category::with('multipleArticles')->simple()->first()->multipleArticles->first()
        );
    }

    public function test_relational_method_with_belongs_to_many_does_interact_with_simple()
    {
        $this->article->multipleCategories()->attach($this->category->id);

        $this->articlesTitlesAreEqual(
            $this->category->multipleArticles()->first(),
            $this->category->multipleArticles()->simple()->first()
        )->articlesTitlesAreEqual(
            $this->category->multipleArticles()->find($this->article->id),
            $this->category->multipleArticles()->simple()->find($this->article->id)
        )->articlesTitlesAreEqual(
            $this->category->multipleArticles()->findMany([$this->article->id])->first(),
            $this->category->multipleArticles()->simple()->findMany([$this->article->id])->first()
        )->articlesTitlesAreEqual(
            $this->category->multipleArticles()->get()->first(),
            $this->category->multipleArticles()->simple()->get()->first()
        )->articlesTitlesAreEqual(
            $this->category->multipleArticles()->paginate()->getCollection()->first(),
            $this->category->multipleArticles()->simple()->paginate()->getCollection()->first()
        )->articlesTitlesAreEqual(
            $this->category->multipleArticles()->simplePaginate()->getCollection()->first(),
            $this->category->multipleArticles()->simple()->simplePaginate()->getCollection()->first()
        );

        $this->category->multipleArticles()->chunk(10, function (Collection $articles) use (&$article) {
            $article = $articles->first();
        });
        $this->category->multipleArticles()->simple()->chunk(10, function (Collection $articles) use (&$primitiveArticle) {
            $primitiveArticle = $articles->first();
        });

        $this->articlesTitlesAreEqual($article, $primitiveArticle);
    }
}
