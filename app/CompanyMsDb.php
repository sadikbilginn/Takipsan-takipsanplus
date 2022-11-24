<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyMsDb extends Model
{
    protected $fillable = [
        'consignment_id',
        'order',
        'season',
        'description',
        'barcode',
        'sds_code',
        'upc',
        'story_desc',
        'price',
        'qty_req',
        'user_id',
        // 'created_user_id', 
        // 'updated_user_id'
    ];

    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    public function packages()
    {
        return $this->hasMany('App\Package');
    }

    public function items()
    {
        return $this->hasMany('App\Item');
    }

    public function created_user()
    {
        return $this->belongsTo('App\User');
    }
}
