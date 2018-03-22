<?php

use Illuminate\Support\Collection;

/**
 * Class GetTest
 */
class SelectTest extends TestCase
{
    /**
     * @var Article
     */
    private $article;

    protected function setUp()
    {
        parent::setUp();

        $this->article = Article::create([
            'title' => 'Test title'
        ]);
    }

    public function test_get_simple_returns_the_same_attributes_as_get()
    {
        $this->articlesTitlesAreEqual(Article::get()->first(), Article::simple()->get()->first());
    }

    public function test_all_simple_returns_the_same_attributes_as_all()
    {
        $this->articlesTitlesAreEqual(Article::all()->first(), Article::allSimple()->first());
    }

    public function test_first_simple_returns_the_same_attributes_as_first()
    {
        $this->articlesTitlesAreEqual(Article::first(), Article::simple()->first());
    }

    public function test_find_simple_returns_the_same_attributes_as_find()
    {
        $this->articlesTitlesAreEqual(Article::find(1), Article::simple()->find(1))
            ->articlesTitlesAreEqual(Article::find([1])->first(), Article::simple()->find([1])->first());
    }

    public function test_first_simple_or_fail_should_throw_an_exception_on_not_existed_model()
    {
        $this->articlesTitlesAreEqual(
            Article::where('id', 1)->firstOrFail(),
            Article::where('id', 1)->simple()->firstOrFail()
        )->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Article::where('id', 60)->simple()->firstOrFail();
    }

    public function test_find_simple_or_fail_should_throw_an_exception_on_not_existed_model()
    {
        $this->articlesTitlesAreEqual(Article::findOrFail([1])->first(), Article::simple()->findOrFail([1])->first())
            ->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        Article::simple()->findOrFail(60);
    }

    public function test_find_many_simple_should_return_full_or_empty_collection_depends_on_circumstances()
    {
        $this->articlesTitlesAreEqual(Article::findMany([1])->first(), Article::simple()->findMany([1])->first())
            ->assertEquals(0, Article::simple()->findMany(null)->count());
    }

    public function test_paginate_simple_should_behave_the_same_as_paginate()
    {
        $paginator = Article::paginate();
        $simplePpaginator = Article::simple()->paginate();

        $this->articlesTitlesAreEqual($paginator->items()[0], $simplePpaginator->items()[0])
            ->assertEquals($paginator->total(), $simplePpaginator->total());
    }

    public function test_simple_paginate_simple_should_behave_the_same_as_simple_paginate()
    {
        $paginator = Article::simplePaginate();
        $simplePpaginator = Article::simple()->simplePaginate();

        $this->articlesTitlesAreEqual(
            $paginator->items()[0],
            $simplePpaginator->items()[0]
        )->assertCount(count($paginator->items()), $simplePpaginator->items());
    }

    public function test_chunk_should_behave_the_same_as_chunk()
    {
        Article::chunk(10, function (Collection $articles) use (&$article) {
            $article = $articles->first();
        });
        Article::simple()->chunk(10, function (Collection $articles) use (&$primitiveArticle) {
            $primitiveArticle = $articles->first();
        });

        $this->articlesTitlesAreEqual($article, $primitiveArticle);
    }

    public function test_belongs_to_many_has_isSimple_method()
    {
        $user = User::create(['id' => 1]);

        $user->articles()->attach($this->article->id, ['user_id' => $user->id]);

        $this->assertEquals($user->id, $this->article->users()->first()->id);
    }
}
