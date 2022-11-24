<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable =  [
        'group_key',
        'required',
        'area_type',
        'title',
        'description',
        'key',
        'value',
        'locale',
        'sort'
    ];
}
