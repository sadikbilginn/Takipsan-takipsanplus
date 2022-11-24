<?php

namespace App\Http\Middleware;

use App\Helpers\OptionTrait;
use Closure;

class Permission
{
    use OptionTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $retVal = false;
       // $retVal2 = false;

        /*
         * Kullanıcı permission ve role bilgileri çekiliyor.
         */
        if(!session()->has('user_roles')){
            return redirect()->route('logout');
        }
      //  $permissions    = session('user_permissions');
        $roles          = session('user_roles');
        /*
         * Sayfa bilgileri çekiliyor.
         */
        $controller = $this->getAction('controller');
        $action     = $this->getAction('action');
        $method     = $request->method();

        /*
         * Rol kontrolleri yapılıyor
         */
        foreach ($roles as $key => $value){

            foreach ($value->permission as $key2 => $value2){
            
                $methods = explode(',', $value2->method);

                if($controller == $value2->controller){
                    if($action == $value2->action){
                     
                        if(in_array($method, $methods)){
                            $retVal = true;
                        }else{
                            $retVal = false;
                        }
                    }
                }
            }
        }
 
        /*
         * Permission kontrolleri yapılıyor
         */
        /*
        08.11.2022 User a özel izin yapısı kaldırıldı. Artık sadece role bazlı izin olacağı için burası yorum satırına alındı.
        foreach ($permissions as $key => $value){

            $methods = explode(',', $value->method);

             //echo 'permis__'.$value->controller.'--'.$controller.'__controller<br>';
             //echo 'permisAction__'.$value->action.'--'.$action.'__action<br>';
            
            if($controller == $value->controller){
                //echo 'sad';
                
                if($action == $value->action){
                    
                    if(in_array($method, $methods)){
                        $retVal2 = true;
                    }else{
                        $retVal2 = false;
                    }

                }

            }
        }
        */
        

        //exit();

        /*
         * HomeController için özel izin veriliyor
         */
        if($controller == 'HomeController'){
            $retVal = true;
           // $retVal2 = true;
        }

        /*
         * AjaxController için özel izin veriliyor
         */
        if($controller == 'AjaxController'){
            $retVal = true;
          //  $retVal2 = true;
        }

        /*
         * Datatable için özel izin veriliyor
         */
        if($action == 'datatable' || $action == 'datatableDetails'){
            $retVal = true;
           // $retVal2 = true;
        }

        /*
         * Sistem yöneticisi için özel izin veriliyor
         */
        /*
        if(auth()->user()->is_admin === 1){
            $retVal = true;
          //  $retVal2 = true;
        }
        */

        /*
         * İzin tanımlı değilse geldiği sayfaya geri gönderiliyor.
         */
       

        //if($retVal == false || $retVal2 == false){
        if($retVal == false){
            //abort(404);
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.no_authorization'), 'error'));
            return redirect()->back();
        }
        
        return $next($request);
    }
}
