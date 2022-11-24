<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class XmlFileRepo extends Model{

    protected $fillable = [
        'country',
        'dep',
        'poNumber',
        'stroke',
        'incotermDate',
        'packType',
        'supplierDesc',
        'ratioPackIndicator',
        'cartonID',
        'series',
        'colour',
        'singles',
        'grossWeight',
        'barcode',
        'cartonUPCType',
        'goh',
        'srp',
        'crc',
        'upc',
        'size',
        'mixedQuantity',
        'poUpcQuantity',
        'status',
    ];

    public function txt_file_repos()
    {
        return $this->belongsTo('App\TxtFileRepo');
    }
    
}
