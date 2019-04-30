<?php

class MorphToTest extends TestCase
{
    /**
     * @var Article
     */
    private $article;

    /**
     * @var Like
     */
    private $like;

    protected function setUp()
    {
        parent::setUp();

        $this->article = Article::create([
            'id' => 50,
            'title' => 'Test article'
        ]);
        $this->like= Like::create([
            'id' => 30,
            'like_for_id' => $this->article->id,
            'like_for_type' => Article::class
        ]);
    }

    public function test_morph_to_relation_returns_related_models()
    {
        $this->articlesTitlesAreEqual(
            $this->like->likable,
            Like::with('likable')->simple()->first()->like_for
        );
    }

    public function test_relational_method_morph_to_does_interact_with_simple()
    {
        $this->articlesTitlesAreEqual(
            $this->like->likable()->first(),
            $this->like->likable()->simple()->first()
        )->articlesTitlesAreEqual(
            $this->like->likable()->find($this->article->id),
            $this->like->likable()->simple()->find($this->article->id)
        )->articlesTitlesAreEqual(
            $this->like->likable()->findMany([$this->article->id])->first(),
            $this->like->likable()->simple()->findMany([$this->article->id])->first()
        )->articlesTitlesAreEqual(
            $this->like->likable()->get()->first(),
            $this->like->likable()->simple()->get()->first()
        )->articlesTitlesAreEqual(
            $this->like->likable()->paginate()->getCollection()->first(),
            $this->like->likable()->simple()->paginate()->getCollection()->first()
        )->articlesTitlesAreEqual(
            $this->like->likable()->simplePaginate()->getCollection()->first(),
            $this->like->likable()->simple()->simplePaginate()->getCollection()->first()
        );
    }
}
