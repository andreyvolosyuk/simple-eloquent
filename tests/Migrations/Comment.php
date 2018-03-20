<?php

namespace Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as DB;

class Comment
{
    public static function run()
    {
        DB::schema()->create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->unsignedInteger('article_id');
            $table->string('body');
        });
    }
}