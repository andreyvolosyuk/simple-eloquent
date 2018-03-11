<?php


class HasOneTest extends TestCase
{
    public function test_model_should_return_its_child()
    {
        $category = Category::create([
            'name' => 'Test category'
        ]);
        Article::create([
            'title' => 'Test article',
            'category_id' => $category->id
        ]);

        $this->assertEquals(
            $category->article->title,
            Category::simple()->with('article')->first()->article->title
        );
    }
}
