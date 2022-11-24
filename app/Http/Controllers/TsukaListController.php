<?php

namespace App\Http\Controllers;

use App\Company;
use App\Package;
use App\TsukaCountingList;
use App\TsukaClDetails;
use App\Helpers\NotificationTrait;
use App\Order;
use App\Helpers\OptionTrait;
use Illuminate\Http\Response;
use Validator;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class TsukaListController extends Controller
{
    use OptionTrait;
    use NotificationTrait;

    /**
     * Kaynaktan bir liste görüntüler.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $linkAttr = [];
        //linkten gelen get parametresi için kontrol ekleniyor
            
        $this->data['consignmentDatatableLink'] = route('tsukalists.datatable', $linkAttr);

        return view('tsukalists.index', $this->data);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('tsukalists.create');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        try {

            $attribute = array(
                'name'              => 'Name'
            );

            $rules = array(
                'name'              => 'required'
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $countingList = new TsukaCountingList;
            $countingList->name              = $request->get('name');
            
            $countingList->save();

        } catch (\Exception $e) {
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
            return redirect()->back()->withInput();
        }

        return redirect()->route('tsukalists.index');
    }

    public function InsertListDetails(Request $request)
    {
        ini_set('memory_limit','-1');
        ini_set('max_execution_time', '-1');

        $validator = Validator::make($request->all(), [
            'hdnListId'     => 'required'
        ]);

        if ($validator->fails()) {
            return $this->apiResponse([], '02', config('webservice.error_codes.02'));
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
        $countingList = TsukaCountingList::findOrFail($id);
        $this->data['countingList'] = $countingList;
        return View('tsukalists.show', $this->data);
    }

    /**
     * Belirtilen kaynağı düzenlemek için formu gösterir.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
            $countingList = TsukaCountingList::findOrFail($id);

        if($countingList){
            $this->data['countingList'] = $countingList;
        }
        return view('tsukalists.edit', $this->data);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        try {

            $attribute = array(
                'name'              => 'Name'
            );

            $rules = array(
                'name'              => 'required'
            );
            
            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $countingList = TsukaCountingList::findOrFail($id);

            $countingList->name              = $request->get('name');
            $countingList->save();

        } catch (\Exception $e) {
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
            return redirect()->back()->withInput();
        }

        return redirect()->route('tsukalists.index');
    }

    /**
     * Belirtilen kaynağı database üzerinden kaldırır.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        try {
            $countingList = TsukaCountingList::findOrFail($id);
            
            $countingList->delete();

        } catch (\Exception $e) {
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
        }

        return redirect()->back();
    }

    public function datatable(Request $request)
    {
         $query =  DB::table('tsuka_counting_lists')->where("deleted_at",null)->orderBy('created_at','DESC')->get();
         
        return Datatables::of($query)
            ->editColumn('name', function ($value){
                return '<a href="' . route('tsukalists.show', $value->id) . '">'. $value->name. '</a>';
            })
            ->editColumn('action', function ($value){
                return '<span class="dropdown">
                                                        <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                                                          <i class="la la-cogs"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="' . route('tsukalists.show', $value->id) . '"><i class="la la-search"></i> '.trans('portal.show').'</a>
                                                            <a class="dropdown-item" href="' . route('tsukalists.edit', $value->id) . '"><i class="la la-edit"></i> '.trans('portal.edit').'</a>
                                                            <a class="dropdown-item" href="' . route('tsukalists.destroy', $value->id) . '" data-method="delete" data-token="' . csrf_token() . '" data-confirm="'.trans('portal.delete_text').'"><i class="la la-trash"></i> '.trans('portal.delete').'</a>
                                                        </div>
                                                    </span>';
            })
            ->rawColumns(['name', 'action', 'status', 'item_count'])
            ->make(true);
    }

}
