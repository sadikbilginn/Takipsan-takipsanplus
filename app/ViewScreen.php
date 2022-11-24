<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ViewScreen extends Model{

    protected $fillable = [
        'name',
        'status',
        'reading'
    ];
    
}
