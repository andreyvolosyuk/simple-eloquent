<?php

class User extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'users';

    public $timestamps = [];

    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }
}