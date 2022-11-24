<?php

namespace App\Http\Controllers;

use App\Company;
use App\UserRole;
use App\CompanyDb;
use App\Helpers\OptionTrait;
use Validator, Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Role;
use App\Custom_Permission;
use App\Permission;
use App\User;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Hash;
use Str;
use Illuminate\Support\Facades\DB;


class CompanyController extends Controller
{
    use OptionTrait;

    /**
     * Kaynaktan bir liste görüntüler.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){

        $company_id = auth()->user()->company_id;

        if(roleCheck(config('settings.roles.admin'))){
            $companies = Company::with(['consignments'])->where('main_company_id','=',0)->get();
            foreach($companies as $key => $value){
                $value['subComCount'] = Company::where('main_company_id','=',$value->id)->count();

                //index.blade de partner olan firmaları belirtebilmek için bu sql yazıldı.
                $company_info = DB::table('companies')
                    ->join('users', 'users.company_id', '=', 'companies.id')
                    ->join('user_role', 'users.id', '=', 'user_role.user_id')
                    ->join('roles', 'user_role.role_id', '=', 'roles.id')
                    ->where('user_role.role_id', '=', 3)->where('users.status','=', 1)->where('companies.status','=', 1)->where('companies.id','=',$value->id)
                    ->count();
                if($company_info ==1) { $value['isPartner'] = 1; }
                else { $value['isPartner'] = 0; }

                $isCreatedByPartner = DB::table('user_role')->where('user_id','=', $value->created_user_id)->first();
                if($isCreatedByPartner->role_id ==3) { $value['isCreatedByPartner'] = 1; } else { $value['isCreatedByPartner'] = 0; }
            }
        }
        else{
            // partner sadece kendi oluşturduğu ana üreticileri görebilir üreticiler sayfasında.
            if(roleCheck(config('settings.roles.partner'))){ // partner
                $companies = Company::with(['consignments'])
                ->where(function ($query) {
                    $query->where("status", "=", 1);
                })
                ->where(function ($query) {
                    $query->where("created_user_id", "=", auth()->user()->id);
                })
                ->where(function ($query) {
                    $query->where("main_company_id", "=", 0);
                })
                ->get();
                foreach($companies as $key => $value){
                    $value['subComCount'] = Company::where('main_company_id','=',$value->id)->count();
                    $value['isPartner'] = 0;
                    $value['isCreatedByPartner'] = 0;
                }
            }else{
                //ana üretici
                $companies = Company::with(['consignments'])->where('main_company_id', auth()->user()->company_id)->get();
                foreach($companies as $key => $value){
                    $value['subComCount'] = 0;
                    $value['isPartner'] = 0;
                    $value['isCreatedByPartner'] = 0;
                }
            }
            //$companies = Company::where('main_company_id', auth()->user()->company_id)->get();
        }
        
        return View('company.index')->with('companies', $companies);
    }

    public function subCompanyIndex($id)
    {
        $companies = Company::with(['consignments'])->where('main_company_id','=', $id)->get();
        return View('company.subcompanyindex')->with('companies', $companies);
    }

    /**
     * Yeni bir kaynak oluşturmak için formu gösterir.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Session'daki kişinin rolü nedir bunu getiriyoruz.
        if(roleCheck(config('settings.roles.admin'))){
            $this->data['userRole'] = Role::all();
        }else{
            $userRole = DB::table('user_role')
            ->select('*')
            ->join('roles', function($join){ $join->on('user_role.role_id', '=', 'roles.id');})
            ->where('user_role.user_id', auth()->user()->id)
            ->get();
            if ($userRole){
                foreach($userRole as $usr){
                    if($usr->title=='Partner'){
                        $this->data['userRole'] = Role::where('title','Üretici')->orWhere('title','Ana Üretici')->get();
                        break;
                    }else if($usr->title=='Ana Üretici'){
                        $this->data['userRole'] = Role::where('title','Üretici')->get();
                        break;
                    }
                    //$this->data['userRole'] = $usr->title;
                }
            }
        }
        $main_company_id = session('main_company_id');
        //Sessiondaki kullanıcı admin ise tüm firmalara kullanıcı ekleyebilir. O yüzden veritabanından tüm firmalar çekiliyor.
        
        if(roleCheck(config('settings.roles.admin'))){
            $company_info = Company::where('status', true)->where('main_company_id','=',0)->get();
        }
        else{
            //Sessiondaki kullanıcı ana üretici / partner ise ya kendi firmasına ya da fason firmalarından birine kullanıcı ekleyebilir.
            if($main_company_id == 0 && roleCheck(config('settings.roles.anaUretici'))){ // ana üretici
                $company_info = Company::where('status', true)->where('id',[auth()->user()->company_id])->get();
            }
            else if($main_company_id == 0 && roleCheck(config('settings.roles.partner'))){ // partner
                $company_info = Company::where('status', true)->where('main_company_id','=',0)->where('created_user_id', '=', auth()->user()->id)->get();
            }
            //Sessiondaki kullanıcı fason firma ise sadece kendi firmasına kullanıcı ekleyebilir. ASlında buna gerek yok çünkü zaten fason firma kullanıcısı, yeni bir kullanıcı ekleyemez. Yetkisi yok.
            else{
                $company_info = Company::where('status', true)->where('id',[auth()->user()->company_id])->get();
            }
        }
        $this->data['company'] = $company_info;

        /*if(roleCheck(config('settings.roles.admin'))){
            $this->data['roles'] = Role::all();
        }
        else{
            $this->data['roles'] = Role::where('title', 'Üretici')->get();
        }*/

        return view('company.create',$this->data);
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
                //'title'         => trans('portal.company_title'),
                'phone'         => trans('portal.phone'),
                'email'         => trans('portal.email'),
                //'address'       => trans('portal.address'),
                //'latitude'      => 'Latitude',
                //'longitude'     => 'Longitude',
                'roles'         => "Test",
                'company_id'    => "Test Company",
                'logo'          => trans('portal.image'),
                'username' => trans('portal.username'),
                'password' => 'string|min:6|confirmed',
                'password_confirmation' => 'string|min:6'
            );

            $rules = array(
                'name'             => 'required|unique:companies',
                //'title'            => 'required',
                'phone'            => 'nullable',
                'email'            => 'required|email',
                //'address'          => 'required',
                //'latitude'         => 'required|numeric',
                //'longitude'        => 'required|numeric',
                'roles'             => 'required',
                'company_id'        => 'nullable',
                'logo'             => 'nullable|mimes:jpeg,png,jfif,jpg|max:1024',
                'username' => 'required|unique:users',
                'password' => 'string|min:6|confirmed',
                'password_confirmation' => 'string|min:6'

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

                Storage::disk(config('settings.media.companies.root_path'))->makeDirectory(config('settings.media.companies.path'));

                $image = Image::make($file->getRealPath());

                $image->save(config('settings.media.companies.full_path') . $file_name);
            }

            $company                    = new Company;
            $company->name              = $request->get('name');
            //$company->title             = $request->get('title');
            $company->phone             = $request->get('phone');
            $company->email             = $request->get('email');
            //$company->address           = $request->get('address');
            //$company->latitude          = $request->get('latitude');
            //$company->longitude         = $request->get('longitude');
            if($file_name != ''){
                $company->logo          = $file_name;
            }
            $company->status =1;

            if($request->get('company_id')=='')
            { $company->main_company_id=0; }
            else
            { $company->main_company_id = $request->get('company_id');  }
            $company->consignment_close = true;
            //$company->consignment_close = $request->has('consignment_close') ? true : false;
            if(roleCheck(config('settings.roles.anaUretici'))){
                $ekleyeninFirmaBilgisi = Company::where('id', '=', auth()->user()->company_id)->first();
                $company->created_user_id  = $ekleyeninFirmaBilgisi->created_user_id;
            }// ana üretici, üretici eklerken tüm alt firmalarını yakalamak için partner / admin user bilgisi kullanıldı.
            else{
                $company->created_user_id   = auth()->user()->id;
            }
            if($company->save()){
                //company tanımdan sonra kullanıcısının işlemleri   
                $lastInsertedId = $company->id;
                $user = new User;
                $user->company_id = $lastInsertedId;
                $user->username = $request->get('username');
                $user->password = bcrypt($request->get('password'));//Hash::make(Str::random(8))
                $user->created_user_id = auth()->user()->id;

                $user->save();

                // Rol tanımlaması eklendi.
                /*$usrRole                   = new UserRole;
                $usrRole->user_id          = $user->id;
                $usrRole->role_id          = $request->get('roles');
                $usrRole->save();*/
                $roles[] = $request->get('roles');
                if(isset($roles) && !empty($roles)){
                    $user->roles()->attach($roles);
                }

                //Default permissionlar ekleniyor.
                $defaultPer = DB::table('role_permission')
                ->select('permission_id')->where('role_id', $request->get('roles'))
                ->get();
                if ($defaultPer){
                    $arr = array();
                    foreach($defaultPer as $per){
                        array_push($arr,$per->permission_id);
                    }
                }
                $user->permissions()->attach($arr);

                $this->createLog('Company','portal.log_create_company', ['name' => $request->get('name')], $company->id);
                session()->flash('flash_message', array(trans('portal.successful'), trans('portal.err_add_company'), 'success'));
                return redirect()->route('company.index');
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
        $company = Company::with(['consignments','consignments.consignee', 'devices'])->findOrFail($id);
        if ($company) {
            $this->data['company'] = $company;
        }

        $last_consignments = \App\Consignment::with(['consignee'])->withCount('items')->where('company_id', $id)->orderBy('updated_at', 'desc')->limit(10)->get();
        if ($last_consignments) {
            $this->data['last_consignments'] = $last_consignments;
        }

        $maxConsignee = \App\Consignment::where('company_id', $id)->where('consignee_id', '!=', 0)->with(['consignee'])->select('consignee_id', \Illuminate\Support\Facades\DB::raw('count(consignee_id) as total'))->groupBy('consignee_id')->orderBy('total', 'desc')->limit(3)->get();
        if ($maxConsignee) {
            $this->data['maxConsignee'] = $maxConsignee;
        }

        return View('company.show', $this->data);
    }

    /**
     * Belirtilen kaynağı düzenlemek için formu gösterir.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $company = Company::findOrFail($id);
        //Session'daki kişinin rolü nedir bunu getiriyoruz.
        /*if(roleCheck(config('settings.roles.admin'))){
            $company['userRole'] = Role::all();
            
        }else{
            $userRole = DB::table('user_role')
            ->select('*')
            ->join('roles', function($join){ $join->on('user_role.role_id', '=', 'roles.id');})
            ->where('user_role.user_id', auth()->user()->id)
            ->get();
            dd($userRole);
            if ($userRole){
                foreach($userRole as $usr){
                    if($usr->title=='Partner'){
                        $company['userRole'] = Role::where('title','Üretici')->orWhere('title','Ana Üretici')->get();
                    
                        break;
                    }else if($usr->title=='Ana Üretici'){
                        $company['userRole'] = Role::where('title','Üretici')->get();
                        
                        break;
                    }
                }
            }
        }
        */
        $main_company_id = session('main_company_id');
        //Sessiondaki kullanıcı admin ise tüm firmalara kullanıcı ekleyebilir. O yüzden veritabanından tüm firmalar çekiliyor.
        
        if(roleCheck(config('settings.roles.admin'))){
            $company_info = Company::where('status', true)->where('main_company_id','=',0)->get();
        }
        else{
            //Sessiondaki kullanıcı ana üretici / partner ise ya kendi firmasına ya da fason firmalarından birine kullanıcı ekleyebilir.
            if($main_company_id == 0 && roleCheck(config('settings.roles.anaUretici'))){ // ana üretici
                $company_info = Company::where('status', true)->where('id',[auth()->user()->company_id])->get();
            }
            else if($main_company_id == 0 && roleCheck(config('settings.roles.partner'))){ // partner
                $company_info = Company::where('status', true)->where('main_company_id','=',0)->get();
            }
            //Sessiondaki kullanıcı fason firma ise sadece kendi firmasına kullanıcı ekleyebilir. ASlında buna gerek yok çünkü zaten fason firma kullanıcısı, yeni bir kullanıcı ekleyemez. Yetkisi yok.
            else{
                $company_info = Company::where('status', true)->where('id',[auth()->user()->company_id])->get();
            }
        }
        $company['company']=$company_info;

        $user = User::where('company_id','=',$id)->first();
        $company['username']= $user->username;
        /*$userRole = DB::table('user_role')
            ->select('role_id')
            ->where('user_role.user_id', $user->id)
            ->first();

        $company['userRoleId'] = $userRole->role_id;
        */
        return View('company.edit')->with('company', $company);

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
                    //'title'         => trans('portal.company_title'),
                    'phone'         => trans('portal.phone'),
                    'email'         => trans('portal.email'),
                    //'address'       => trans('portal.address'),
                    //'latitude'      => 'Latitude',
                    //'longitude'     => 'Longitude',
                    'logo'          => trans('portal.image')
            );    

            $rules = array(
                    'name'             => ['required',Rule::unique('companies')->ignore($id, 'id')],
                    //'title'            => 'required',
                    'phone'            => 'nullable',
                    'email'            => 'required|email',
                    //'address'          => 'required',
                    //'latitude'         => 'required|numeric',
                    //'longitude'        => 'required|numeric',
                    'logo'             => 'nullable|mimes:jpeg,png,jfif,jpg|max:1024',
            );       
            
             $validator = Validator::make($request->all(), $rules);
             $validator->setAttributeNames($attribute);

             if ($validator->fails()) {
                 return redirect()->back()
                     ->withErrors($validator)
                     ->withInput();
             }
             else
             {
                $file_name = '';
                if($request->hasFile('logo')){

                    $file       = $request->file('logo');
                    $file_ex    = $file->getClientOriginalExtension();

                    $file_name = md5(date('d-m-Y H:i:s') . $file->getClientOriginalName()) . "." . $file_ex;

                    Storage::disk(config('settings.media.companies.root_path'))->makeDirectory(config('settings.media.companies.path'));

                    $image = Image::make($file->getRealPath());

                    $image->save(config('settings.media.companies.full_path') . $file_name);
                }

                $company                    = Company::find($id);
                $company->name              = $request->get('name');
                //$company->title             = $request->get('title');
                $company->phone             = $request->get('phone');
                $company->email             = $request->get('email');
                //$company->address           = $request->get('address');
                //$company->latitude          = $request->get('latitude');
                //$company->longitude         = $request->get('longitude');
                if($file_name != ''){
                    $company->logo          = $file_name;
                }
                //$company->status            = $request->get('status');
                //$company->consignment_close = $request->has('consignment_close') ? true : false;
                $company->updated_user_id   = auth()->user()->id;

                if($company->save()){
                    
                $this->createLog('Company','portal.log_update_company', ['name' => $request->get('name')], $company->id);

                session()->flash('flash_message', array(trans('portal.successful'), trans('portal.err_update_company'), 'success'));

                return redirect()->route('company.index');
                }

             }
        }
        catch (\Exception $e){
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
            return redirect()->back()->withInput();
        }
    }

    function get_absolute_path($path) {
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.' == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return implode(DIRECTORY_SEPARATOR, $absolutes);
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
            $company = Company::find($id);
            $company->updated_user_id= auth()->user()->id;
            $company->save();
            $company->delete();

            $user = User::where('company_id',$id);
            $user->delete();

            $this->createLog('Company','portal.log_delete_company', ['name' => $company->name], $company->id);

            session()->flash('flash_message', array(trans('portal.successful'), trans('portal.err_delete_company'), 'success'));
        }

        catch (\Exception $e){
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
        }

        return redirect()->route('company.index');
    }
}
