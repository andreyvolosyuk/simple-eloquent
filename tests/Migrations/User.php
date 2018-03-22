<?php

namespace Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as DB;

class User
{
    public static function run()
    {
        DB::schema()->create('users', function (Blueprint $table) {
            $table->increments('id');
        });
        DB::schema()->create('article_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('article_id');
            $table->integer('user_id');
        });
    }
}
