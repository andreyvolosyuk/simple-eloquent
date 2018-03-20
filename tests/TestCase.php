<?php

use Illuminate\Database\Capsule\Manager as DB;

/**
 * Class TestCase
 */
class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        $this->setUpDatabase();
        \Migrations\Migrator::run();
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
}
