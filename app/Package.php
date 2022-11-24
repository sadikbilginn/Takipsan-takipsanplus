<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'order_id',
        'consignment_id',
        'package_no',
        'size',
        'model',
        'order_model_id',
        'load_type',
        'box_type_id',
        'barcode',
        'order_size_id',
        'device_id',
        'status',
        'created_user_id',
        ];

    public function order(){

        return $this->belongsTo('App\Order');

    }

    public function consignment(){

        return $this->belongsTo('App\Consignment');

    }

    public function items(){

        return $this->hasMany('App\Item');

    }

    public function deleted_items(){

        return $this->hasMany('App\Item')->onlyTrashed();

    }

    public function devices(){

        return $this->hasMany('App\Device');

    }

    public function device(){

        return $this->belongsTo('App\Device');

    }

    public function created_user(){

        return $this->belongsTo('App\User', 'created_user_id');
        
    }

}
