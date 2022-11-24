<?php

namespace App\Http\Controllers;

use App\Company;
use App\Consignee;
use App\Consignment;
use App\Helpers\OptionTrait;
use App\Item;
use App\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;
use Yajra\DataTables\DataTables;

class OrderController extends Controller
{
    use OptionTrait;

    /**
     * Kaynaktan bir liste görüntüler.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Eğer sessiondaki kullanıcı admin ise ayırt etmeksizin tüm firmaların listesini görsün.
        if(roleCheck(config('settings.roles.admin')))
        {
            $companies = Company::where('status',1)->get();
        }
        //Eğer sessiondaki kullanıcı admin değilse ana üreticidir. Alt üretici zaten bu sayfayı göremiyor. Ana üretici ise sadece kendi ana firmasını ve kendi altındaki fason firmaları görsün.
        else
        {
            $companies = Company::where('status', true)->where('id',[auth()->user()->company_id])->orWhere('main_company_id',[auth()->user()->company_id])->get();
        }
        if($companies){
            $this->data['companies'] = $companies;
        }

        return view('order.index', $this->data);
    }

    /**
     * Yeni bir kaynak oluşturmak için formu gösterir.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $consignees = Consignee::where('status', true)->get();
        if($consignees){
            $this->data['consignees'] = $consignees;
        }

        return view('order.create', $this->data);
    }

    /**
     * Yeni oluşturulan bir kaynağı database'e kayıt eder.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            $attribute = array(
                'po_no'             => trans('portal.po_no'),
                'name'              => trans('portal.model_name'),
                'item_count'        => trans('portal.piece'),
                'consignee_id'      => trans('portal.consignee_name')
            );

            $rules = array(
                'po_no'             => 'required|unique:orders,po_no,NULL,id,deleted_at,NULL',
                'name'              => 'required',
                'item_count'        => 'required|numeric',
                'consignee_id'      => 'required'
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $order = new Order;
            $order->order_code          = $this->autoGenerateOrderCode();
            $order->consignee_id        = $request->get('consignee_id');
            $order->po_no               = $request->get('po_no');
            $order->name                = $request->get('name');
            $order->item_count          = $request->get('item_count');
            $order->created_user_id     = auth()->user()->id;
            $order->save();

            $this->createLog('Order', 'portal.log_create_order', ['name' => $order->po_no], $order->id);

            session()->flash('flash_message', array(trans('portal.successful'), trans('portal.err_add_order'), 'success'));

        } catch (\Exception $e) {
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
            return redirect()->back()->withInput();
        }

        return redirect()->route('order.index');
    }


    /**
     * Belirtilen kaynağı gösterir.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::with(['consignments'])->withCount('items')->find($id);
        if ($order) {
            $this->data['order'] = $order;
        }

        return View('order.show', $this->data);
    }
    /**
     * Belirtilen kaynağı düzenlemek için formu gösterir.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $order = Order::find($id);
        if($order){
            $this->data['order'] = $order;
        }

        $consignees = Consignee::where('status', true)->get();
        if($consignees){
            $this->data['consignees'] = $consignees;
        }

        return view('order.edit', $this->data);
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
        try {

            $attribute = array(
                //'po_no'             => trans('portal.po_no'),
                'name'              => trans('portal.model_name'),
                'item_count'        => trans('portal.piece'),
                'consignee_id'      => trans('portal.consignee_name')
            );

            $rules = array(
                //'po_no'             => "required|unique:orders,po_no,{$id},id,deleted_at,NULL",
                'name'              => 'required',
                'item_count'        => 'required|numeric',
                'consignee_id'      => 'required'
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $order                      = Order::find($id);
            //$order->order_code          = $this->autoGenerateOrderCode();
            $order->consignee_id        = $request->get('consignee_id');
            //$order->po_no               = $request->get('po_no');
            $order->name                = $request->get('name');
            $order->item_count          = $request->get('item_count');
            $order->updated_user_id     = auth()->user()->id;
            $order->save();


            $this->createLog('Order','portal.log_update_order', ['name' => $order->po_no], $order->id);

            session()->flash('flash_message', array(trans('portal.successful'), trans('portal.err_update_order'), 'success'));
            return redirect()->route('order.index');

        } catch (\Exception $error) {
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

            $order                      = Order::find($id);
            $order->updated_user_id     = auth()->user()->id;
            $order->save();
            $order->consignments()->delete();
            $order->packages()->delete();
            $order->items()->delete();
            $order->delete();

           $this->createLog('Order','portal.log_delete_order', ['name' => $order->po_no], $id);

           session()->flash('flash_message', array(trans('portal.successful'), trans('portal.err_delete_order'), 'success'));

       }catch (\Exception $error){
           session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
       }

        return redirect()->back();
    }

    public function datatable(Request $request)
    {
        // Burası alt üretici- ana üretici yapısına göre düzenlenmedi. Çünkü burayı zaten sadece admin gördüğü için, veritabanındaki tüm firmaların tüm siparişlerini getiriyor sorgu.
        $query = Order::query();

        //Filter
        if ($request->has('search.value') && $request->get('search')['value'] != '') {
            $text = $request->get('search')['value'];
            $query->orWhereHas('consignee', function($q) use ($text) {
                $q->where('name', 'like', "%" .$text. "%");
            });
            $query->orWhere('order_code', 'like', "%" .$text. "%");
            $query->orWhere('po_no', 'like', "%" .$text. "%");
            $query->orWhere('name', 'like', "%" .$text. "%");
        }

        //Sıralama
        if($request->has('order.0')){
            $dir    = $request->get('order')[0]['dir'];
            $column = $request->get('order')[0]['column'];
            $query->orderBy($request->get('columns')[$column]['name'], $dir);
        }else{
            $query->orderBy('updated_at', 'desc');
        }

        return Datatables::of($query)
            ->editColumn('order_code', function ($value){
                return '<a href="' . route('order.show', $value->id) . '">'. $value->order_code. '</a>';
            })
            ->editColumn('po_no', function ($value){
                return '<a href="' . route('order.show', $value->id) . '">'. $value->po_no. '</a>';
            })
            ->editColumn('consignee_id', function ($value){
                return $value->consignee ? $value->consignee->name : '-';
            })
            ->editColumn('created_at', function ($value){
                return getLocaleDate($value->created_at);
            })
            ->editColumn('status', function ($value){
                return $value->status == 1 ? '<span class="badge badge-success">'.trans('portal.opened').'</span>' : '<span class="badge badge-danger">'.trans('portal.closed').'</span>';
            })
            ->editColumn('action', function ($value){
                return '<span class="dropdown">
                                                        <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                                                          <i class="la la-cogs"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" id="addCons-'.$value->id.'" href="javascript:;" onclick="addConsignment(this);" data-order-code="'.$value->order_code.'" data-id="'.$value->id.'" data-toggle="modal" data-target="#addConsignment"><i class="la la-truck"></i> '.trans('portal.new_consignment').'</a>
                                                            <a class="dropdown-item" href="' . route('order.show', $value->id) . '"><i class="la la-search"></i> '.trans('portal.show').'</a>
                                                            <a class="dropdown-item" href="' . route('order.edit', $value->id) . '"><i class="la la-edit"></i> '.trans('portal.edit').'</a>
                                                            <a class="dropdown-item" href="' . route('order.destroy', $value->id) . '" data-method="delete" data-token="' . csrf_token() . '" data-confirm="'.trans('portal.delete_text').'"><i class="la la-trash"></i> '.trans('portal.delete').'</a>
                                                        </div>
                                                    </span>';
            })
            ->rawColumns(['order_code', 'po_no', 'action', 'status'])
            ->orderColumns(['order_code', 'po_no'], '-:column $1')
            ->make(true);
    }

    public function datatableDetails(Request $request)
    {
        $tr = '';

        $consignments = Consignment::where('order_id', $request->get('id'))->with(['items', 'order', 'company'])->get();
        if($consignments){
            foreach ($consignments as $key => $value){
                $tr .= "<tr>";
                $tr .= "<td><a href='".route('consignment.show', $value->id) ."'>".$value->name."</a></td>";
                $tr .= "<td>".$value->company->name."</td>";
                $tr .= "<td>".$value->item_count." / ".count($value->items)."</td>";
                $tr .= "<td>".$value->delivery_date."</td>";
                $tr .= "<td>".$value->created_at."</td>";
                if($value->status == 1){
                    $tr .= '<td class="text-center"><span class="badge badge-success">'.trans('portal.opened').'</span></td>';
                }else{
                    $tr .= '<td class="text-center"><span class="badge badge-danger">'.trans('portal.closed').'</span></td>';
                }
                $tr .= '<td class="text-center"><span class="dropdown">
                                                        <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                                                          <i class="la la-cogs"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="' . route('consignment.show', $value->id) . '"><i class="la la-search"></i> '.trans('portal.show').'</a>
                                                            <a class="dropdown-item" href="' . route('consignment.edit', $value->id) . '"><i class="la la-edit"></i> '.trans('portal.edit').'</a>
                                                            <a class="dropdown-item" href="' . route('consignment.destroy', $value->id) . '" data-method="delete" data-token="' . csrf_token() . '" data-confirm="'.trans('portal.delete_text').'"><i class="la la-trash"></i> '.trans('portal.delete').'</a>
                                                        </div>
                                                    </span></td>';
                $tr .= "</tr>";
            }
        }

        return  response()->json($tr);
    }

}
