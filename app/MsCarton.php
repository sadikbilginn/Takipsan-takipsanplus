<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MsCarton extends Model{

    protected $fillable = [
        'consignment_id',
        'upc',
        'cartonID',
        'series',
        'colour',
        'singles',
        'barcode'
    ];

    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    public function packages()
    {
        return $this->hasMany('App\Package');
    }

}
