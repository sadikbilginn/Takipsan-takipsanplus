<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogActivity extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'model',
        'subject',
        'subject_en',
        'url',
        'controller',
        'action',
        'method',
        'ip',
        'agent',
        'record_id',
        'user_id',
        'created_at'
    ];

    public function created_user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
