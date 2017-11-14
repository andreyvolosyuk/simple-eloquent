<?php

namespace Migrations;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class Article
 * @package Migrations
 */
class Article
{
    public static function run()
    {
        DB::schema()->create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id')->nullable(true);
            $table->string('title');
            $table->timestamps();
        });
    }
}