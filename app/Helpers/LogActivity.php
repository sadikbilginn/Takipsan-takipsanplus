<?php
/**
 * Created by Mehmet Karabulut
 * Date :  3.09.2019
 * Time :  13:47
 */

namespace App\Helpers;

trait LogActivity
{
    public function createLog($model, $subject, $replace = [], $record_id = 0)
    {
        if(request()->route() == null){
            $controller = "Command";
            $action     = "Command";
        }else{
            $controller = class_basename(request()->route()->action['controller']);
            list($controller, $action) = explode('@', $controller);
        }

        return \App\LogActivity::create([
            "model"         =>  $model,
            "subject"       =>  trans($subject, $replace, 'tr'),
            "subject_en"    =>  trans($subject, $replace, 'en'),
            "url"           =>  request()->fullUrl(),
            "controller"    =>  $controller,
            "action"        =>  $action,
            "method"        =>  request()->method(),
            "ip"            =>  request()->ip(),
            "agent"         =>  request()->userAgent(),
            "record_id"     =>  $record_id,
            "user_id"       =>  auth()->check() ? auth()->user()->id : 0,
            "created_at"    => date('Y-m-d H:i:s')
        ]);
    }
}
