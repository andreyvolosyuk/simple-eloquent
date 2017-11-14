<?php

use Volosyuk\SimpleEloquent\SimpleEloquent;

class Likable extends \Illuminate\Database\Eloquent\Model
{
    use SimpleEloquent;

    protected $table = 'likable';

    protected $guarded = ['id'];
}
