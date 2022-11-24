<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationTrait;
use App\Helpers\OptionTrait;
use Illuminate\Http\Request;
use Validator;
use Yajra\DataTables\DataTables;

class AjaxController extends Controller
{

    use OptionTrait;
    use NotificationTrait;

    public function index(Request $request)
    {


        switch ($request->get('process')) {

            case 'checkConsignment' :

                if($request->has('consignmentId')){

                    $consignmentId = $request->get('consignmentId');

                    $consignment = \App\Consignment::withCount(['items'])->find($consignmentId);

                    return  response()->json(['consignment' => $consignment]);
                }else{
                    return response()->json(['message' => 'Sevkiyat Bulunamadı'], 400);
                }

                break;

            case 'checkConsignmentHome' :

                $company_id = auth()->user()->company_id;

                $open_consignments = \App\Consignment::where('company_id', $company_id)
                    ->where('status', true)
                    ->orderBy('updated_at', 'desc')
                    ->with(['consignee'])
                    ->withCount('items')
                    ->get();

                $company = \App\Company::withCount(['consignments', 'packages', 'items', 'consignees', 'users', 'devices'])->find($company_id);

                return  response()->json(['company' => $company, 'openConsignments' => $open_consignments]);
                break;

            case 'getPackages' :

                $query = \App\Package::select(['id','package_no','size','model','created_at','status', 'created_user_id'])->with(['device', 'created_user'])->withCount(['items'])->where('consignment_id', $request->get('consignmentId'));

                //Sıralama
                if($request->has('order.0')){
                    $dir    = $request->get('order')[0]['dir'];
                    $column = $request->get('order')[0]['column'];

                    $query->orderBy($request->get('columns')[$column]['name'], $dir);
                }else{
                    $query->orderBy('package_no', 'asc');
                }

                return Datatables::of($query)
                    ->editColumn('package_no', function ($value){
                        return trans('portal.package')." " . $value->package_no;
                    })
                    ->editColumn('items', function ($value){
                        return $value->items_count. " ". trans('portal.piece');
                    })
                    ->editColumn('status', function ($value){
                        return $value->status == 1 ? '<span class="badge badge-success">'.trans('portal.opened').'</span>' : '<span class="badge badge-danger">'.trans('portal.closed').'</span>';
                    })
                    ->editColumn('model', function ($value){
                        return $value->model != '' ? $value->model : '-';
                    })
                    ->editColumn('size', function ($value){
                        return $value->size != '' ? $value->size : '-';
                    })
                    ->editColumn('create_date', function ($value){
                        return $value->created_at;
                    })
                    ->editColumn('created_user', function ($value){
                        return $value->created_user ? $value->created_user->name : '-';
                    })
                    ->rawColumns(['status'])
                    ->make(true);

                break;

            case 'getItems' :

                $tr = '';

                $query = \App\Item::select(['id','epc','size','device_id'])->with(['device'])->where('package_id', $request->get('packageId'))->get();
                if($query){
                    foreach ($query as $key => $value){
                        $tr .= "<tr>";
                        $tr .= "<td>".($key + 1)."</td>";
                        $tr .= "<td>".$value->epc."</td>";
                        if($value->size != ''){
                            $tr .= "<td>".$value->size."</td>";
                        }else{
                            $tr .= "<td>-</td>";
                        }
                        $tr .= "<td>".config('settings.devices.'.$value->device->device_type.'.name')."</td>";
                        $tr .= "</tr>";

                    }
                }

                return  response()->json($tr);

                break;

            case 'saveConsignment':
                $response = [];

                try {

                    $attribute = array(
                        'order_id'              => 'Sipariş',
                        'company_id'            => 'Firma',
                        'item_count'            => 'Adet',
                        'delivery_date'         => 'Teslimat Tarihi',
                    );

                    $rules = array(
                        'order_id'              => 'required',
                        'company_id'            => 'required',
                        'item_count'            => 'required',
                        'delivery_date'         => 'required',
                    );

                    $validator = Validator::make($request->all(), $rules);
                    $validator->setAttributeNames($attribute);

                    if ($validator->fails()) {
                        $response = [
                            'status'    => 'error',
                            'title'     => 'Başarısız!',
                            'text'      => 'Hata! Lütfen tekrar deneyiniz.'
                        ];
                        return response()->json($response);
                    }

                    $consignment = new \App\Consignment;
                    $consignment->order_id              = $request->get('order_id');
                    $consignment->company_id            = $request->get('company_id');
                    $consignment->name                  = $this->autoGeneratePoNo($request->get('order_id'));
                    $consignment->item_count            = $request->get('item_count');
                    $consignment->plate_no              = $request->get('plate_no');
                    $consignment->delivery_date         = $request->get('delivery_date');
                    $consignment->consignee_id          = $this->getConsigneeId($request->get('order_id'));
                    $consignment->created_user_id       = auth()->user()->id;
                    
                    if($consignment->save()){
                        if($request->hasFile('db_list')){
        
                            CompanyDb::where('company_id', $id)->delete();
        
                            $path = $_FILES["db_list"]['tmp_name'];//$request->file('db_list')->getRealPath();
                            $this->get_absolute_path($path);
        
                            $inx = 0;
                            foreach(file($path) as $key => $value){
                                $line = explode(';', $value);
                                if(is_array($line)){
                                    if($inx != 0)
                                    {
                                        $data = [
                                            'company_id'        => $consignment->id,
                                            'ean_code'          => $line[0],
                                            'model_code'        => $line[1],
                                            'item_code'         => $line[2],
                                            'item_desc'         => $line[3],
                                            'size'              => $line[4],
                                            'pcb'               => $line[5],
                                            'user_id'           => auth()->user()->id
                                        ];
        
                                        if(isset($line[6])){
                                            $data['status'] =  $line[6];
                                        }
                                        CompanyDb::create($data);
                                    }
                                    $inx++;
                                }
                            }
                        }
                    }

                    $this->createLog('Consignment','portal.log_create_consignment', ['name' => $consignment->name, 'date' => $consignment->delivery_date], $consignment->id);

                    $this->createNotification('station.notification_create_shipment', ['name' => $consignment->name, 'date' => $consignment->delivery_date]);

                    $response = [
                        'status'    => 'success',
                        'title'     => 'Başarılı!',
                        'text'      => 'Sevkiyat kaydedildi..'
                    ];

                } catch (\Exception $e) {
                    $response = [
                        'status'    => 'error',
                        'title'     => 'Başarısız!',
                        'text'      => 'Hata! Lütfen tekrar deneyiniz.',
                        'message'   => $e
                    ];
                }

                return response()->json($response);
        }


    }

}
