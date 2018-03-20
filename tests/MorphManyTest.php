<?php

use Illuminate\Database\Capsule\Manager as DB;

class MorphManyTest extends TestCase
{
    /**
     * @var Article
     */
    private $article;

    protected function setUp()
    {
        parent::setUp();

        $this->article = Article::create(['title' => 'First test article']);
    }

    public function test_morph_many_returns_related_models()
    {
        $this->assertCount(0, Article::simple()->with('likesMany')->first()->likesMany);

        DB::table('likable')->insert([
            ['likable_id' => $this->article->id, 'likable_type' => Article::class, 'like_id' => 222],
            ['likable_id' => $this->article->id, 'likable_type' => Article::class, 'like_id' => 222],
        ]);

        $simpleArticle = Article::simple()->with('likesMany')->first();

        $this->compareLikables(
            $this->article->likesMany->first(),
            $simpleArticle->likesMany->first()
        )->assertEquals(
            $this->article->likesMany->count(),
            $simpleArticle->likesMany->count()
        );
    }

    public function test_relational_method_morph_many_does_interact_with_simple()
    {
        $likable = Likable::create(['likable_id' => $this->article->id, 'likable_type' => Article::class, 'like_id' => 222]);

        $this->compareLikables(
            $this->article->likesMany()->first(),
            $this->article->likesMany()->simple()->first()
        )->compareLikables(
            $this->article->likesMany()->find($likable->id),
            $this->article->likesMany()->simple()->find($likable->id)
        )->compareLikables(
            $this->article->likesMany()->findMany([$likable->id])->first(),
            $this->article->likesMany()->simple()->findMany([$likable->id])->first()
        )->compareLikables(
            $this->article->likesMany()->get()->first(),
            $this->article->likesMany()->simple()->get()->first()
        )->compareLikables(
            $this->article->likesMany()->paginate()->getCollection()->first(),
            $this->article->likesMany()->simple()->paginate()->getCollection()->first()
        )->compareLikables(
            $this->article->likesMany()->simplePaginate()->getCollection()->first(),
            $this->article->likesMany()->simple()->simplePaginate()->getCollection()->first()
        );
    }

    private function compareLikables(Likable $likable, stdClass $primitiveLikable)
    {
        $this->assertEquals(
            $likable->id,
            $primitiveLikable->id
        );

        return $this;
    }
}
