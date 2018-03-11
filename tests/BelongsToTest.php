<?php


class BelongsToTest extends TestCase
{
    public function test_child_model_should_return_its_parent()
    {
        $category = Category::create([
            'name' => 'Test category'
        ]);
        $article = Article::create([
            'title' => 'Test article',
            'category_id' => $category->id
        ]);

        $this->assertEquals(
            $article->category->name,
            Article::simple()->with('category')->first()->category->name
        );

        $this->assertCount(0, Article::where('id', 60)->with('category')->getSimple());
        $this->assertCount(0, Article::simple()->where('id', 60)->with('category')->get());
        $this->assertCount(0, Article::where('id', 60)->simple()->with('category')->get());
    }
}