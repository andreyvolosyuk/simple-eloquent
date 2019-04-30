<?php

use Illuminate\Database\Capsule\Manager as DB;
use Migrations\Migrator;

/**
 * Class TestCase
 */
class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        $this->setUpDatabase();
        Migrator::run();
    }
    
    /**
     * @return void
     */
    private function setUpDatabase()
    {
        $database = new DB;
        $database->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:'
        ]);
        $database->bootEloquent();
        $database->setAsGlobal();
    }

    /**
     * @param Article $article
     * @param stdClass $primitiveArticle
     * @return $this
     */
    protected function articlesTitlesAreEqual(Article $article, stdClass $primitiveArticle)
    {
        $this->assertEquals($article->title, $primitiveArticle->title);

        return $this;
    }

    /**
     * @param Comment $comment
     * @param stdClass $primitiveComment
     * @return $this
     */
    protected function checkCommentsBodies(Comment $comment, stdClass $primitiveComment)
    {
        $this->assertEquals($comment->body, $primitiveComment->body);

        return $this;
    }
}
