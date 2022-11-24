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

class HistoryController extends Controller
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
         //linkten gelen get parametresi için kontrol ekleniyor
         $linkAttr = [];

         if($request->has('epc')){
             $linkAttr['epc'] = $request->get('epc');
         }else{
            if ($validator->fails()) {
                return redirect()->back();
            }
         }
        $this->data['historyDatatableLink'] = route('history.datatable', $linkAttr);

        return view('history.index', $this->data);
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

    public function datatable(Request $request)
    {
        $epc = $request->get('epc');
        $query =  DB::table('tsuka_cl_history')
        ->join('tsuka_branchs', 'tsuka_branchs.id', '=', 'tsuka_cl_history.branch_id')
        ->select('tsuka_branchs.name', 'tsuka_cl_history.epc', 'tsuka_cl_history.created_at')
        ->where("epc",$epc)
        ->orderBy('created_at','DESC')
        ->get();

        $arr = $query->toArray();
         
        return Datatables::of($query)
            ->rawColumns(['epc','name','created_at'])
            ->make(true);
    }

}
