<?php

namespace App\Http\Controllers;

use App\Company;
use App\Custom_Permission;
use App\Permission;
use App\Role;
use App\User;
use App\ViewScreen;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Hash;
use Str;

class ViewScreenController extends Controller{
    
    /**
     * Kaynaktan bir liste görüntüler.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){

        return view('viewScreen.index', $this->data);

    }

    /**
     * Belirtilen kaynağı düzenlemek için formu gösterir.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){

        $data = ViewScreen::find($id);

        return View('viewScreen.edit')->with('view', $data);

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

            $attribute = array(
                'name' => trans('portal.viewname'),
                'status' => trans('portal.viewstatus'),
                'reading' => trans('portal.viewreading')
            );

            $rules = array(
                'name' => 'required|string|max:255',
                'status' => 'required|string|max:255',
                'reading' => 'required|string|max:255'
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $data = ViewScreen::find($id);
            $data->name = $request->get('name');
            $data->status = $request->get('status');
            $data->reading = $request->get('reading');
            $data->save();

            session()->flash('flash_message', array(trans('portal.successful'), trans('portal.err_update_view'), 'success'));

        }catch (\Exception $e){
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
        }

        return redirect()->route('view_screen.index');

    }

     /**
     * Kaynaktan bir json liste görüntüler.
     *
     * @return \Illuminate\Http\Response
     */
    public function datatable(Request $request){

        $query = ViewScreen::query();

        return Datatables::of($query)
        ->editColumn('viewname', function ($value){
            return $value->name ? $value->name : '-';
        })
        ->editColumn('viewstatus', function ($value){
            return $value->status ? $value->status : '-';
        })
        ->editColumn('action', function ($value){
            return '<span class="dropdown">
                <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                    <i class="la la-cogs"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="' . route('view_screen.edit', $value->id) . '">
                        <i class="la la-edit"></i> '.trans('portal.edit').'
                    </a>
                </div>
            </span>';
        })->rawColumns(['action', 'status'])->make(true);
    }
    
}
