<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyDb extends Model
{
    protected $fillable = [
        'consignment_id',
        'order',
        'season',
        'product',
        'variant',
        'gtin',
        'article_number',
        'sds_code',
        'description',
        'user_id'
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
