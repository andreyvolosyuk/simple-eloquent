<?php

class HasManyTest extends TestCase
{
    public function test_model_should_return_its_child()
    {
        $category = Category::create([
            'name' => 'Test category'
        ]);

        $this->assertCount(0, Category::with('articles')->simple()->first()->articles);

        Article::create([
            'title' => 'Test article',
            'category_id' => $category->id
        ]);

        $this->assertEquals(
            $category->articles->first()->title,
            Category::with('articles')->simple()->first()->articles->first()->title
        );
    }
}