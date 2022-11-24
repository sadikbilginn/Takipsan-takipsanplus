<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{

    protected $fillable = [
        'company_id',
        'message',
        'message_en',
        'read_mobile',
        'read_web',
        'created_user_id',
        'updated_user_id',
    ];

    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    public function created_user()
    {
        return $this->belongsTo('App\User', 'created_user_id');
    }
}
