<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReadFile extends Model{

    protected $fillable = [
        'order_id',
        'company_id',
        'consignee_id',
        'name',
        'file_path',
        'created_user_id',
        'updated_user_id'
    ];
    
}
