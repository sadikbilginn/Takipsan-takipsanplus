<?php
/**
 * Created by Mehmet Karabulut
 * Date :  9.04.2020
 * Time :  13:47
 */

namespace App\Helpers;

trait NotificationTrait
{
    public function createNotification($message, $param = [])
    {
        
        $tr = trans($message, $param,'tr');
        $en = trans($message, $param,'en');
        
        $companyId = $param['company_id'];
        $userId = $param['user_id'];
        

        return \App\Notification::create([
            "company_id"        =>  $companyId,
            "message"           =>  $tr,
            "message_en"        =>  $en,
            "created_user_id"   =>  auth()->check() ? $userId : 0,
        ]);
    }
}
