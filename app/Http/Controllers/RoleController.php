<?php

namespace App\Http\Controllers;

use App\Custom_Permission;
use App\Permission;
use App\Role;
use Validator;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Kaynaktan bir liste görüntüler.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){

        $roles = Role::orderBy('updated_at', 'desc')->get();

        return view('role.index')->with('roles', $roles);
    }

    /**
     * Yeni bir kaynak oluşturmak için formu gösterir.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $customPermission = Custom_Permission::all();
        $this->data['customPermission'] = $customPermission;
        $this->data['permissions'] = Permission::all()->sortBy('title')->groupBy('controller');


        return view('role.create', $this->data);
    }

    /**
     * Yeni oluşturulan bir kaynağı database'e kayıt eder.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        try{
            $attribute = array(
                'title'         => 'İsim',
            );

            $rules = array(
                'title'            => 'required|unique:roles',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $role                 = new Role;
            $role->title          = $request->get('title');
            if($role->save()){
                $permissions = $this->arrayOnlyNumeric(explode(',', $request->get('permissions')));
                if(!empty($permissions)){
                    $role->permission()->attach($permissions);
                }
                $customPermission = $request->get('customPermission');
                if (empty($customPermission)) {
                    $role->custom_permission()->detach();
                }else{
                    $role->custom_permission()->sync($customPermission);
                }
            }

            session()->flash('flash_message', array('Başarılı!','Rol kaydedildi.', 'success'));
        }

        catch (\Exception $e){
            session()->flash('flash_message', array('Başarısız!','Hata! Lütfen tekrar deneyiniz.', 'error'));

        }

        return redirect()->route('role.index');
    }

    /**
     * Belirtilen kaynağı düzenlemek için formu gösterir.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customPermission = Custom_Permission::all();
        $this->data['role'] = Role::find($id);
        $this->data['permissions'] = Permission::all()->sortBy('title')->groupBy('controller');

        $roleSelectPermission = [];
        if($this->data['role']->permission){
            foreach ($this->data['role']->permission as $value)
            {
                array_push($roleSelectPermission, $value->id);
            }
            $this->data['roleSelectPermission'] = $roleSelectPermission;
        }
        $this->data['customPermission'] = $customPermission;
        if (Role::find($id)->custom_permission){
            $this->data['selectedCustomPermission'] = Role::find($id)->custom_permission->pluck('id')->toArray();
        }

        return view('role.edit', $this->data);
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
                'title'    => 'İsim',
            );

            $rules = array(
                'title'            => 'required|unique:roles,title,'.$id,
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->route('role.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }

            $role                 = Role::find($id);
            $role->title          = $request->get('title');
            if($role->save()){
                $permissions = $this->arrayOnlyNumeric(explode(',', $request->get('permissions')));
                if(empty($permissions)){
                    $role->permission()->detach();
                }else{
                    $role->permission()->sync($permissions);
                }
                $customPermission = $request->get('customPermission');
                if (empty($customPermission)) {
                    $role->custom_permission()->detach();
                }else{
                    $role->custom_permission()->sync($customPermission);
                }
            }

            session()->flash('flash_message', array('Başarılı!','Rol güncellendi.', 'success'));
        }

        catch (\Exception $e){

            session()->flash('flash_message', array('Başarısız!','Hata! Lütfen tekrar deneyiniz.', 'error'));
        }

        return redirect()->route('role.index');
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
            $role = Role::find($id);
            if($role->delete()){
                $role->permission()->detach();
            }

            session()->flash('flash_message', array('Başarılı!','Rol silindi.', 'success'));
        }

        catch (\Exception $e){

            session()->flash('flash_message', array('Başarısız!','Hata! Lütfen tekrar deneyiniz.', 'error'));

        }

        return redirect()->route('role.index');
    }

}
