<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'title',
    ];

    public function permission()
    {
        return $this->belongsToMany('App\Permission', 'role_permission','role_id', 'permission_id');
    }

    public function custom_permission()
    {
        return $this->belongsToMany('App\Custom_Permission','custom_permission_role','role_id','custom_permission_id');
    }

}
