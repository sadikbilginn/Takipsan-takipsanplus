<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;
   public $itemDetails =  [];
    protected $fillable = [
        'company_id',
        'order_id',
        'consignment_id',
        'package_id',
        'epc',
        'size',
        'device_id',
        'created_user_id',
    ];

    public function order()
    {
        return $this->belongsTo('App\Order');
    }

    public function consignment()
    {
        return $this->belongsTo('App\Consignment');
    }

    public function package()
    {
        return $this->belongsTo('App\Package');
    }

    public function devices()
    {
        return $this->hasMany('App\Device');
    }

    public function device()
    {
        return $this->belongsTo('App\Device');
    }

    public function created_user()
    {
        return $this->belongsTo('App\User', 'created_user_id');
    }

}
