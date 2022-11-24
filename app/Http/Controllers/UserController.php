<?php

namespace App\Http\Controllers;

use App\Company;
use App\Custom_Permission;
use App\Permission;
use App\Helpers\OptionTrait;
use App\Role;
use App\User;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Hash;
use Str;
use Illuminate\Support\Facades\DB;

class UserController extends Controller{

    use OptionTrait;

    /**
     * Kaynaktan bir liste görüntüler.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        
        return view('user.index', $this->data);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){

        // ekli olan kullanıcının rol bilgisi alınıyor.
        $userRole = DB::table('user_role')
            ->select('*')
            ->join('roles', function($join){ $join->on('user_role.role_id', '=', 'roles.id');})
            ->where('user_role.user_id', auth()->user()->id)
            ->get();

        if ($userRole){
            foreach($userRole as $usr){ $this->data['userRole'] = $usr->title; }
        }
        
        $this->data['permissions'] = Permission::all()->sortBy('title')->groupBy('controller');
        //Sessiondaki kişi admin ise hem ana üretici hem üretici ekleyebilir.
        if(roleCheck(config('settings.roles.admin')))
        {
            $this->data['roles'] = Role::all();
        }
        //Sessiondaki kişi ana üretici ise sadece üretici ekleyebilir.
        else
        {
            $this->data['roles'] = Role::where('title', 'Üretici')->get();
        }
        
        $this->data['customPermission'] = Custom_Permission::all();

        $main_company_id = session('main_company_id');

        //Sessiondaki kullanıcı admin ise tüm firmalara kullanıcı ekleyebilir. O yüzden veritabanından tüm firmalar çekiliyor.
        if(roleCheck(config('settings.roles.admin'))){

            $company_info = Company::where('status', true)->get();

        }
        else{
            //Sessiondaki kullanıcı ana üretici ise ya kendi firmasına ya da fason firmalarından birine kullanıcı ekleyebilir.
            if($main_company_id ==0)
            {
                //$company_info = Company::where('status', true)->find(auth()->user()->company_id)->all();
                $company_info = Company::where('status', true)->where('main_company_id',[auth()->user()->company_id])->get();
            }
            //Sessiondaki kullanıcı fason firma ise sadece kendi firmasına kullanıcı ekleyebilir. ASlında buna gerek yok çünkü zaten fason firma kullanıcısı, yeni bir kullanıcı ekleyemez. Yetkisi yok.
            else{
                $company_info = Company::where('status', 1)->where('id',[auth()->user()->company_id])->get();
            }
        }
        $this->data['company'] = $company_info;



        return view('user.create', $this->data);

    }

    /**
     * Yeni oluşturulan bir kaynağı database'e kayıt eder.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){

        try{

            $attribute = array(
                'company_id' => trans('portal.company'),
                'username' => trans('portal.username'),
                'name' => trans('portal.name'),
                'email' => trans('portal.email'),
                'password' => trans('portal.password'),
                'password_confirmation' => trans('portal.confirm_password'),
                'permissions' => trans('portal.permissions'),
                'roles' => trans('portal.role'),
            );

            $rules = array(
                'company_id' => 'nullable',
                'username' => 'required|unique:users',
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:150',
                'password' => 'string|min:6|confirmed',
                'password_confirmation' => 'string|min:6',
                'permissions' => 'nullable',
                'roles' => 'required',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $user = new User;
            $user->company_id = $request->get('company_id');
            $user->username = $request->get('username');
            $user->name = $request->get('name');
            $user->email = $request->get('email');
            $user->password = bcrypt($request->get('password'));//Hash::make(Str::random(8))
            $user->created_user_id = auth()->user()->id;

            if($user->save()){
                $permissions = $this->arrayOnlyNumeric(explode(',', $request->get('permissions')));
                if(isset($permissions) && !empty($permissions)){
                    $user->permissions()->attach($permissions);
                }

                $roles = $request->get('roles');
                if(isset($roles) && !empty($roles)){
                    $user->roles()->attach($roles);
                }

                $customPermission = $request->get('customPermission');
                if (isset($customPermission) && !empty($customPermission)) {
                    $user->custom_permission()->attach($customPermission);
                }
            }

            // mail ayarları ile ilgili duzenleme yapılmalı localde kapatıldı.
            // $userData = [
            //     'username' => $request->get('username'),
            //     'name' => $request->get('name'),
            //     'password' => $password
            // ];

            // $user->createUserNotify($userData);

            session()->flash(
                'flash_message', 
                array(trans('portal.successful'), trans('portal.err_add_user'), 'success')
            );
        }

        catch (\Exception $e){
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
            //return redirect()->back()->withInput();
        }

        //exit();

        return redirect()->route('user.index');
    }

    /**
     * Belirtilen kaynağı düzenlemek için formu gösterir.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){
        if($id == 1){
            abort(404);
        }

        $userRole = DB::table('user_role')
        ->select('*')
        ->join('roles', function($join){ $join->on('user_role.role_id', '=', 'roles.id');})
        ->where('user_role.user_id', auth()->user()->id)
        ->get();

        if ($userRole){
            foreach($userRole as $usr){
                $this->data['userRole'] = $usr->title;
            }
        }

        $user = User::findOrFail($id);
        $this->data['customPermission'] = Custom_Permission::all();
        $this->data['company'] = Company::all();
        //Sessiondaki kişi admin ise hem ana üretici hem üretici düzenleyebilir.
        if(roleCheck(config('settings.roles.admin')))
        {
            $this->data['roles'] = Role::all();
        }
        //Sessiondaki kişi ana üretici ise sadece üretici düzenleyebilir.
        else
        {
            $this->data['roles'] = Role::where('title', 'Üretici')->get();
        }
        $this->data['user'] = $user;
        $this->data['permissions'] = Permission::all()->sortBy('title')->groupBy('controller');

        if($user->permissions){
            $userSelectPermission = [];
            foreach ($user->permissions as $value){
                array_push($userSelectPermission, $value->id);
            }
            $this->data['userSelectPermission'] = $userSelectPermission;
        }

        if($user->roles){
            $userSelectRoles = [];
            foreach ($user->roles as $value){
                array_push($userSelectRoles, $value->id);
            }
            $this->data['userSelectRoles'] = $userSelectRoles;
        }

        if ($user->custom_permission){
            $this->data['selectedCustomPermission'] = $user->custom_permission->pluck('id')->toArray();
        }

        $main_company_id = session('main_company_id');
        if(roleCheck(config('settings.roles.admin'))){

            $company_info = Company::where('status', true)->get();

        }else{
            //Sessiondaki kullanıcı ana üretici ise
            if($main_company_id ==0)
            {
                //$company_info = Company::where('status', true)->find(auth()->user()->company_id)->all();
                $company_info = Company::where('status', true)->where('main_company_id',[auth()->user()->company_id])->get();
            }
            //Sessiondaki kullanıcı fason ise
            else{
                $company_info = Company::where('status', 1)->where('id',[auth()->user()->company_id])->get();
            }
        }
        $this->data['company'] = $company_info;


        return view('user.edit', $this->data);
    }

    /**
     * Database üzerindeki belirtilen kaynağı günceller.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        try{

            if($id == 1){
                abort(404);
            }

            $attribute = array(
                'company_id' => trans('portal.company'),
                'username' => trans('portal.username'),
                'name' => trans('portal.name'),
                'email' => trans('portal.email'),
             //   'password' => trans('portal.password'),
            //    'password_confirmation' => trans('portal.confirm_password'),
                'permissions' => trans('portal.permissions'),
                'roles' => trans('portal.role'),
            );

            $rules = array(
                'company_id' => 'nullable',
                'username' => 'required|unique:users,username,'.$id,
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:150',
             //   'password' => 'string|min:6|confirmed',
             //   'password_confirmation' => 'string|min:6',
                'permissions' => 'nullable',
                'roles' => 'required',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $user = User::find($id);
            $user->company_id = $request->get('company_id');
            $user->username = $request->get('username');
            $user->name = $request->get('name');
            $user->email = $request->get('email');
        //    $user->password = bcrypt($request->get('password'));
            $user->updated_user_id = auth()->user()->id;
            if($user->save()){
                $permissions = $this->arrayOnlyNumeric(explode(',', $request->get('permissions')));
                if(empty($permissions)){
                    $user->permissions()->detach();
                }else{
                    $user->permissions()->sync($permissions);
                }
                $roles = $request->get('roles');
                if(empty($roles)){
                    $user->roles()->detach();
                }else{
                    $user->roles()->sync($roles);
                }
                $customPermission = $request->get('customPermission');
                if (empty($customPermission)) {
                    $user->custom_permission()->detach();
                }else{
                    $user->custom_permission()->sync($customPermission);
                }
            }

            session()->flash(
                'flash_message', 
                array(trans('portal.successful'), trans('portal.err_update_user'), 'success')
            );
        }

        catch (\Exception $e){
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
        }

        return redirect()->route('user.index');
    }

    /**
     * Belirtilen kaynağı database üzerinden kaldırır.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){

        try{
            if($id == 1){
                abort(404);
            }

            $user = User::find($id);
            $user->updated_user_id = auth()->user()->id;
            $user->save();
            $user->delete();

            session()->flash(
                'flash_message', 
                array(trans('portal.successful'), trans('portal.err_delete_user'), 'success')
            );
        }
        catch (\Exception $e){
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
        }

        return redirect()->route('user.index');

    }

    /**
     * Kaynaktan bir json liste görüntüler.
     *
     * @return \Illuminate\Http\Response
     */
    public function datatable(Request $request){
        //Kullanıcı listesini görüntüleyecek kişi admin ise, kendisi hariç tüm firmaların tüm kullanıcılarını görsün.
        if(roleCheck(config('settings.roles.admin'))){
            $query = User::whereNotIn('id',[auth()->user()->id])->with(['roles'])->orderBy('updated_at', 'desc');      
        }
         //Kullanıcı listesini görüntüleyecek kişi Ana üretici firmanın kullanıcısı ise, hem kendi firmasındaki kullanıcıları, hem alt fason firmaların kullanıcılarını görsün. Alt üretici zaten kullanıcı listesini hiç göremiyor, yetkisi yok.
        else{
            $query = User::join('companies', function ($join) {
                $join->on('users.company_id', '=', 'companies.id');
            })->select('users.*')
            ->where(function ($query) {
                $query->where('companies.id','=',[auth()->user()->company_id])
                    ->orWhere('companies.main_company_id','=',[auth()->user()->company_id]);
            })->with(['roles'])->orderBy('users.updated_at', 'desc');
    
        }
        

        return Datatables::of($query)
            ->editColumn('role', function ($value){
                return count($value->roles ) > 0 ? $value->roles->pluck('title')->implode(', ') : '-';
            })
            ->editColumn('company', function ($value){
                return $value->company ? $value->company->name : '-';
            })
            ->editColumn('action', function ($value){
                $act = '<span class="dropdown">
                    <a 
                        href="#" 
                        class="btn btn-sm btn-clean btn-icon btn-icon-md" 
                        data-toggle="dropdown" 
                        aria-expanded="true"
                    >
                        <i class="la la-cogs"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="' . route('user.edit', $value->id) . '">
                            <i class="la la-edit"></i> '.trans('portal.edit').'
                        </a>';

                        if($value->id != auth()->user()->id){ // kendi kullanıcısını silememesi için

                        $act .= '<a 
                            class="dropdown-item" 
                            href="' . route('user.destroy', $value->id) . '" 
                            data-method="delete" 
                            data-token="' . csrf_token() . '" 
                            data-confirm="'.trans('portal.delete_text').'">
                            <i class="la la-trash"></i> '.trans('portal.delete').'
                        </a>';
                        }
                        $act .= '</div>
                </span>';
                return $act;
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

}