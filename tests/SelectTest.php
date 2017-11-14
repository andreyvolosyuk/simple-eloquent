<?php

/**
 * Class GetTest
 */
class SelectTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        Article::create([
            'title' => 'Test title'
        ]);
    }

    public function test_get_simple_returns_the_same_attributes_as_get()
    {
        $this->assertEquals(
            Article::get()->first()->title,
            Article::getSimple()->first()->title
        );
    }

    public function test_all_simple_returns_the_same_attributes_as_all()
    {
        $this->assertEquals(
            Article::all()->first()->title,
            Article::allSimple()->first()->title
        );
    }

    public function test_first_simple_returns_the_same_attributes_as_first()
    {
        $this->assertEquals(
            Article::first()->title,
            Article::firstSimple()->title
        );
    }

    public function test_find_simple_returns_the_same_attributes_as_find()
    {
        $this->assertEquals(
            Article::find(1)->title,
            Article::findSimple(1)->title
        );

        $this->assertEquals(
            Article::find([1])->first()->title,
            Article::findSimple([1])->first()->title
        );
    }

    public function test_first_simple_or_fail_should_throw_an_exception_on_not_existed_model()
    {
        $this->assertEquals(
            Article::where('id', 1)->firstOrFail()->title,
            Article::where('id', 1)->firstSimpleOrFail()->title
        );

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        Article::where('id', 60)->firstSimpleOrFail();
    }

    public function test_find_simple_or_fail_should_throw_an_exception_on_not_existed_model()
    {
        $this->assertEquals(
            Article::findOrFail([1])->first()->title,
            Article::findSimpleOrFail([1])->first()->title
        );

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        Article::findSimpleOrFail(60);
    }

    public function test_find_many_simple_should_return_full_or_empty_collection_depends_on_circumstances()
    {
        $this->assertEquals(
            Article::findMany([1])->first()->title,
            Article::findManySimple([1])->first()->title
        );

        $this->assertEquals(0, Article::findManySimple(null)->count());
    }

    public function test_paginate_simple_should_behave_the_same_as_paginate()
    {
        $paginator = Article::paginate();
        $simplePpaginator = Article::paginateSimple();

        $this->assertEquals($paginator->total(), $simplePpaginator->total());
        $this->assertEquals(
            $paginator->items()[0]->title,
            $simplePpaginator->items()[0]->title
        );
    }
}