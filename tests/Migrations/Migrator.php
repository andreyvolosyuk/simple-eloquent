<?php

namespace Migrations;

/**
 * Class Migrator
 */
class Migrator
{
    /**
     * @var array
     */
    private static $migrations = [
        Article::class,
        Category::class,
        ArticleCategory::class,
        Comment::class,
        Like::class,
        Likable::class,
        User::class
    ];

    /**
     * Runs migrations
     */
    public static function run()
    {
        foreach (self::$migrations as $migration) {
            $migration::run();
        }
    }
}
