<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CountryListModel extends Model
{
    public $country =  [];
    protected $fillable = [
      'country_list_name',
    ];
}
