<?php

class BelongsToTest extends TestCase
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
            'name' => 'Test category'
        ]);
        $this->article = Article::create([
            'title' => 'Test article',
            'category_id' => $this->category->id
        ]);
    }

    public function test_child_model_should_return_its_parent()
    {
        $this->compareCategoryNames(
            $this->article->category,
            Article::simple()->with('category')->first()->category
        );

        $this->assertCount(0, Article::where('id', 60)->with('category')->getSimple());
        $this->assertCount(0, Article::simple()->where('id', 60)->with('category')->get());
        $this->assertCount(0, Article::where('id', 60)->simple()->with('category')->get());
    }

    public function test_relational_method_belongs_to_does_interact_with_simple()
    {
        $this->compareCategoryNames(
            $this->article->category()->first(),
            $this->article->category()->simple()->first()
        )->compareCategoryNames(
            $this->article->category()->find($this->article->id),
            $this->article->category()->simple()->find($this->article->id)
        )->compareCategoryNames(
            $this->article->category()->findMany([$this->article->id])->first(),
            $this->article->category()->simple()->findMany([$this->article->id])->first()
        )->compareCategoryNames(
            $this->article->category()->get()->first(),
            $this->article->category()->simple()->get()->first()
        )->compareCategoryNames(
            $this->article->category()->paginate()->getCollection()->first(),
            $this->article->category()->simple()->paginate()->getCollection()->first()
        )->compareCategoryNames(
            $this->article->category()->simplePaginate()->getCollection()->first(),
            $this->article->category()->simple()->simplePaginate()->getCollection()->first()
        );
    }

    private function compareCategoryNames(Category $category, stdClass $primitiveCategory)
    {
        $this->assertEquals($category->name, $primitiveCategory->name);

        return $this;
    }
}
