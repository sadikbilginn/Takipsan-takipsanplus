<?php

namespace App\Http\Controllers;

use App\Custom_Permission;
use App\Permission;
use App\User;
use Illuminate\Http\Request;
use Validator;

class CustomPermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Custom_Permission::all();
        return view('custom_permission.index',compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::all();
        $permissions = Permission::all();
        return view('custom_permission.create',compact('users','permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $attribute = array(
                'name'                 => 'İsim',
            );

            $rules = array(
                'name'                 => 'required|unique:custom_permission',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $permission                 = new Custom_Permission;
            $permission->name          = $request->get('name');
            $permission->save();

            session()->flash('flash_message', array('Başarılı!','İzin kaydedildi.', 'success'));

        }

        catch (\Exception $e){

            session()->flash('flash_message', array('Başarısız!','Hata! Lütfen tekrar deneyiniz.', 'error'));
        }

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission = Custom_Permission::find($id);
        return view('custom_permission.edit',compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $attribute = array(
                'name'                 => 'İsim',
            );

            $rules = array(
                'name'                 => 'required|unique:custom_permission',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $permission                 = Custom_Permission::find($id);
            $permission->name          = $request->get('name');
            $permission->save();

            session()->flash('flash_message', array('Başarılı!','İzin kaydedildi.', 'success'));

        }

        catch (\Exception $e){

            session()->flash('flash_message', array('Başarısız!','Hata! Lütfen tekrar deneyiniz.', 'error'));
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $permission = Custom_Permission::find($id);
            $permission->delete();

            session()->flash('flash_message', array('Başarılı!','İzin silindi.', 'success'));
        }

        catch (\Exception $e){

            session()->flash('flash_message', array('Başarısız!','Hata! Lütfen tekrar deneyiniz.', 'error'));

        }

        return redirect()->route('custompermission.index');
    }
}
