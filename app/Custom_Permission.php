<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Custom_Permission extends Model
{
    protected $table = "custom_permission";
    protected $fillable = ["name"];



}
