<?php

namespace App\Http\Controllers;

use App\Company;
use App\Consignee;
use App\ViewScreen;
use App\Helpers\OptionTrait;
use Rap2hpoutre\FastExcel\FastExcel;
use Validator, Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ConsigneeController extends Controller
{

    use OptionTrait;

    /**
     * Kaynaktan bir liste görüntüler.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){

        if(roleCheck(config('settings.roles.admin')))
        {
            $consignees = Consignee::with(['companies'])->get();
        }
        elseif(roleCheck(config('settings.roles.partner')))
        {
            $consignees = Consignee::with(['companies'])->get();
            //Sadece partnerin oluşturduğu fasonları bir array e atıp, blade tarafında sadece o arrayin içinde olanları gösteriyoruz
            $companies = Company::where('main_company_id', '>', 0)
            ->where(function ($query) {
                $query->where("companies.created_user_id", "=", auth()->user()->id);
            })
            ->where(function ($query) {
                $query->where("companies.status", "=", 1);
            })->pluck('id');

            $consignees->company_array = $companies->toArray();
        }
        elseif(roleCheck(config('settings.roles.anaUretici')))
        {
            $consignees = Consignee::with(['companies'])->get();
            //Sadece partnerin oluşturduğu fasonları bir array e atıp, blade tarafında da in_array() ile kontrol ediyoruz
            $companies = Company::where('main_company_id', '=', auth()->user()->company_id)
            ->where(function ($query) {
                $query->where("companies.status", "=", 1);
            })->pluck('id');

            $consignees->company_array = $companies->toArray();
        }
        return View('consignee.index')->with('consignees', $consignees);
    }

    /**
     * Yeni bir kaynak oluşturmak için formu gösterir.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      //  if(roleCheck(config('settings.roles.partner')))
        $companies = Company::where('status', 1)->get();
        $viewScreen = ViewScreen::all();

        return view('consignee.create')->with('companies', $companies)->with('viewScreen', $viewScreen);
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
                'name'          => trans('portal.company_name'),
                'phone'         => trans('portal.phone'),
                'address'       => trans('portal.address'),
                'auth_name'     => trans('portal.auth_name'),
                'auth_phone'    => trans('portal.auth_phone'),
                'logo'          => trans('portal.image'),
                'status'        => trans('portal.status'),
                'view'          => trans('portal.okuma_ekran')
                //'sayfa_gorunum' => trans('portal.sayfa_gorunum')
            );

            $rules = array(
                'name'             => 'required',
                'phone'            => 'nullable',
                'address'          => 'nullable',
                'auth_name'        => 'nullable',
                'auth_phone'       => 'nullable',
                'logo'             => 'nullable|mimes:jpeg,png,jpg|max:1024',
                'status'           => 'required',
                //'sayfa_gorunum'    => 'required'
                'viewid'           => 'required'
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $file_name = '';
            if($request->hasFile('logo')){

                $file       = $request->file('logo');
                $file_ex    = $file->getClientOriginalExtension();

                $file_name = md5(date('d-m-Y H:i:s') . $file->getClientOriginalName()) . "." . $file_ex;

                Storage::disk(config('settings.media.consignees.root_path'))->makeDirectory(config('settings.media.consignees.path'));

                $image = Image::make($file->getRealPath());

                $image->save(config('settings.media.consignees.full_path') . $file_name);
            }

            $consignee                 = new Consignee;
            $consignee->name           = $request->get('name');
            $consignee->phone          = $request->get('phone');
            $consignee->address        = $request->get('address');
            $consignee->auth_name      = $request->get('auth_name');
            $consignee->auth_phone     = $request->get('auth_phone');
            if($file_name != ''){
                $consignee->logo       = $file_name;
            }
            $consignee->status         = $request->get('status');
            //$consignee->sayfa_gorunum  = $request->get('sayfa_gorunum');
            $consignee->viewid         = $request->get('viewid');
            $consignee->created_user_id= auth()->user()->id;
            if($consignee->save()){
                $companies = $request->get('companies');
                if(!empty($companies)){
                    $consignee->companies()->attach($companies);
                }
            }

            $this->createLog('Consignee','portal.log_add_consignee', ['name' => $consignee->name], $consignee->id);

            session()->flash('flash_message', array(trans('portal.successful'), trans('portal.err_add_consignee'), 'success'));

            return redirect()->route('consignee.index');
        }

        catch (\Exception $e){
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
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
        $consignee = Consignee::find($id);
        if(roleCheck(config('settings.roles.admin')))
        {
            $companies = Company::where('status', 1)->where('main_company_id','>',0)->get();
        }
        elseif(roleCheck(config('settings.roles.partner')))
        {
            $companies = Company::where('status', 1)->where('main_company_id','>',0)->where('created_user_id','=',auth()->user()->id)->get();
        }
        //ana üretici seçeneği yok çünkü ana üretici edit sayfasını göremez.
       
        $viewScreen = ViewScreen::all();

        return View('consignee.edit')
            ->with('consignee', $consignee)
            ->with('companies', $companies)
            ->with('viewScreen', $viewScreen);
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
                'name'          => trans('portal.company_name'),
                'phone'         => trans('portal.phone'),
                'address'       => trans('portal.address'),
                'auth_name'     => trans('portal.auth_name'),
                'auth_phone'    => trans('portal.auth_phone'),
                'logo'          => trans('portal.image'),
                'status'        => trans('portal.status'),
                'view'          => trans('portal.okuma_ekran')
                //'sayfa_gorunum' => trans('portal.sayfa_gorunum')
            );

            $rules = array(
                'name'             => 'required',
                'phone'            => 'nullable',
                'address'          => 'nullable',
                'auth_name'        => 'nullable',
                'auth_phone'       => 'nullable',
                'logo'             => 'nullable|mimes:jpeg,png,jpg|max:1024',
                'status'           => 'required',
                'viewid'           => 'required'
                //'sayfa_gorunum'    => 'required'
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $file_name = '';
            if($request->hasFile('logo')){

                $file       = $request->file('logo');
                $file_ex    = $file->getClientOriginalExtension();

                $file_name = md5(date('d-m-Y H:i:s') . $file->getClientOriginalName()) . "." . $file_ex;

                Storage::disk(config('settings.media.consignees.root_path'))->makeDirectory(config('settings.media.consignees.path'));

                $image = Image::make($file->getRealPath());

                $image->save(config('settings.media.consignees.full_path') . $file_name);
            }

            $consignee                  = Consignee::find($id);
            $consignee->name            = $request->get('name');
            $consignee->phone           = $request->get('phone');
            $consignee->address         = $request->get('address');
            $consignee->auth_name       = $request->get('auth_name');
            $consignee->auth_phone      = $request->get('auth_phone');
            if($file_name != ''){
                $consignee->logo        = $file_name;
            }
            $consignee->status          = $request->get('status');
            //$consignee->sayfa_gorunum  = $request->get('sayfa_gorunum');
            $consignee->viewid          = $request->get('viewid');
            $consignee->updated_user_id = auth()->user()->id;
            if($consignee->save()){
                $companies = $request->get('companies');

                //eğer işlemi yapan adminse
                if(roleCheck(config('settings.roles.admin'))){
                    if(empty($companies)){
                        $consignee->companies()->detach();
                    }else{
                        $consignee->companies()->sync($companies);
                    }
                }
                //eğer işlemi yapan partnerse
                elseif(roleCheck(config('settings.roles.partner'))){

                    //company_consignee tablosunda partnerin oluşturmadığı firmaların id leri çekiliyor.
                    $companiesExcludePartner = DB::table('company_consignee')
                    ->select('companies.id')
                    ->join('companies', function ($join) {
                        $join->on('company_consignee.company_id', '=', 'companies.id');
                    })
                    ->where('companies.created_user_id', '!=', auth()->user()->id)
                    ->where('company_consignee.consignee_id', '=', $id)
                    ->where('companies.status', 1)
                    ->get();

                    if(count($companiesExcludePartner) > 0)
                    {
                        if(isset($companies) && count($companies) > 0)
                        {
                            foreach($companiesExcludePartner as $key => $value)
                            {
                                $newArray = array($value->id);
                            }
                            $allCompanies = array_merge($newArray,$companies);
                            $consignee->companies()->sync($allCompanies);
                        }
                        //sadece burası düzgün çalışmıyor
                        else
                        {
                            $companiesByPartner = DB::table('company_consignee')
                            ->select('companies.id')
                            ->join('companies', function ($join) {
                                $join->on('company_consignee.company_id', '=', 'companies.id');
                            })
                            ->where('companies.created_user_id', '=', auth()->user()->id)
                            ->where('company_consignee.consignee_id', '=', $id)
                            ->where('companies.status', 1)
                            ->get();

                            if(count($companiesByPartner) > 0){
                                foreach($companiesByPartner as $key3 => $value3)
                                {
                                    $newArrayPartner = array($value3->id);
                                }
                                $consignee->companies()->detach($newArrayPartner);
                            }
                        }
                        
                    }
                    //partnerin companyleri haricinde hiç veri yoksa direkt partnerinkileri ekle db ye
                    else
                    {
                        if(empty($companies)){
                            $consignee->companies()->detach();
                        }else{
                            $consignee->companies()->sync($companies);
                        }
                    }      
                }
            }

            $this->createLog('Consignee','portal.log_update_consignee', ['name' => $consignee->name], $consignee->id);

            session()->flash('flash_message', array(trans('portal.successful'), trans('portal.err_update_consignee'), 'success'));

            return redirect()->route('consignee.index');
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
            $consignee = Consignee::find($id);
            $consignee->updated_user_id= auth()->user()->id;
            $consignee->save();
            $consignee->delete();

            $this->createLog('Consignee','portal.log_delete_consignee', ['name' => $consignee->name], $consignee->id);

            session()->flash('flash_message', array(trans('portal.successful'), trans('portal.err_delete_consignee'), 'success'));
        }

        catch (\Exception $e){
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
        }

        return redirect()->route('consignee.index');
    }

}
