<?php

namespace App\Helpers;
use Illuminate\Support\Str;

trait OptionTrait{
    
    /**
    * Array verileri içerisindeki verileri tarayarak sadece
    * numeric verileri döner.
    *
    * @param  array  $array
    * @return array $new_result
    */
    public function arrayOnlyNumeric($array = []):array{

        $new_result = [];
        
        foreach ($array as  $value){

            if(is_numeric($value))
                array_push($new_result, $value);

        }

        return $new_result;

    }
    
    /**
    * Data içerisindeki verileri tarayarak parent_id
    * durumuna göre tree görünümüne dönüştürür.
    *
    * @param  array  $data
    * @param  integer  $parent_id
    * @return array $return
    */
    public function menuSortingParseJsonArray($data, $parent_id = 0):array{
        
        $return = [];

        foreach ($data as $subArray) {

            $returnSubSubArray = [];
            if (isset($subArray->children)) {
                $returnSubSubArray = $this->menuSortingParseJsonArray($subArray->children, $subArray->id);
            }

            $return[] = ['id' => $subArray->id, 'parent_id' => $parent_id];
            $return = array_merge($return, $returnSubSubArray);

        }

        return $return;

    }
    
    /**
    * Menüye verilen izinler ile user izinlerini kontrol eder,
    * user erişim izni varsa true döner.
    *
    * @param  array  $menu_roles
    * @param  array  $user_roles
    * @return boolean $return
    */
    function menuRoleCheck($menu_roles, $user_roles) {

        $return = false;
        $menu_roles_ids = [];
        foreach ($menu_roles as $key => $value){
            $menu_roles_ids[] = $value->id;
        }
        
        $user_roles_ids = [];
        foreach ($user_roles as $key => $value){
            $user_roles_ids[] = $value->id;
        }
        
        $x = array_intersect($menu_roles_ids, $user_roles_ids);
        
        if(count($x) > 0){
            $return = true;
        }
        
        return $return;

    }
    
    /**
    * Veriler değer üzerinden request action'dan
    * istenilen değer döner.
    *
    * @param  string  $data
    * @return string
    */
    public function getAction($data = ''):string{
        
        $action = app('request')->route()->getAction();
        $controller = class_basename($action['controller']);
        list($controller, $action) = explode('@', $controller);
        
        switch ($data){
            case 'controller' : $return = $controller; break;
            case 'action' : $return = $action; break;
            default : $return = $controller;
        }
        
        return $return;

    }
    
    /**
    *
    * Otomatik sipariş kodu oluşturur.
    * @param int $limit
    * @param string $chars
    * @return string
    */
    public function autoGenerateOrderCode($limit = 6, $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'){
        
        $code = '';
        for ($i = 0; $i < $limit; $i++) {
            $code .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        
        if(auth()->check() && auth()->user()->name != ''){
            $code = substr(mb_strtoupper(Str::slug(auth()->user()->name)), 0, 3). '-' . $code;
        }else{
            $code =  'SYS-' . $code;
        }
        
        $customer = \Illuminate\Support\Facades\DB::table('orders')->where('order_code', $code)->count();
        if($customer > 0){
            // echo ('limit => '.$limit.' - chars => '.$chars);
            // exit();
            $this->autoGenerateOrderCode($limit, $chars);
        }
        
        return $code;

    }
    
    /**
    *
    * Po numarası varsa copy olarak sonuna ekleme yapar
    * @param string $po_no
    * @return string
    */
    public function autoGeneratePoCode($po_no){
        
        $i = 0;
        while(true) {
            if($i) {
                $new_po_no = str_slug($po_no . '-' . $i);
            } else {
                $new_po_no = str_slug($po_no);
            }
            
            $orders = \Illuminate\Support\Facades\DB::table('orders')->where('po_no', $new_po_no)->count();
            if(!$orders) {
                return $new_po_no;
            } else {
                $i++;
            }
        }

    }
    
    /**
    *
    * Otomatik po no oluşturur.
    * @param integer $order_id
    * @return string
    */
    public function autoGeneratePoNo($order_id,$country_code,$companyname):string{
        
        $poNo = false;
        $order = \App\Order::find($order_id);
        $no = 0;
        $country = null;
        $name = null;
        //$name = $order->po_no.'/'. $order->season . '/'.  $country_code;
        // name ornek : po_no-1/ulke_kod/H&M - po_no-1/Zara
        if ($country_code != null){
            //$name =  $order->po_no.'/'.$country_code;
            $cons = \App\Consignment::where('name', 'like', '%' . $order->po_no . '%')
                ->where('country_code', $country_code)
                ->orderBy('created_at', 'desc')
                ->first();

        }else{
            
            $cons = \App\Consignment::where('name', 'like', '%' . $order->po_no . '%')
                ->where('status', 1)
                ->orderBy('created_at', 'desc')
                ->first();
                
            if ($cons){
                
                $bol = explode('-', $cons->name);
                if ($bol[0] != $order->po_no){
                    
                    $consNew = \App\Consignment::where('name', 'like', '%' . $order->po_no . '%')
                        ->where('status', 1)
                        ->where('id', '!=', $cons->id)
                        ->orderBy('created_at', 'desc')
                        ->first();
                }

            }

        }
            
        if (isset($consNew)){
            $name = $consNew->name;
        }elseif (!isset($consNew) && isset($cons)){
            $name = $cons->name;
        }
            
        if (isset($name) && $name != null){
            // echo $name.'_name<br>';
            // exit();
            $ex = explode('-', $name);
            if (is_array($ex)){
                $poNo = end($ex);
                //echo $poNo;
                $companyEx = explode('/',$poNo);
                // ulke kodu için parcalanıp sayı 3 se ulke kodu alınıcak
                //echo count($companyEx);
                if (count($companyEx) == 3 ){
                    $country = $companyEx[1];
                }
                
                $companyVal = end($companyEx);

            }
            //exit();
        }
            
        // aynı isimli olanlarda sondaki no degerini markaya bakarak arttır. 
        // echo $companyVal.'<br>';
        // echo $companyname.'<br>';
        // exit();
        if (isset($companyVal)){
            
            if ($country != null && $country == $country_code){
                $no = intval($poNo) + intval(1);
            }elseif ($country == null && $companyVal == $companyname){
                $no = intval($poNo) + intval(1);
            }else{
                $no = 1;
            }

        }else{

            $no = 1;

        }
            
        // ülke kodları farklı olanlarda no degerini 1 e esitle
        if ($country_code != null && $country_code != isset($companyEx[1])){
            $no = 1;
        }
        // echo $no.'<br>';
        // echo $country_code.'<br>'; 
        // echo isset($companyVal).'<br>';
        // echo $companyname;
        // exit();
        if ($country_code == null) {
            //return $order->po_no.'/'. $order->name . '/'. $no. '/'.$companyname;
            return $order->po_no.'-'.$no.'/'.$companyname;
        }
        //return $order->po_no.'/'. $order->season . '/'.  $country_code . '-' . $no. '/'.$companyname;
        // echo $order->po_no.'-'. $no.'/'.$country_code.'/'.$companyname;
        // exit();
        return $order->po_no.'-'. $no.'/'.$country_code.'/'.$companyname;

    }
        
    /**
    *
    * Sİparişten sevk edilen firma id getirir oluşturur.
    * @param integer $id
    * @return integer
    */
    public function getConsigneeId($id){
        
        $consignee_id = 0;
        $order = \App\Order::find($id);
        if($order){
            $consignee_id = $order->consignee_id != 0 ? $order->consignee_id : 0;
        }
        
        return $consignee_id;

    }
        
    /**
    * Servis bağlantı fonksiyonu
    *
    * @method      İstek tipi
    * @request     İstekte bulunulan fonksiyon adı
    * @parameters  Parametre bilgileri
    * @return_type Result tipi True == (Array) / False == (Object)
    *
    */
    public function get_service($method, $request, $parameters = [], $return_type = false) {
        
        $result = false;
        $param = '';
        
        switch ($method){

            case 'get' :

                if($parameters && count($parameters) > 0){
                    $i = 0;
                    foreach ($parameters as $key => $value){
                        if($i == 0){
                            $param .= '?' . $key . '=' . $value;
                        }else{
                            $param .= '&' . $key . '=' . $value;
                        }
                        $i = $i + 1;
                    }
                }
                
                $ch = curl_init($request . $param);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 600);
                $return = curl_exec($ch);
                
                if(!curl_errno($ch)){
                    $info = curl_getinfo($ch);
                    session()->flash('curl_info', 'İsteğin ' . $info['url'] . ' adresine gönderilmesi ' . $info['total_time'] . ' saniye sürdü');
                }
                
                curl_close($ch);
                
                $result = json_decode($return , $return_type);

            break;
            
            case 'post' :
                
                $ch = curl_init($request);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 600);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen(json_encode($parameters)))
                );
                
                $result = curl_exec($ch);
                
                if(!curl_errno($ch)){
                    $info = curl_getinfo($ch);
                    session()->flash('curl_info', 'İsteğin ' . $info['url'] . ' adresine gönderilmesi ' . $info['total_time'] . ' saniye sürdü');
                }
                
                curl_close($ch);
                
                $result = json_decode($result);
                
            break;
            
            case 'put' :
                
                $ch = curl_init($request);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 600);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen(json_encode($parameters)))
                );
                
                $result = curl_exec($ch);
                
                if(!curl_errno($ch)){
                    $info = curl_getinfo($ch);
                    session()->flash('curl_info', 'İsteğin ' . $info['url'] . ' adresine gönderilmesi ' . $info['total_time'] . ' saniye sürdü');
                }
                
                curl_close($ch);
                
                $result = json_decode($result);
                
            break;
            
            default:
            
                if($parameters && count($parameters) > 0){
                    $i = 0;
                    foreach ($parameters as $key => $value){
                        if($i == 0){
                            $param .= '?' . $key . '=' . $value;
                        }else{
                            $param .= '&' . $key . '=' . $value;
                        }
                        $i = $i + 1;
                    }
                }
                
                $return = file_get_contents($request . $param );
                $result = json_decode($return , $return_type);

        }
            
        return $result;

    }
        
    /**
    * Kullanıcıya verilen roller ile user istenilen rol bilgisini
    *  kontrol eder, user erişim izni varsa true döner.
    *
    * @param  integer  $roleId
    * @return boolean $return
    */
    public function roleCheck($roleId):bool{
        
        $return = false;
        $roleIds =  session('user_roles')->pluck('id')->toArray();
        if(in_array($roleId, $roleIds)){
            $return = true;
        }
    
        
        return $return;

    }
    
    /*
    public function customPermissionCheck($permissionId):bool{
        
        $return = false;
        if (in_array($permissionId, session('user_custom_permission_ids'))) {
            $return = true;
        }
        
        if(auth()->user()->is_admin){
            $return = true;
        }
        
        return $return;

    }
*/
    

}