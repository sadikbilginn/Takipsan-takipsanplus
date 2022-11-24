<?php

namespace App\Http\Middleware;

use App\Helpers\OptionTrait;
use Closure;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class License
{
    use OptionTrait;

    public function __construct()
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $main_company_id = session('main_company_id');
        //Sistem kullanıcısı veya admin ise izin ver
        if(auth()->check() && $main_company_id>0){
            return $next($request);
        }
        else
        {
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.only_manufacturers_can_access_read_screen'), 'error'));
            return redirect()->route('home');
           
        }
        /*
        if( isset(auth()->user()->company->id ) ){
            if( $licenseTime = DB::table('licenses')->where('company_id', auth()->user()->company->id)->latest('finish_at')->first() ){

                if( $licenseTime->finish_at < Carbon::now()->addDay() OR $licenseTime->status == 0 ){
                    // session()->forget('license_finish_days');
                    // return View::make('errors.license-problem');
                    //auth()->logout();
                    return redirect()->route('errors.license-problem');
                }
                elseif( $licenseTime->finish_at < Carbon::now()->addMonths() ){
                    session()->put('license_finish_days', Carbon::now()->diffInDays( $licenseTime->finish_at ) );
                }
            }
            // else{
            //     auth()->logout();
            //     return redirect()->route('errors.license-problem');
            // }
        }
        */

        // if(!session()->has('check_license')){
        //     $licese = $this->get_service(
        //         'post',
        //         config('settings.main_host_api.license'),
        //         [
        //             'companyCode'       => config('settings.main_host_api.company_code'),
        //             'manufacturerId'    => auth()->user()->company_id,
        //             'userId'            => auth()->user()->id
        //         ]);
        //     if($licese && $licese->code == 00){

        //         session()->put('check_license', true);

        //         Artisan::call('config:clear');

        //     }else{
        //         session()->flash('flash_message', array(trans('portal.failed'), trans('portal.licensing_problem'), 'error'));
        //         auth()->logout();
        //         session()->forget('check_license');
        //         return redirect()->route('station.loginShow');
        //     }
        // }

       // return $next($request);
    }
}
