<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MsCartonEpc extends Model{

    protected $fillable = [
        'consigment_id',
        'barcode',
        'epc',
        'upc',
    ];

    public function consignment()
    {
        return $this->belongsTo('App\Consignment');
    }

}
