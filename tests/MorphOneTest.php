<?php

class MorphOneTest extends TestCase
{
    /**
     * @var Article
     */
    private $article;

    /**
     * @var Like
     */
    private $like;

    protected function setUp(): void
    {
        parent::setUp();

        $this->article = Article::create([
            'id' => 20,
            'title' => 'Test article'
        ]);
        $this->like = Like::create([
            'id' => 40,
            'like_for_id' => $this->article->id,
            'like_for_type' => get_class($this->article)
        ]);

    }

    public function test_morph_one_returns_morphed_models()
    {
        $this->compareLikes($this->article->like, Article::simple()->with('like')->first()->like);
    }

    public function test_relational_method_morph_one_does_interact_with_simple()
    {
        $this->compareLikes(
            $this->article->like()->first(),
            $this->article->like()->simple()->first()
        )->compareLikes(
            $this->article->like()->find($this->like->id),
            $this->article->like()->simple()->find($this->like->id)
        )->compareLikes(
            $this->article->like()->findMany([$this->like->id])->first(),
            $this->article->like()->simple()->findMany([$this->like->id])->first()
        )->compareLikes(
            $this->article->like()->get()->first(),
            $this->article->like()->simple()->get()->first()
        )->compareLikes(
            $this->article->like()->paginate()->getCollection()->first(),
            $this->article->like()->simple()->paginate()->getCollection()->first()
        )->compareLikes(
            $this->article->like()->simplePaginate()->getCollection()->first(),
            $this->article->like()->simple()->simplePaginate()->getCollection()->first()
        );
    }

    private function compareLikes(Like $like, stdClass $primitiveLike)
    {
        $this->assertEquals(
            $like->id,
            $primitiveLike->id
        );

        return $this;
    }
}
