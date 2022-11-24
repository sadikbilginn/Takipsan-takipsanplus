<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_code',
        'consignee_id',
        'season',
        'po_no',
        'name',
        'item_count',
        'record_id',
        'created_user_id',
        'updated_user_id',
        'status',
    ];

    public function consignee()
    {
        return $this->belongsTo('App\Consignee');
    }

    public function consignments()
    {
        return $this->hasMany('App\Consignment');
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
        return $this->belongsTo('App\User', 'created_user_id');
    }

    public function updated_user()
    {
        return $this->belongsTo('App\User', 'updated_user_id');
    }
}
