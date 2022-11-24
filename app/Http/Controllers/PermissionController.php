<?php

namespace App\Http\Controllers;

use App\Permission;
use Validator;
use Illuminate\Http\Request;

class PermissionController extends Controller
{

    /**
     * Kaynaktan bir liste görüntüler.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){

        $permissions = Permission::orderBy('updated_at', 'desc')->get();

        return View('permission.index')->with('permissions', $permissions);
    }

    /**
     * Yeni bir kaynak oluşturmak için formu gösterir.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('permission.create');
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
                'title'         => 'İsim',
                'method'        => 'Method',
                'controller'    => 'Controller',
                'action'        => 'Action'
            );

            $rules = array(
                'title'            => 'required|unique:permissions',
                'method'           => 'required',
                'controller'       => 'required',
                'action'           => 'required'
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $permission                 = new Permission;
            $permission->title          = $request->get('title');
            $permission->method         = collect($request->get('method'))->implode(',');
            $permission->controller     = $request->get('controller');
            $permission->action         = $request->get('action');
            $permission->save();

            session()->flash('flash_message', array(trans('portal.successful'), trans('portal.permission_added_succesfully'), 'success'));

        }

        catch (\Exception $e){

            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
        }

        return redirect()->route('permission.index');
    }

    /**
     * Belirtilen kaynağı düzenlemek için formu gösterir.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission = Permission::find($id);

        return View('permission.edit')->with('permission', $permission);

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
                'title'         => 'İsim',
                'method'        => 'Method',
                'controller'    => 'Controller',
                'action'        => 'Action'
            );

            $rules = array(
                'title'            => 'required|unique:permissions,title,'.$id,
                'method'           => 'required',
                'controller'       => 'required',
                'action'           => 'required'
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $permission                 = Permission::find($id);
            $permission->title          = $request->get('title');
            $permission->method         = collect($request->get('method'))->implode(',');
            $permission->controller     = $request->get('controller');
            $permission->action         = $request->get('action');
            $permission->save();

            session()->flash('flash_message', array(trans('portal.successful'), trans('portal.permission_updated_succesfully'), 'success'));
        }

        catch (\Exception $e){

            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));

        }

        return redirect()->route('permission.index');
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
            $permission = Permission::find($id);
            $permission->delete();

            session()->flash('flash_message', array(trans('portal.successful'), trans('portal.permission_deleted_succesfully'), 'success'));
        }

        catch (\Exception $e){

            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));

        }

        return redirect()->route('permission.index');
    }

}
