<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Consignee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'logo',
        'phone',
        'address',
        'auth_name',
        'auth_phone',
        'created_user_id',
        'updated_user_id',
        'old_id',
        'record_id',
        'viewid',
        //'sayfa_gorunum',
        'status',
    ];

    public function consignment()
    {
        return $this->hasMany('App\Consignment');
    }

    public function companies()
    {
        return $this->belongsToMany('App\Company', 'company_consignee','consignee_id', 'company_id');
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
