<?php


class BelongsToManyTest extends TestCase
{
    public function test_belongs_to_many_relation_should_return_related_models()
    {
        $article = Article::create([
            'id' => 5,
            'title' => 'Test title'
        ]);
        $category = Category::create([
            'id' => 10,
            'name' => 'Test article'
        ]);

        $this->assertCount(0, Article::with('multipleCategories')->firstSimple()->multipleCategories);
        $this->assertCount(0, Category::with('multipleArticles')->firstSimple()->multipleArticles);

        $article->multipleCategories()->attach($category->id);

        $this->assertEquals(
            $article->multipleCategories->first()->name,
            Article::with('multipleCategories')->firstSimple()->multipleCategories->first()->name
        );

        $this->assertEquals(
            $category->multipleArticles->first()->title,
            Category::with('multipleArticles')->firstSimple()->multipleArticles->first()->title
        );
    }
}
