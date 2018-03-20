<?php

namespace Migrations;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\Blueprint;

class Likable
{
    public static function run()
    {
        DB::schema()->create('likable', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('like_id');
            $table->unsignedInteger('likable_id');
            $table->timestamps();
            $table->string('likable_type');
        });
    }
}