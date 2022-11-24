<?php

namespace App;

use App\Notifications\CreateUserNotification;
use App\Notifications\CustomResetPasswordNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facedes\Notification;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id', 'name', 'username', 'email', 'password', 'api_token', 'is_admin', 'status', 'created_user_id', 'updated_user_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
         'remember_token', 'api_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function permissions()
    {
        return $this->belongsToMany('App\Permission', 'user_permission','user_id', 'permission_id');
    }

    public function roles()
    {
        return $this->belongsToMany('App\Role', 'user_role','user_id', 'role_id');
    }

    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    public function custom_permission()
    {
        return $this->belongsToMany('App\Custom_Permission','custom_permission_user','user_id','custom_permission_id');
    }

    public function createUserNotify($user)
    {
        $this->notify(new CreateUserNotification($user));
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }

    public function getEmailForPasswordReset() {
        return $this->username;
    }

}
