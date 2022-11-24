<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class TsukaClDetails extends Model
{
    // use SoftDeletes;

    protected $fillable = [
        'epc',
        'po_number',
        'size',
        'color',
        'model_no',
        'brand',
        'list_id'
    ];

    // protected $table ="tsuka_counting_list";

    // public function company()
    // {
    //     return $this->belongsTo('App\Company');
    // }

    // public function order()
    // {
    //     return $this->belongsTo('App\Order');
    // }

    // public function packages()
    // {
    //     return $this->hasMany('App\Package');
    // }

    // public function deleted_packages()
    // {
    //     return $this->hasMany('App\Package')->onlyTrashed();
    // }

    // public function items()
    // {
    //     return $this->hasMany('App\Item');
    // }

    // public function consignee()
    // {
    //     return $this->belongsTo('App\Consignee');
    // }

    // public function created_user()
    // {
    //     return $this->belongsTo('App\User', 'created_user_id');
    // }

    // public function updated_user()
    // {
    //     return $this->belongsTo('App\User', 'updated_user_id');
    // }
}
