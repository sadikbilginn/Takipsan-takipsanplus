<?php

namespace App\Http\Controllers;

use App\License;
use Validator;
use App\User;
use Carbon\Carbon;
use App\Company;
use App\Exports\LicensesExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LicenseController extends Controller
{

    /**
     * Kaynaktan bir liste görüntüler.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){

        $licenses = License::orderBy('finish_at')->get();

        if ($licenses) {
            $data = [];
            foreach ($licenses as $key => $value){
            
                $data[] = [
                    'id'                => $value->id,
                    'status'            => $value->status,
                    'start_date'        => $value->start_at,
                    'finish_date'       => $value->finish_at,
                    'company'           => $value->company_id,
                    //'manufacturer'      => "sdasdas",
                    'user'              => "-"
                ];

            }
            $this->data['licenses'] = $data;
        }

        return View('license.index', $this->data);

    }

    /**
     * Yeni bir kaynak oluşturmak için formu gösterir.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $companies = Company::orderBy('name')->get();
        if($companies && count($companies) > 0){
            $this->data['companies'] = $companies;
        }

        return view('license.create', $this->data);
    }

    /**
     * Yeni oluşturulan bir kaynağı database'e kayıt eder.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request);
        try{

            $attribute = array(
                'company_id'           => trans('portal.companies'),
                //'manufacturer_id'      => trans('portal.manufacturer'),
                // 'user_id'              => 1,
                'start_at'        => trans('portal.license_start'),
                'finish_at'       => trans('portal.license_finish'),
            );

            $rules = array(
                'company_id'            => 'required|numeric',
                //'manufacturer_id'       => 'required|numeric',
                // 'user_id'               => 'required|numeric',
                'start_at'         => 'required',
                'finish_at'        => 'required',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);
            
            if ($validator->fails()) {
            
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $license                        = new License;
            $license->company_id            = $request->get('company_id');
            //$license->manufacturer_id       = $request->get('manufacturer_id');
            //$license->license_type          = $request->get('license_type');
            // $license->user_id               = 1;
            $license->start_at            = $request->get('start_at');
            $license->finish_at           = $request->get('finish_at');
            $license->status                = true;
            //$license->created_user_id       = auth()->user()->id;
            $license->updated_at       = Carbon::now();
            $license->save();

            session()->flash('flash_message', array('Başarılı!', 'Lisans kaydedildi.', 'success'));
            return redirect()->route('license.index');

        }

        catch (\Exception $e){
            session()->flash('flash_message', array('Başarısız!', 'Hata! Lütfen tekrar deneyiniz.', 'error'));
            return redirect()->back()->withInput();
        }

    }

    /**
     * Belirtilen kaynağı düzenlemek için formu gösterir.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $license = License::find($id);
        $this->data['license'] = $license;

        $this->data['license']->start_at = Carbon::parse($this->data['license']->start_at)->translatedFormat('Y-m-d');  
        $this->data['license']->finish_at = Carbon::parse($this->data['license']->finish_at)->translatedFormat('Y-m-d');  

        $this->data['company'] = \App\Company::find($license->company_id);
        
        return View('license.edit', $this->data);
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
                'company_id'           => trans('portal.companies'),
                //'manufacturer_id'      => trans('portal.manufacturer'),
                // 'user_id'              => 1,
                'start_at'        => trans('portal.license_start'),
                'finish_at'       => trans('portal.license_finish'),
            );

            $rules = array(
                'company_id'            => 'required|numeric',
                //'manufacturer_id'       => 'required|numeric',
                // 'user_id'               => 'required|numeric',
                'start_at'         => 'required',
                'finish_at'        => 'required',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $license = License::find($id);

            /*if($request->get('license_period') != ''){
                if(strtotime($license->finish_date) <= strtotime(date('Y-m-d'))){
                    $license->finish_date   = \Carbon\Carbon::now()->addYear($request->get('license_period'))->format('Y-m-d');
                }else{
                    $license->finish_date   = \Carbon\Carbon::parse($license->finish_date)->addYear($request->get('license_period'))->format('Y-m-d');
                }
            }*/
            $license->company_id            = $request->get('company_id');
            //$license->manufacturer_id       = $request->get('manufacturer_id');
            //$license->license_type          = $request->get('license_type');
            // $license->user_id               = 1;
            $license->start_at            = Carbon::parse($license->start_at)->translatedFormat('Y.m.d');  
            $license->finish_at            = Carbon::parse($license->finish_at)->translatedFormat('Y.m.d');  

            $license->updated_at = Carbon::now();  
            
            $license->status                = $request->get('status');
            //$license->updated_user_id       = auth()->user()->id;
            $license->save();

            session()->flash('flash_message', array('Başarılı!','Lisans güncellendi.', 'success'));
            return redirect()->route('license.index');

        }

        catch (\Exception $e){
            session()->flash('flash_message', array('Başarısız!','Hata! Lütfen tekrar deneyiniz.', 'error'));
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
            $license = License::find($id);
            $license->updated_at = Carbon::now();
            $license->deleted_at = Carbon::now();
            $license->save();
            $license->delete();

            session()->flash('flash_message', array('Başarılı!','Lisans silindi.', 'success'));
        }

        catch (\Exception $e){

            session()->flash('flash_message', array('Başarısız!','Hata! Lütfen tekrar deneyiniz.', 'error'));
        }

        return redirect()->route('license.index');
    }

    public function noLicense()
    {
        $companies = Company::all();
        $data = [];

        foreach($companies as $key => $company) {

            $isLicensed = License::where('company_id', $company->id)->count();
            if($isLicensed == 0){   
                
                    $data[] = [
                        'id'                => "-",
                        'start_date'        => "-",
                        'finish_date'       => "-",
                        'company'           => $company->name,
                        'status'            => false,
                        ];
                
            }
        }

        $this->data['licenses'] = $data;

        return View('license.index', $this->data);
    }

    public function licensedExcel()
    {
        return Excel::download(new LicensesExport, 'Lisanslar.xlsx');
    }

    public function notLicensedExcel()
    {
        return Excel::download(new NotLicensesExport, 'Lisans_olmayanlar.xlsx');
    }

}
