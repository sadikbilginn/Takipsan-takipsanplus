<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Locale;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        session()->put('glb_locales', Locale::all());

        return view('auth.login');
    }

    public function authenticated()
    {
        if(auth()->user()->company_id != 0){
            $company_check = \App\Company::where('status', true)->find(auth()->user()->company_id);
            if(!$company_check){
                session()->flash('flash_message', array(trans('portal.failed'), trans('portal.system_shut_down'), 'error'));
                auth()->logout();
                return redirect()->route('login');
            }
        }

        //Özel izinler atanıyor
        $permissionsIds = auth()->user()->custom_permission->pluck('id')->toArray();
        session()->put('user_custom_permission_ids', $permissionsIds);

        //izinler atanıyor
        $permissions = auth()->user()->permissions;
        session()->put('user_permissions', $permissions);

        //rol bilgileri atanıyor
        $roles = auth()->user()->roles;
        session()->put('user_roles', $roles);
        
        //Kullanıcının ana üreticisi atanıyor.
        $company_info = \App\Company::where('status', true)->find(auth()->user()->company_id);
        session()->put('main_company_id', $company_info->main_company_id);
        //Kullanımı: session('main_company_id')    
    }

    public function logout ()
    {
        auth()->logout();

        session()->forget('company_permissions');
        session()->forget('check_license');
        session()->forget('main_company_id');

        return redirect('/');
    }

    public function username()
    {
        return 'username';
    }
}
