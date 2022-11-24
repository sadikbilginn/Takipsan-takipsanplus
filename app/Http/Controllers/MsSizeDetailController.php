<?php

namespace App\Http\Controllers;

use App\Company;
use App\Custom_Permission;
use App\Permission;
use App\Role;
use App\User;
use App\CompanyMsDb;
use App\Consignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Hash;
use Str;

class MsSizeDetailController extends Controller{
    
    /**
    * Kaynaktan bir liste görüntüler.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(){

        return view('msSizeDetail.index');

    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create(){

        $this->data['consignments'] = Consignment::all()->where("status", 1);
        
        return view('msSizeDetail.create', $this->data);

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
                'consignment_id' => trans('portal.consignment'),
                'order' => trans('portal.po_no'),
                'season' => 'Season',
                'description' => trans('portal.name'),
                'primary_size' => 'Primary Size',
                'secondary_size' => 'Secondary Size',
                'upc' => 'Upc',
                'story_description' => 'Story Description',
                'price' => 'Actual Selling Price',
                'qty_req' => 'Qty Req',
            );

            $rules = array(
                'consignment_id' => 'required',
                'order' => 'nullable',
                'season' => 'nullable',
                'description' => 'required',
                'primary_size' => 'nullable',
                'secondary_size' => 'nullable',
                'upc' => 'required',
                'story_description' => 'nullable',
                'price' => 'nullable',
                'qty_req' => 'nullable',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $primarySize = $request->get('primary_size');
            $secondarySize = $request->get('secondary_size');

            if ($primarySize != "" && $secondarySize != ""){
                $sdsCode = $primarySize.'/'.$secondarySize;
            }elseif ($primarySize != "" && $secondarySize == ""){
                $sdsCode = $primarySize;
            }elseif ($primarySize == "" && $secondarySize != ""){
                $sdsCode = $secondarySize;
            }

            $data = [
                'consignment_id' => $request->get('consignment_id'),
                'order' => $request->get('order'),
                'season' => $request->get('season'),
                'description' => $request->get('description'),
                'sds_code' => $sdsCode,
                'upc' => $request->get('upc'),
                'storyDesc' => $request->get('story_description'),
                'price' => $request->get('price'),
                'qtyReq' => $request->get('qty_req'),
                'user_id' => auth()->user()->id
            ];
            
            CompanyMsDb::create($data);

            session()->flash(
                'flash_message', 
                array(trans('portal.successful'), trans('portal.err_add_consignment'), 'success')
            );

        }

        catch (\Exception $e){
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
            //return redirect()->back()->withInput();
        }

        return redirect()->route('ms_size_detail.index');
    }

    /**
    * Belirtilen kaynağı düzenlemek için formu gösterir.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function edit($id){

        $consignmentsDetail = CompanyMsDb::find($id);
        $this->data['consignmentsDetail'] = $consignmentsDetail;
        $this->data['consignments'] = Consignment::all()->where("status", 1);
        
        return View('msSizeDetail.edit', $this->data);

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
                'consignment_id' => trans('portal.consignment'),
                'order' => trans('portal.po_no'),
                'season' => 'Season',
                'description' => trans('portal.name'),
                'primary_size' => 'Primary Size',
                'secondary_size' => 'Secondary Size',
                'upc' => 'Upc',
                'story_description' => 'Story Description',
                'price' => 'Actual Selling Price',
                'qty_req' => 'Qty Req',
            );

            $rules = array(
                'consignment_id' => 'required',
                'order' => 'nullable',
                'season' => 'nullable',
                'description' => 'required',
                'primary_size' => 'nullable',
                'secondary_size' => 'nullable',
                'upc' => 'required',
                'story_description' => 'nullable',
                'price' => 'nullable',
                'qty_req' => 'nullable',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $primarySize = $request->post('primary_size');
            $secondarySize = $request->post('secondary_size');

            if ($primarySize != "" && $secondarySize != ""){
                $sdsCode = $primarySize.'/'.$secondarySize;
            }elseif ($primarySize != "" && $secondarySize == ""){
                $sdsCode = $primarySize;
            }elseif ($primarySize == "" && $secondarySize != ""){
                $sdsCode = $secondarySize;
            }

            $data = CompanyMsDb::find($id);
            $data->consignment_id = $request->get('consignment_id');
            $data->order = $request->get('order');
            $data->season = $request->get('season');
            $data->description = $request->get('description');
            $data->sds_code = $sdsCode ? $sdsCode : null;
            $data->upc = $request->get('upc');
            $data->story_desc = $request->get('story_desc');
            $data->price = $request->get('price');
            $data->qty_req = $request->get('qty_req');
            $data->user_id = auth()->user()->id;
            $data->save();

            // $data->name = $request->get('name');
            // $data->status = $request->get('status');
            // $data->reading = $request->get('reading');

            session()->flash('flash_message', array(trans('portal.successful'), trans('portal.err_update_view'), 'success'));

        }catch (\Exception $e){
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
        }

        return redirect()->route('ms_size_detail.index');

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
            
            $data = CompanyMsDb::find($id);
            $data->delete();
            
            session()->flash(
                'flash_message', 
                array(trans('portal.successful'), trans('portal.err_delete_consignment'), 'success')
            );

        }catch (\Exception $e){
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
        }
        
        return redirect()->route('ms_size_detail.index');

    }

    /**
    * Kaynaktan bir json liste görüntüler.
    *
    * @return \Illuminate\Http\Response
    */
    public function datatable(Request $request){

        $query = CompanyMsDb::query()->orderBy('id', 'desc');

        return Datatables::of($query)
        ->editColumn('season', function ($value){
            return $value->season ? $value->season : '-';
        })
        ->editColumn('description', function ($value){
            return $value->description ? $value->description : '-';
        })
        ->editColumn('upc', function ($value){
            return $value->upc ? $value->upc : '-';
        })
        ->editColumn('action', function ($value){
            return '<span class="dropdown">
                <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                    <i class="la la-cogs"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="' . route('ms_size_detail.edit', $value->id) . '">
                        <i class="la la-edit"></i> '.trans('portal.edit').'
                    </a>
                    <a 
                        class="dropdown-item" 
                        href="' . route('ms_size_detail.destroy', $value->id) . '" 
                        data-method="delete" 
                        data-token="' . csrf_token() . '" 
                        data-confirm="'.trans('portal.delete_text').'"
                    >
                        <i class="la la-trash"></i> '.trans('portal.delete').'
                    </a>
                </div>
            </span>';
        })->rawColumns(['action', 'status'])->make(true);
    }
    
}
