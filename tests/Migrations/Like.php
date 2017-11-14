<?php

namespace Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as DB;

class Like
{
    public static function run()
    {
        DB::schema()->create('likes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('like_for_id');
            $table->string('like_for_type');
        });
    }
}
