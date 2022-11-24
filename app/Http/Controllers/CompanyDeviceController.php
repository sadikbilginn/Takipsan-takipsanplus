<?php

namespace App\Http\Controllers;

use App\Device;
use App\Company;
use App\ReadType;
use Validator, Image;
use Illuminate\Http\Request;
use App\Helpers\OptionTrait;
use App\License;
use App\User;
use Carbon\Carbon;

class CompanyDeviceController extends Controller
{
    use OptionTrait;
    /**
     * Kaynaktan bir liste görüntüler.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id){

        $company = Company::find($id);
        return View('company.device.index')->with('company', $company);

    }

    /**
     * Yeni bir kaynak oluşturmak için formu gösterir.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $this->data['company'] = Company::find($id);
        $this->data['read_types']  = ReadType::where('status', 1)->get();

        return View('company.device.create', $this->data);
    }

    /**
     * Yeni oluşturulan bir kaynağı database'e kayıt eder.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        try{

            $attribute = array(
                'read_type_id'          => trans('portal.read_mode'),
                'device_type'           => trans('portal.device_type'),
                'name'                  => trans('portal.device_name'),
                'reader'                => 'Reader',
                'reader_mode'           => 'Reader Mode',
                'estimated_population'  => 'Est. Population',
                'search_mode'           => 'Search Mode',
                'session'               => 'Session',
                'device_ip'             => trans('portal.reader_ip_address'),
                'package_timeout'       => trans('portal.package_close_time'),
                'common_power'          => trans('portal.common_power'),
                'status'                => trans('portal.status'),
            );

            $rules = array(
                'read_type_id'          => 'required|numeric',
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
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $device                         = new Device;
            $device->company_id             = $id;
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
            $device->read_type_id            = $request->get('read_type_id');
            $device->auto_print              = $request->has('auto_print') ? true : false;
            $device->status                  = 0;
            $device->created_user_id         = auth()->user()->id;
            if($device->save())
            {
                $license = new License;
                $license->license_id = 1;
                $license->device_id = $device->id;
                $license->status = 0;
                $license->company_id = $id;
                $license->start_at = $request->get('start_at');
                $license->finish_at = $request->get('finish_at');
                $license->created_at = Carbon::now();
                if($license->save())
                {
                    session()->flash('flash_message', array(trans('portal.successful'), trans('portal.device_and_license_saved'), 'success'));
                    return redirect()->route('company.device.index', $id);
                }
            }

        }

        catch (\Exception $e){
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
            return redirect()->back()->withInput();
        }

    }

    /**
     * Belirtilen kaynağı gösterir.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $device = Device::with('company')->find($id);
        return View('company.device.show')->with('device', $device);
    }

    /**
     * Belirtilen kaynağı düzenlemek için formu gösterir.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->data['device']  = Device::with('company')->find($id);
        $this->data['read_types']  = ReadType::where('status', 1)->get();

        return View('company.device.edit', $this->data);
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
                'device_type'           => trans('portal.device_type'),
                'name'                  => trans('portal.device_name'),
                'reader'                => 'Reader',
                'reader_mode'           => 'Reader Mode',
                'estimated_population'  => 'Est. Population',
                'search_mode'           => 'Search Mode',
                'session'               => 'Session',
                'device_ip'             => trans('portal.reader_ip_address'),
                'package_timeout'       => trans('portal.package_close_time'),
                'common_power'          => trans('portal.common_power'),
                'status'                => trans('portal.status'),
            );

            $rules = array(
                'read_type_id'          => 'required|numeric',
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
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $device                         = Device::find($id);
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
            $device->save();

            session()->flash('flash_message', array(trans('portal.successful'), trans('portal.err_update_device'), 'success'));
            return redirect()->route('company.device.index', $device->company_id);
        }

        catch (\Exception $e){
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
            return redirect()->back()->withInput();
        }

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
            $device->updated_user_id= auth()->user()->id;
            if($device->save()){

                //o cihaza ait lisans da silinmeli

               $license  = License::where('device_id', '=',$id)->first();
               $license->updated_at= Carbon::now();
               $license->delete();
               $license->save();
           }
            $device->delete();

            session()->flash('flash_message', array(trans('portal.successful'), trans('portal.err_delete_device'), 'success'));
        }

        catch (\Exception $e){
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
        }

        return redirect()->route('company.device.index', $device->company_id);
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

        return redirect()->route('company.device.index', $device->company_id);
    }

    public function deviceActive($id)
    {
        try{

            $isAdminOrPartner = 0;
            $device = Device::find($id);

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
                
                //o cihaza ait lisans da etkilenmeli
                $license  = License::where('device_id', '=',$id)->first();
                $license->updated_at= Carbon::now();

                if($isAdminOrPartner == 1){
                    $license->status = 1; 
                }
                else
                {
                    $license->status = 2; 
                }
                if($license->save())
                {
                    if($isAdminOrPartner == 1)
                    {
                        session()->flash('flash_message', array(trans('portal.successful'), trans('portal.device_is_activated_for_admin'), 'success'));
                    }
                    else
                    {
                        session()->flash('flash_message', array(trans('portal.successful'), trans('portal.device_is_activated'), 'success'));
                    }
                }
           }
            
        }

        catch (\Exception $e){
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.failed_activated'), 'error'));
        }
        return redirect()->route('company.device.index', $device->company_id);
    }

}
