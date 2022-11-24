<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MsUpcCarton extends Model{

    protected $fillable = [
        'consignment_id',
        'user_id',
        'upc',
        'size',
        'po_upc_quantity',
        'descriptions'
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
