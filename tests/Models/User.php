<?php

class User extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'users';

    public $timestamps = [];

    protected $guarded = [];

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_users');
    }
}