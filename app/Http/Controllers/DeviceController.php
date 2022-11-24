<?php

namespace App\Http\Controllers;

use App\Company;
use App\Device;
use App\ReadType;
use App\Helpers\OptionTrait;
use Validator, Image;
use Illuminate\Http\Request;
use App\License;
use App\User;
use Carbon\Carbon;

class DeviceController extends Controller
{ 
    use OptionTrait;
    /**
     * Kaynaktan bir liste görüntüler.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){

        if(roleCheck(config('settings.roles.admin'))){

            $devices = Device::all();
        }
        elseif(roleCheck(config('settings.roles.partner'))){

            $devices = Device::join('companies', function ($join) {
                $join->on('devices.company_id', '=', 'companies.id');
            })->select('companies.name','devices.*')->where(function ($query) {
                $query->where("companies.created_user_id", "=", auth()->user()->id);
            })
            ->where(function ($query) {
                $query->where("companies.status", "=", 1);
            })->get();

        }
        
        if ($devices){
            foreach($devices as $dev){
                //dd($dev->id);
                $license  = License::where('device_id', '=',$dev->id)->first();
                if(isset($license->start_at)){
                    $dev->start_at=Carbon::parse($license->start_at)->translatedFormat('d-m-Y'); 
                    $dev->finish_at=Carbon::parse($license->finish_at)->translatedFormat('d-m-Y'); 
                }else{
                    $dev->start_at=trans('portal.no_license_start_date');
                    $dev->finish_at=trans('portal.no_license_finish_date');
                }
            }
        }

        return View('device.index')->with('devices', $devices);
    }

    /**
     * Yeni bir kaynak oluşturmak için formu gösterir.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->data['companies'] = Company::where('status', true)->where('main_company_id','!=',0)->get();
        $this->data['read_types'] = ReadType::where('status', 1)->get();
        $this->data['start_at'] = Carbon::parse(now())->translatedFormat('Y-m-d');  
        $this->data['finish_at'] = Carbon::parse(date( 'd.m.Y', strtotime('+1 years')))->translatedFormat('Y-m-d');  

        return view('device.create', $this->data);
    }

    /**
     * Yeni oluşturulan bir kaynağı database'e kayıt eder.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{

            $attribute = array(
                'read_type_id'          => trans('portal.read_mode'),
                'company_id'            => trans('portal.company'),
                'device_type'           => trans('portal.device_type'),
                'device_aim'            => trans('portal.device_aim'),
                'name'                  => trans('portal.device_name'),
                'reader'                => trans('portal.reader'),
                'reader_mode'           => trans('portal.reader_mode'),
                'estimated_population'  => trans('portal.est_population'),
                'search_mode'           => trans('portal.reader_mode'),
                'session'               => trans('portal.session'),
                'device_ip'             => trans('portal.reader_ip_address'),
                'package_timeout'       => trans('portal.package_close_time'),
                'common_power'          => trans('portal.common_power'),
                'status'                => trans('portal.status'),
                'start_at'              => trans('portal.license_start'),
                'finish_at'             => trans('portal.license_finish'),
            );

            $rules = array(
                'read_type_id'          => 'required|numeric',
                'company_id'            => 'required|numeric',
                'device_type'           => 'required',
                'device_aim'            => 'required',
                'name'                  => 'required',
                'reader'                => 'required',
                'reader_mode'           => 'required',
                'estimated_population'  => 'required',
                'search_mode'           => 'required',
                'session'               => 'required',
                'device_ip'             => 'required',
                'package_timeout'       => 'required',
                'common_power'          => 'nullable',
                'status'                => 'nullable',
                'start_at'              => 'required|date',
                'finish_at'             => 'required|date|after:start_at'
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            $device                         = new Device;
            $device->company_id             = $request->get('company_id');
            $device->device_type            = $request->get('device_type');
            $device->device_aim             = $request->get('device_aim');
            $device->name                   = $request->get('name');
            $device->reader                 = $request->get('reader');
            $device->reader_mode            = $request->get('reader_mode');
            $device->estimated_population   = $request->get('estimated_population');
            $device->search_mode            = $request->get('search_mode');
            $device->session                = $request->get('session');
            $device->string_set             = $request->get('string_set');
            $device->gpio_start             = $request->get('gpio_start');
            $device->gpio_stop              = $request->get('gpio_stop');
            $device->gpio_error             = $request->get('gpio_error');
            $device->printer_address        = $request->get('printer_address');
            $device->ip_address             = $request->get('device_ip');
            $device->package_timeout        = $request->get('package_timeout');
            $device->common_power           = $request->has('common_power') ? true : false;
            if($request->has('common_power')){
                $device->antennas            = json_encode($request->get('antenna'));
            }else{
                $device->antennas            = json_encode($request->get('antennas'));
            }
            $device->read_type_id           = $request->get('read_type_id');
            $device->auto_print              = $request->has('auto_print') ? true : false;
            $device->status                  = 0;
            $device->created_user_id         = auth()->user()->id;
            
            if($device->save())
            {
                $license = new License;
                $license->license_id = 1;
                $license->device_id = $device->id;
                $license->status = 0;
                $license->company_id = $request->get('company_id');
                $license->start_at = $request->get('start_at');
                $license->finish_at = $request->get('finish_at');
                $license->created_at = Carbon::now();
                if($license->save())
                {
                    session()->flash('flash_message', array(trans('portal.successful'), trans('portal.device_and_license_saved'), 'success'));
                }
            }

        }

        catch (\Exception $e){
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
            return redirect()->back()
                ->withInput();
        }

        return redirect()->route('device.index');
    }

    /**
     * Belirtilen kaynağı düzenlemek için formu gösterir.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        $this->data['device']      = Device::find($id);
        $this->data['companies']   = Company::where('status', 1)->get();
        $this->data['read_types']  = ReadType::where('status', 1)->get();

        $license  = License::where('device_id', '=',$id)->first();

        $this->data['license'] = $license;
        $this->data['license']->start_at = Carbon::parse($license->start_at)->translatedFormat('Y-m-d');  
        $this->data['license']->finish_at = Carbon::parse($license->finish_at)->translatedFormat('Y-m-d');  

        return view('device.edit', $this->data);
    }

    /**
     * Database üzerindeki belirtilen kaynağı günceller.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{

            $attribute = array(
                'read_type_id'          => trans('portal.read_mode'),
                'company_id'            => trans('portal.company'),
                'device_type'           => trans('portal.device_type'),
                'name'                  => trans('portal.device_name'),
                'reader'                => trans('portal.reader'),
                'reader_mode'           => trans('portal.reader_mode'),
                'estimated_population'  => trans('portal.est_population'),
                'search_mode'           => trans('portal.reader_mode'),
                'session'               => trans('portal.session'),
                'device_ip'             => trans('portal.reader_ip_address'),
                'package_timeout'       => trans('portal.package_close_time'),
                'common_power'          => trans('portal.common_power'),
                'status'                => trans('portal.status'),
                'device_aim'             => trans('portal.device_aim'),
                'start_at'              => trans('portal.license_start'),
                'finish_at'             => trans('portal.license_finish')
            );

            $rules = array(
                'read_type_id'          => 'required|numeric',
                'company_id'            => 'required|numeric',
                'device_type'           => 'required',
                'name'                  => 'required',
                'reader'                => 'required',
                'reader_mode'           => 'required',
                'estimated_population'  => 'required',
                'search_mode'           => 'required',
                'session'               => 'required',
                'device_ip'             => 'required',
                'package_timeout'       => 'required',
                'common_power'          => 'nullable',
                'status'                => 'nullable',
                'device_aim'             => 'required',
                'start_at'              => 'required',
                'finish_at'             => 'required|date|after:start_at'
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                //dd($validator->errors());
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $device                         = Device::find($id);
            $device->company_id             = $request->get('company_id');
            $device->device_type            = $request->get('device_type');
            $device->name                   = $request->get('name');
            $device->reader                 = $request->get('reader');
            $device->reader_mode            = $request->get('reader_mode');
            $device->estimated_population   = $request->get('estimated_population');
            $device->search_mode            = $request->get('search_mode');
            $device->session                = $request->get('session');
            $device->string_set             = $request->get('string_set');
            $device->gpio_start             = $request->get('gpio_start');
            $device->gpio_stop              = $request->get('gpio_stop');
            $device->gpio_error             = $request->get('gpio_error');
            $device->printer_address        = $request->get('printer_address');
            $device->ip_address             = $request->get('device_ip');
            $device->package_timeout        = $request->get('package_timeout');
            $device->common_power           = $request->has('common_power') ? true : false;
            if($request->has('common_power')){
                $device->antennas            = json_encode($request->get('antenna'));
            }else{
                $device->antennas            = json_encode($request->get('antennas'));
            }
            $device->read_type_id           = $request->get('read_type_id');
            $device->auto_print              = $request->has('auto_print') ? true : false;
            $device->updated_user_id         = auth()->user()->id;
            
            if($device->save())
            {
                $license = new License;    
                $license->start_at = Carbon::parse($request->get('start_at'))->translatedFormat('Y.m.d');
                $license->finish_at = Carbon::parse($request->get('finish_at'))->translatedFormat('Y.m.d');
                $license->updated_at = Carbon::now();
                if($license->save())
                {
                    session()->flash('flash_message', array(trans('portal.successful'), trans('portal.err_update_device'), 'success'));
                }
            }
        }

        catch (\Exception $e){
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
        }

        return redirect()->route('device.index');
    }

    /**
     * Belirtilen kaynağı database üzerinden kaldırır.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{

            $device = Device::find($id);
            $device->updated_user_id = auth()->user()->id;
            if($device->save()){

                //o cihaza ait lisans da silinmeli

               $license  = License::where('device_id', '=',$id)->first();
               $license->updated_at= Carbon::now();
               $license->delete();
               $license->save();
           }
            $device->delete();

            session()->flash('flash_message', array('Başarılı!','Cihaz silindi.', 'success'));
        }

        catch (\Exception $e){

            session()->flash('flash_message', array('Başarısız!','Hata! Lütfen tekrar deneyiniz.', 'error'));
        }

        return redirect()->route('device.index');
    }

    public function devicePassive($id)
    {
        try{

            $device = Device::find($id);
            $device->updated_user_id= auth()->user()->id;
            $device->status = 0;
            
            if($device->save()){

                 //o cihaza ait lisans da pasife geçmeli

                $license  = License::where('device_id', '=',$id)->first();
                $license->updated_at= Carbon::now();
                $license->status = 0; 
                $license->save();
            }

            session()->flash('flash_message', array(trans('portal.successful'), trans('portal.device_is_deactivated'), 'success'));
        }

        catch (\Exception $e){
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.failed_deactivated'), 'error'));
        }

        return redirect()->route('device.index');
    }

    public function deviceActive($id)
    {
        try{

            $isAdminOrPartner = 0;
            $device = Device::find($id);

            // Lisansı sadece admin veya partner aktif edebilir. O yüzden burada böyle bir kontrol ekledim. 
            //Eğer işlemi yapan kişi admin(-1) veya partner(3) ise cihazın statusunu direkt 1(aktif) yap.
            //Eğer işlemi yapan ana üretici ise cihazın statusunu 2(onay bekliyor) yap.
            if(roleCheck(config('settings.roles.partner')) || roleCheck(config('settings.roles.admin')))
            {
                $device->status = 1;
                $isAdminOrPartner = 1;
            }
            //Normalde bu menüyü admin ve partner haricince kimse göremez ama olur da url den vs. bir şekilde giriş yapılsa bile cihazın statusunu 2(onay bekliyor) yap.
            else
            {
                $device->status = 2;
            }
            $device->updated_user_id= auth()->user()->id;
            
            
            if($device->save()){

                //o cihaza ait lisans da aktife geçmeli

                $license  = License::where('device_id', '=',$id)->first();
                $license->updated_at= Carbon::now();
                $license->status = 1; 
                $license->save();
            }
            if($isAdminOrPartner == 1)
            {
                session()->flash('flash_message', array(trans('portal.successful'), trans('portal.device_is_activated_for_admin'), 'success'));
            }
            else
            {
                session()->flash('flash_message', array(trans('portal.successful'), trans('portal.device_is_activated'), 'success'));
            }
        }

        catch (\Exception $e){
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.failed_activated'), 'error'));
        }

        return redirect()->route('device.index');
    }

}
