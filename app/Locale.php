<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Locale extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'title',
        'title_glb',
        'abbr',
        'path',
        'default',
    ];
}
