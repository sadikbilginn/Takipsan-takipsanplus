<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'title',
        'phone',
        'email',
        'address',
        'logo',
        'latitude',
        'longitude',
        'created_user_id',
        'updated_user_id',
        'old_id',
        'record_id',
        'status',
        'consignment_close',
        'main_company_id',
    ];

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

    public function devices()
    {
        return $this->hasMany('App\Device');
    }

    public function users()
    {
        return $this->hasMany('App\User');
    }

    public function consignees()
    {
        return $this->belongsToMany('App\Consignee', 'company_consignee','company_id', 'consignee_id');
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
