<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class License extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'license_id',
        'company_id',
        'device_id',
        'status',
        'start_at',
        'finish_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

}
