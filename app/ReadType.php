<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReadType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_en',
        'reader',
        'reader_mode',
        'estimated_population',
        'search_mode',
        'session',
        'string_set',
        'created_user_id',
        'updated_user_id',
        'status',
    ];

    public function devices()
    {
        return $this->hasMany('App\Device');
    }
}
