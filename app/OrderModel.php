<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderModel extends Model
{

    protected $fillable = [
        'company_id',
        'order_id',
        'consignment_id',
        'name',
        'quantity',
        'record_id',
        'created_user_id',
        'updated_user_id',
    ];
}
