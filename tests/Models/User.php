<?php

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    public $timestamps = [];

    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }
}