<?php

namespace App\Http\Controllers;

use App\Company;
use App\MsUpcCarton;
use Illuminate\Support\Facades\Auth;
use App\Consignment;
use App\Helpers\NotificationTrait;
use App\Order;
use App\CountryListModel;
use App\CompanyDb;
use App\CompanyMsDb;
use App\CompanyLevisDb;
use App\ReadFile;
use App\ViewScreen;
use Illuminate\Support\Facades\DB;
use App\Helpers\OptionTrait;
use Illuminate\Http\Response;
//use Validator;

use Validator,Image;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Shuchkin\SimpleXlsx\SimpleXLSX;
use Smalot\PdfParser\Parser;
use Shuchkin\SimpleXLS;
use App\ParserConfig;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Cache\Factory;

class ConsignmentController extends Controller{

    use OptionTrait;
    use NotificationTrait;
    /**
    * Kaynaktan bir liste görüntüler.
    *
    * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    */
    public function index(Request $request){

        //linkten gelen get parametresi için kontrol ekleniyor
        $linkAttr = [];
        $validator = Validator::make($request->all(), [
            'company' => 'numeric',
            'consignee' => 'numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back();
        }

        if($request->has('company')){
            $linkAttr['company'] = $request->get('company');
        }

        if($request->has('consignee')){
            $linkAttr['consignee'] = $request->get('consignee');
        }

        $this->data['consignmentDatatableLink'] = route('consignment.datatable', $linkAttr);

        return view('consignment.index', $this->data);

    }

    /**
    * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    */
    public function create(){

        $companies = Company::where('status', true)->get();
        if($companies){
            $this->data['companies'] = $companies;
        }

        $orders = Order::where('status', true)->get();
        if($orders){
            $this->data['orders'] = $orders;
        }

        $company = DB::table('company_consignee')
                    ->select('*')
                    ->join('consignees', function ($join) {
                        $join->on('company_consignee.consignee_id', '=', 'consignees.id');
                    })
                    ->where('company_consignee.company_id', auth()->user()->company_id)
                    ->where('status', 1)
                    ->orderBy('id', 'ASC')
                    ->get();
        if ($company){
            $this->data['company'] = $company;
        }

        return view('consignment.create', $this->data);

    }

    /**
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */
    public function store(Request $request){

        ini_set('memory_limit','-1');
        ini_set('max_execution_time', '-1');

        switch ($request->get('process')) {

            case 'consigmentZara':

                $company = \App\Company::with(['consignees'])->find(auth()->user()->company_id);
                if($company){
                    $this->data['company'] = $company;
                }

                $this->data['consignee'] = $request->get('consingneeId');

                return response()->json(
                    ['status' => 'ok', 'html' => view('consignment.read.consignmentZara', $this->data)->render()]
                );

                break;

            case 'consigmentHm':

                $company = \App\Company::with(['consignees'])->find(auth()->user()->company_id);
                $country_list = DB::table('country_list')->orderBy('country_list_name', 'ASC')->get();

                if($company){
                    $this->data['company'] = $company;
                }

                $this->data['country_list'] = $country_list;
                $this->data['consignee'] = $request->get('consingneeId');

                return response()->json(
                    ['status' => 'ok', 'html' => view('consignment.read.consignmentHm', $this->data)->render()]
                );

                break;

            case 'consigmentMs':

                $company = \App\Company::with(['consignees'])->find(auth()->user()->company_id);
                $country_list = DB::table('country_list')->orderBy('country_list_name', 'ASC')->get();

                if($company){
                    $this->data['company'] = $company;
                }

                $this->data['country_list'] = $country_list;
                $this->data['consignee'] = $request->get('consingneeId');

                return response()->json(
                    ['status' => 'ok', 'html' => view('consignment.read.consignmentMs', $this->data)->render()]
                );

                break;

            case 'consigmentDc':

                $company = \App\Company::with(['consignees'])->find(auth()->user()->company_id);
                if($company){
                    $this->data['company'] = $company;
                }

                $this->data['consignee'] = $request->get('consingneeId');

                return response()->json(
                    ['status' => 'ok', 'html' => view('consignment.read.consignmentDc', $this->data)->render()]
                );

                break;

            case 'consigmentHb':

                $company = \App\Company::with(['consignees'])->find(auth()->user()->company_id);
                $country_list = DB::table('country_list')->orderBy('country_list_name', 'ASC')->get();
                if($company){
                    $this->data['company'] = $company;
                }

                $this->data['country_list'] = $country_list;
                $this->data['consignee'] = $request->get('consingneeId');

                return response()->json(
                    ['status' => 'ok', 'html' => view('consignment.read.consignmentHb', $this->data)->render()]
                );

                break;

            case 'consigmentLevis':

                $company = \App\Company::with(['consignees'])->find(auth()->user()->company_id);
                $country_list = DB::table('country_list')->orderBy('country_list_name', 'ASC')->get();
                if($company){
                    $this->data['company'] = $company;
                }

                $this->data['country_list'] = $country_list;
                $this->data['consignee'] = $request->get('consingneeId');

                return response()->json(
                    ['status' => 'ok', 'html' => view('consignment.read.consignmentLevis', $this->data)->render()]
                );

                break;

            case 'consigmentZaraStore':

                try {

                    $attribute = array(
                        'po_no' => trans('station.po_number'),
                        'name' => trans('station.name'),
                        'item_count' => trans('station.product_quantity'),
                        'delivery_date' => trans('station.delivery_date'),
                        'plate_no' => trans('station.plate_no'),
                        'company_name' => trans('station.company_name')
                    );

                    $rules = array(
                        'po_no' => 'required',
                        'name' => 'nullable',
                        'item_count' => 'required|numeric|min:1',
                        'delivery_date' => 'required|date|date_format:Y-m-d',
                        'plate_no' => 'nullable',
                        'company_name' => 'nullable'
                    );

                    $validator = Validator::make($request->all(), $rules);
                    $validator->setAttributeNames($attribute);
                    if ($validator->fails()) {
                        return response()->json([
                            'status' => false,
                            'errors' => $validator->getMessageBag()->toArray()
                        ]);
                    }

                    $order = \App\Order::where('po_no', $request->get('po_no'))->first();
                    if(!$order){

                        $order = new \App\Order;
                        $order->order_code = $this->autoGenerateOrderCode();
                        $order->consignee_id = $request->get('consignee_id') != 'other' ? $request->get('consignee_id') : $company->id;
                        $order->po_no = $request->get('po_no');
                        $order->name = $request->get('company_name') ?? 'Takipsan';
                        $order->item_count = $request->get('item_count');
                        $order->created_user_id = auth()->user()->id;
                        $order->save();

                    }

                    if($order){

                        $this->createLog('Order','portal.log_create_order', ['name' => $order->po_no], $order->id);

                        if ($request->get('company_name')){

                            $consigneeFind = $company->id;

                        }else{

                            $consigneeFind = $request->get('consignee_id');

                        }

                        $consigneeName = \App\Consignee::find((int) $consigneeFind);
                        if ($consigneeName){
                            $conName = $consigneeName->name;
                        }else{
                            //$conName = $request->get('company_name');
                            $conName = $company->name;
                        }

                        $consignment = new \App\Consignment;

                        $consignment->order_id = $order->id;
                        //$consignment->country_code = "RUW337";
                        $consignment->company_id = auth()->user()->company_id;
                        $consignment->name = $this->autoGeneratePoNo($order->id, $consignment->country_code, $conName);
                        $consignment->delivery_date = date('Y-m-d', strtotime($request->get('delivery_date')));
                        $consignment->consignee_id = $request->get('consignee_id') != 'other' ? $request->get('consignee_id') : $company->id;
                        $consignment->item_count = $request->get('item_count');
                        $consignment->created_user_id = auth()->user()->id;
                        if ($consignment->save()){

                            $pox = explode('/', $consignment->name);
                            if (is_array($pox)){
                                $order->po_no = $pox[0];
                                $order->save();
                            }

                        }

                        $this->createLog(
                            'Consignment','portal.log_create_consignment',
                            ['name' => $consignment->name, 'date' => $consignment->delivery_date], $consignment->id
                        );
                        $this->createNotification(
                            'station.notification_create_shipment',
                            [
                                'name' => $consignment->name,
                                'date' => $consignment->delivery_date,
                                'user_id' => auth()->user()->id,
                                'company_id' => auth()->user()->company_id
                            ]
                        );

                        session()->flash(
                            'flash_message',
                            array(trans('portal.successful'), trans('portal.err_add_consignment'), 'success')
                        );

                        return response()->json([
                            'status' => 'ok',
                            'consignmentId' => $consignment->id,
                            'url' => route('consignment.index')
                        ]);

                        //return redirect()->route('consignment.index');

                    }

                }catch (\Exception $e) {

                    session()->flash(
                        'flash_message',
                        array(trans('station.failed'),trans('station.error_text'), 'error')
                    );
                    return redirect()->back()->withInput();

                }

                //return redirect()->route('consignment.index');

                break;

            case 'consigmentHmStore':

                try {

                    $attribute = array(
                        //'po_no' => trans('station.po_number'),
                        'country_code' => trans('station.country'),
                        'item_count' => trans('station.product_quantity'),
                        //'consignee_id' => trans('station.consignee_name'),
                        'delivery_date' => trans('station.delivery_date'),
                        //'plate_no' => trans('station.plate_no')
                    );

                    $rules = array(
                        // 'po_no' => 'required',
                        'country_code' => 'required',
                        'item_count' => 'required|numeric|min:1',
                        // 'consignee_id' => 'required',
                        'delivery_date' => 'required|date|date_format:Y-m-d',
                        // 'plate_no' => 'nullable',
                    );

                    $validator = Validator::make($request->all(), $rules);
                    $validator->setAttributeNames($attribute);
                    if ($validator->fails()) {
                        return response()->json([
                            'status' => false,
                            'errors' => $validator->getMessageBag()->toArray()
                        ]);
                    }

                    $order = new \App\Order;

                    $order->order_code = $this->autoGenerateOrderCode();
                    $order->consignee_id = $request->get('sticker');
                    //$order->po_no = $request->get('po_no');
                    $order->name = $request->get('name') ?? "null";
                    $order->item_count = $request->get('item_count');
                    $order->created_user_id = auth()->user()->id;
                    $order->save();

                    if($order){

                        $this->createLog('Order','portal.log_create_order', ['name' => $order->po_no], $order->id);

                        $consignment = new \App\Consignment;
                        $consignment->order_id = $order->id;
                        $consignment->country_code = $request->get('country_code');
                        $consignment->company_id = auth()->user()->company_id;
                        //$consignment->name = $this->autoGeneratePoNo($order->id,$consignment->country_code);
                        // $consignment->plate_no = strtoupper(str_replace(" ", "", $request->get('plate_no')));
                        $consignment->item_count = $request->get('item_count');
                        $consignment->delivery_date = $request->get('delivery_date');
                        $consignment->consignee_id = $request->get('sticker');
                        $consignment->created_user_id = auth()->user()->id;
                        if($consignment->save()){

                            if($request->hasFile('db_list')){

                                $path = $_FILES["db_list"]['tmp_name'];
                                //$request->file('db_list')->getRealPath();
                                $this->get_absolute_path($path);
                                $inx = 0;
                                $model = "";
                                foreach(file($path) as $key => $value){

                                    $line = explode(';', $value);
                                    if(is_array($line)){

                                        if($inx != 0){

                                            CompanyDb::where('gtin',$line[0])->delete();
                                            $gtin = $line[4];

                                            if($gtin[0] == '0'){
                                                $gtin = substr($gtin, 1, strlen($gtin));
                                            }

                                            $order->po_no = $line[0];
                                            $order->season = $line[1];
                                            $model = explode(',', $line[7])[1];
                                            $data = [
                                                'consignment_id' => $consignment->id,
                                                'order' => $line[0],
                                                'season' => $line[1],
                                                'product' => $line[2],
                                                'variant' => $line[3],
                                                'gtin' => $gtin,
                                                'article_number' => $line[5],
                                                'sds_code' => $model,//$line[6],
                                                'description' => $line[7],
                                                'user_id' => auth()->user()->id,
                                            ];

                                            CompanyDb::create($data);

                                        }

                                        $inx++;

                                    }

                                }

                                $order->save();

                                $consigneeName = \App\Consignee::find((int) $request->get('sticker'));
                                if ($consigneeName){
                                    $conName = $consigneeName->name;
                                }
                                //$consignment->name = '_sad';
                                $consignment->name = $this->autoGeneratePoNo($order->id, $request->get('country_code'), $conName);

                                // echo ($consignment->name);
                                // exit();
                                $consignment->save();

                            }

                        }

                        $this->createLog(
                            'Consignment','portal.log_create_consignment',
                            ['name' => $consignment->name, 'date' => $consignment->delivery_date],
                            $consignment->id
                        );

                        $this->createNotification(
                            'station.notification_create_shipment',
                            [
                                'name' => $consignment->name,
                                'date' => $consignment->delivery_date,
                                'user_id' => auth()->user()->id,
                                'company_id' => auth()->user()->company_id
                            ]
                        );

                    }

                    session()->flash(
                        'flash_message',
                        array(trans('station.successful'),trans('station.consignment_successfully'), 'success')
                    );

                    return response()->json([
                        'status' => 'ok',
                        'consignmentId' => $consignment->id,
                        'url' => route('consignment.index', ['consignment' => $consignment->id])
                    ]);

                } catch (\Exception $e) {

                    session()->flash(
                        'flash_message',
                        array(trans('station.failed'),trans('station.error_text'), 'error')
                    );
                    return redirect()->back()->withInput();

                }

                break;

            case 'consigmentMsStore':

                // excel okuma adımlarını helper üzerinden yürütmeye çalış..
                try{

                    $attribute = array(
                        'po_no' => trans('station.po_number'),
                        //'country_code' => trans('station.country'),
                        'item_count' => trans('station.product_quantity'),
                        //'consignee_id' => trans('station.consignee_name'),
                        'delivery_date' => trans('station.delivery_date'),
                        //'plate_no' => trans('station.plate_no')
                        'db_list' => trans('station.file_upload'),
                    );

                    $rules = array(
                        'po_no' => 'required',
                        //'country_code' => 'required',
                        'item_count' => 'required|numeric|min:1',
                        // 'consignee_id' => 'required',
                        'delivery_date' => 'required|date|date_format:Y-m-d',
                        // 'plate_no' => 'nullable',
                        'db_list' => 'required|mimes:pdf|max:2048'
                    );

                    $validator = Validator::make($request->all(), $rules);
                    $validator->setAttributeNames($attribute);
                    if ($validator->fails()) {
                        return response()->json([
                            'status' => false,
                            'errors' => $validator->getMessageBag()->toArray()
                        ]);
                    }

                    $order = \App\Order::where('po_no', $request->get('po_no'))->first();
                    if(!$order){

                        $order = new \App\Order;
                        $order->order_code = $this->autoGenerateOrderCode();
                        $order->consignee_id = $request->get('sticker');
                        $order->po_no = $request->get('po_no');
                        $order->name = $request->get('name') ?? "null";
                        $order->item_count = $request->get('item_count');
                        $order->created_user_id = auth()->user()->id;
                        $order->save();

                    }

                    if($order){

                        $this->createLog('Order','portal.log_create_order', ['name' => $order->po_no], $order->id);

                        $consigneeName = \App\Consignee::find((int) $request->get('sticker'));
                        if ($consigneeName){
                            $conName = $consigneeName->name;
                        }

                        $consignment = new \App\Consignment;
                        $consignment->order_id = $order->id;
                        //$consignment->country_code = "RUW337";
                        $consignment->company_id = auth()->user()->company_id;
                        $consignment->name = $this->autoGeneratePoNo($order->id, $consignment->country_code, $conName);
                        $consignment->delivery_date = date('Y-m-d', strtotime($request->get('delivery_date')));
                        $consignment->consignee_id = $request->get('sticker') != 'other' ? $request->get('sticker') : $company->id;
                        $consignment->item_count = $request->get('item_count');
                        $consignment->created_user_id = auth()->user()->id;

                        if($request->get('po_no')){

                            $xmlFileRepoQuery = DB::table('xml_file_repos')
                                ->select('*')
                                ->join('txt_file_repos', function($join){ $join->on('txt_file_repos.xmlid', '=', 'xml_file_repos.id');})
                                ->where('xml_file_repos.poNumber', $request->get('po_no'))
                                ->orderBy('xml_file_repos.id', 'ASC')
                                ->get();

                            // echo '<pre>';
                            // print_r($xmlFileRepoQuery);

                            if ($xmlFileRepoQuery){

                                foreach($xmlFileRepoQuery as $xmlFile){

                                    $cartons = json_decode($xmlFile->cartons);
                                    $seasonId = $xmlFile->season;
                                    foreach($cartons as $carton){
                                        //echo $carton->colour.'<br>';
                                        foreach($carton->upcs as $upcs){

                                            $colorName = $xmlFile->colourCode.' '.
                                                $xmlFile->colourDesc.' '.
                                                $xmlFile->departmentDesc.' '.
                                                $xmlFile->strokeDesc;

                                            $data = [
                                                'consignment_id' => $consignment->id,
                                                'order' => $request->get('po_no'),
                                                'season' => $seasonId,
                                                'description' => $colorName,
                                                'sds_code' => $upcs->size,
                                                'upc' => $upcs->upc,
                                                'price' => null,
                                                'story_desc' => null,
                                                'qty_req' => null,
                                                'user_id' => auth()->user()->id,
                                            ];

                                            // echo '<pre>';
                                            // print_r($data);

                                            CompanyMsDb::create($data);

                                        }
                                    }

                                }

                            }

                            $pox = explode('/', $consignment->name);
                            if (is_array($pox)){
                                $order->po_no = $pox[0];
                            }
                            //echo $seasonId.'<br>';
                            $order->season = $seasonId;
                            $order->save();

                        }
                        /* if($consignment->save()){
                            // dosya varsa dosyayı convert et. convert hali ile işleme gir.Ajax kullanabilirsin.
                            //consignment name dogru gelmeyebilir. işlem sonunda da tekrar bakabilirsin.
                            //order season ve po_no convert edilen dosya içerisinden alınıcak.
                            if($request->hasFile('db_list')){

                                $file = $request->file('db_list');
                                $fileEx = $file->getClientOriginalExtension();
                                $fileName = md5(date('d-m-Y H:i:s') . $file->getClientOriginalName()) . "." . $fileEx;

                                //$file = $file->getRealPath();
                                $fileSave = Storage::disk('upload')->put(
                                    config('settings.media.files.path').$fileName, file_get_contents($file)
                                );

                                if ($fileSave){

                                    $fileModel = new \App\ReadFile;
                                    $fileModel->order_id = $order->id;
                                    $fileModel->company_id = auth()->user()->company_id;
                                    $fileModel->consignee_id = $request->get('sticker');
                                    $fileModel->name = $fileName;
                                    $fileModel->file_path = config('settings.media.files.full_path').$fileName;
                                    $fileModel->created_user_id = auth()->user()->id;
                                    if ($fileModel->save()){

                                        ob_start();
                                        $command = escapeshellcmd("convertPdfToXls.py $fileModel->name $fileModel->file_path");
                                        //sunucu icin ayarlanmis yol
                                        //$command = escapeshellcmd("python3 /home/admin/public_html/takipsan/public/convertPdfToXls.py $fileModel->name $fileModel->file_path");
                                        $output = shell_exec($command);
                                        ob_get_clean();
                                        if ($output){

                                            //echo $output;
                                            $file = preg_replace("/\s+/", " ", $output);
                                            $file = trim($output);
                                            if ( $xlsx = \SimpleXLSX::parse($file) ) {

                                                $sekmeler[] = $xlsx->sheetNames();
                                                $okunacakSekmeler = array();
                                                $aralikDeger = 10;
                                                $aralikDeger2 = 4;
                                                $flag = false;

                                                foreach($sekmeler as $sk => $sekme){
                                                    //print_r($sekme);
                                                    foreach($sekme as $s => $sekmeValue){
                                                        $sekmeBirlestir[$sekmeValue] = $xlsx->rows($s);
                                                    }

                                                }

                                                //Birleştirilen Sekmelerden Sabit Değerler İlk Sayfadan Manuel Alınıyor.
                                                foreach ($sekmeBirlestir as $sabitBKey => $sabitBValue){

                                                    if ($sabitBKey == 'page-2-table-1'){

                                                        if (count($sabitBValue) <= 9 ){

                                                            $supplierNo = $sabitBValue[3][2];
                                                            $strokeNumber = $sabitBValue[3][4];
                                                            $seasonId = $sabitBValue[7][2];

                                                        }elseif (count($sabitBValue) > 9 ){

                                                            $supplierNo = $sabitBValue[3][2];
                                                            $strokeNumber = $sabitBValue[3][4];
                                                            $seasonId = $sabitBValue[8][2];

                                                        }

                                                    }

                                                }

                                                //Birlestirilen Sekmelerden Okunacak Sayfa Bilgileri Alınıyor.
                                                foreach($sekmeBirlestir as $sBKey => $sBValue){
                                                    //echo $sBKey.'_<br>';
                                                    foreach($sBValue as $satirKey => $satirValue){
                                                        //echo $satirKey.'__<br>';
                                                        foreach ($satirValue as $sutunKey => $sutunValue){

                                                            if (
                                                                empty($okunacakSekmeler) &&
                                                                $satirKey === 1 && $sutunValue === 'Product Type :'
                                                                //$sutunKey === 2 && $sutunValue === 'P&L Approved :'
                                                            ){
                                                                $okunacakSekmeler[$sBKey][] = $sekmeBirlestir[$sBKey];

                                                                if ($flag = false){
                                                                    $aralikDeger = 10;
                                                                }
                                                                $flag = true;

                                                            }

                                                            if (
                                                                empty($okunacakSekmeler) &&
                                                                $satirKey === 1 && $sutunValue === 'UPCLabel Information'
                                                            ){
                                                                $okunacakSekmeler[$sBKey][] = $sekmeBirlestir[$sBKey];
                                                                $aralikDeger = 4;
                                                            }

                                                            if (
                                                                //empty($okunacakSekmeler) &&
                                                                //$satirKey === 1 && $sutunValue === 'Product Source Season ID :'
                                                                $satirKey === 3 && $sutunValue === 'Colourway Name'
                                                            ){
                                                                $okunacakSekmeler = [];
                                                                $okunacakSekmeler[$sBKey][] = $sekmeBirlestir[$sBKey];
                                                                $aralikDeger = 6;
                                                                //echo 'sad<br>';
                                                            }

                                                            if ($satirKey === 2 && $sutunValue === 'P&L Approved Date :'){

                                                                if ($sBKey == 'page-2-table-2'){
                                                                    //echo 'sad';
                                                                    unset($okunacakSekmeler[$sBKey]);
                                                                }
                                                                // $okunacakSekmeler = [];
                                                                $okunacakSekmeler[$sBKey][] = $sekmeBirlestir[$sBKey];
                                                                if ($flag = false){
                                                                    $aralikDeger = 1;
                                                                }
                                                                $flag = true;

                                                            }

                                                            //belirlenen sekme dısında kalan tek upc için ozel olarak eklendi. uniq durum
                                                            if (
                                                                empty($okunacakSekmeler) &&
                                                                $satirKey === 28 && $sutunValue === 'UPCLabel Information'
                                                            ){
                                                                //$okunacakSekmeler = [];
                                                                $okunacakSekmeler[$sBKey][] = $sekmeBirlestir[$sBKey];
                                                                $aralikDeger = 1;
                                                            }

                                                            if ($satirKey === 29 && $sutunValue === 'UPCLabel Information'){
                                                                $okunacakSekmeler = [];
                                                                $okunacakSekmeler[$sBKey][] = $sekmeBirlestir[$sBKey];
                                                                $aralikDeger = 1;
                                                                $aralikDeger2 = 1;
                                                            }

                                                            if ($satirKey === 5 && $sutunValue === 'UPCLabel Information'){
                                                                $aralikDeger = 7;
                                                            }

                                                            if ($satirKey === 10 && $sutunValue === 'UPCLabel Information'){
                                                                $aralikDeger = 12;
                                                            }

                                                            if ($satirKey === 43 && $sutunValue === 'UPCLabel Information'){
                                                                $aralikDeger = 6;
                                                            }

                                                            if ($satirKey === 42 && $sutunValue === 'UPCLabel Information'){
                                                                $aralikDeger = 5;
                                                            }

                                                            if ($strokeNumber == '7895'){
                                                                $aralikDeger = 6;
                                                            }

                                                        }

                                                    }

                                                }

                                                // print_r($okunacakSekmeler);
                                                // exit();

                                                $aralikSay = 0;
                                                $temizlikAralikSay = 0;
                                                $yazilacakBilgiler = array();
                                                $belgeBilgi = array();

                                                // echo 'supplierNo : '. $supplierNo.'<br>';
                                                // echo 'strokeNumber : '.$strokeNumber.'<br>';
                                                // echo 'seasonId : '.$seasonId.'<br>';
                                                //Alınan Veriler Koşula Göre Ayarlanıp DB ye kayıt edilecek.
                                                foreach($okunacakSekmeler as $sekmeKey => $sekmeValue){
                                                    //echo $sekmeKey.'<br>';
                                                    foreach($sekmeValue as $svKey => $svValue){
                                                        //print_r($svValue);
                                                        //Size bilgisi Baslik ile birlesik gelirse ayır
                                                        //price bilgisi upc bilgisi yerine gelirse yer degistir
                                                        //farklı bir durum cikarsa yeni kosul belirt
                                                        foreach($svValue as $svBaslikKey => $svBaslikValue){

                                                            $baslikUzunluk = count($svBaslikValue);
                                                            if (
                                                                $svBaslikValue[2] == "" &&
                                                                isset($svBaslikValue[4]) && $svBaslikValue[4] == "" &&
                                                                isset($svBaslikValue[6]) ? $svBaslikValue[6] == "" : ''
                                                            ){
                                                                $birlesikBaslik = $svBaslikValue[1];
                                                                $upcDegis = $svBaslikValue[3];
                                                                $priceDegis = $svBaslikValue[5];
                                                                if (strstr($birlesikBaslik, "\n")){
                                                                    $baslikBol = preg_split('/\r\n|\r|\n/', $birlesikBaslik);
                                                                    $bolunenBaslik = $baslikBol[0];
                                                                    $bolunenSize = $baslikBol[1];
                                                                    $svValue[$svBaslikKey][1] = $bolunenBaslik;
                                                                    $svValue[$svBaslikKey][3] = $bolunenSize;
                                                                    $svValue[$svBaslikKey][5] = $upcDegis;
                                                                    $svValue[$svBaslikKey][7] = $priceDegis;
                                                                }
                                                            }

                                                            if ($baslikUzunluk == 5 && $svBaslikValue[3] == ""){

                                                                $birlesikBaslik = $svBaslikValue[1];
                                                                $upcDegis = $svBaslikValue[2];
                                                                $priceDegis = $svBaslikValue[4];

                                                                if (strstr($birlesikBaslik, "\n")){

                                                                    $baslikBol = preg_split('/\r\n|\r|\n/', $birlesikBaslik);
                                                                    $bolunenBaslik = $baslikBol[0];
                                                                    $bolunenSize = $baslikBol[1];

                                                                    $svValue[$svBaslikKey][1] = $bolunenBaslik;
                                                                    $svValue[$svBaslikKey][2] = "";
                                                                    $svValue[$svBaslikKey][3] = $bolunenSize;
                                                                    $svValue[$svBaslikKey][4] = "";
                                                                    $svValue[$svBaslikKey][5] = $upcDegis;
                                                                    $svValue[$svBaslikKey][6] = "";
                                                                    $svValue[$svBaslikKey][7] = $priceDegis;

                                                                }

                                                            }

                                                            //upc no birlesik geldigi icin uniq hazırlandı.
                                                            if ($baslikUzunluk == 4 && $svBaslikValue[0] == 31){
                                                                $birlesikBaslik = $svBaslikValue[2];
                                                                $priceDegis = $svBaslikValue[3];

                                                                if (strstr($birlesikBaslik, "\n")){
                                                                    $baslikBol = preg_split('/\r\n|\r|\n/', $birlesikBaslik);
                                                                    $bolunenBaslik = $baslikBol[0];
                                                                    $bolunenSize = $baslikBol[1];
                                                                    $svValue[$svBaslikKey][2] = "";
                                                                    $svValue[$svBaslikKey][3] = $bolunenBaslik;
                                                                    $svValue[$svBaslikKey][4] = "";
                                                                    $svValue[$svBaslikKey][5] = $bolunenSize;
                                                                    $svValue[$svBaslikKey][6] = "";
                                                                    $svValue[$svBaslikKey][7] = $priceDegis;
                                                                }

                                                            }

                                                            //0102c icin primary_size ve upc birlesik geldigi icin hazırlandı.
                                                            if ($baslikUzunluk == 5 && $svBaslikValue[0] == 31){
                                                                $birlesikBaslik = $svBaslikValue[3];
                                                                $priceDegis = $svBaslikValue[4];

                                                                if (strstr($birlesikBaslik, "\n")){
                                                                    $baslikBol = preg_split('/\r\n|\r|\n/', $birlesikBaslik);
                                                                    $bolunenPrimarySize = $baslikBol[0];
                                                                    $bolunenUpc = $baslikBol[1];
                                                                    $svValue[$svBaslikKey][1] = $svBaslikValue[2];
                                                                    $svValue[$svBaslikKey][2] = "";
                                                                    $svValue[$svBaslikKey][3] = $bolunenPrimarySize;
                                                                    $svValue[$svBaslikKey][4] = "";
                                                                    $svValue[$svBaslikKey][5] = $bolunenUpc;
                                                                    $svValue[$svBaslikKey][6] = "";
                                                                    $svValue[$svBaslikKey][7] = $priceDegis;
                                                                }

                                                            }

                                                            //9236 icin size ve upc birlesik geldigi icin hazırlandı.
                                                            if ($baslikUzunluk == 4 && $svBaslikValue[0] == 30){

                                                                $birlesikBaslik = $svBaslikValue[2];
                                                                $priceDegis = $svBaslikValue[3];

                                                                if (strstr($birlesikBaslik, "\n")){

                                                                    $baslikBol = preg_split('/\r\n|\r|\n/', $birlesikBaslik);
                                                                    $bolunenBaslik = $baslikBol[0];
                                                                    $bolunenSize = $baslikBol[1];
                                                                    $svValue[$svBaslikKey][2] = "";
                                                                    $svValue[$svBaslikKey][3] = $bolunenBaslik;
                                                                    $svValue[$svBaslikKey][4] = "";
                                                                    $svValue[$svBaslikKey][5] = $bolunenSize;
                                                                    $svValue[$svBaslikKey][6] = "";
                                                                    $svValue[$svBaslikKey][7] = $priceDegis;

                                                                }

                                                            }

                                                            //7895 icin baslik ve size birlesik geldigi icin hazirlandi.
                                                            if ($baslikUzunluk == 6 && $strokeNumber == '7895'){

                                                                $birlesikBaslik = $svBaslikValue[1];
                                                                $upcDegis = $svBaslikValue[3];
                                                                $priceDegis = $svBaslikValue[5];

                                                                if (strstr($birlesikBaslik, "\n")){

                                                                    $baslikBol = preg_split('/\r\n|\r|\n/', $birlesikBaslik);
                                                                    $bolunenBaslik = $baslikBol[0];
                                                                    $bolunenSize = $baslikBol[1];
                                                                    $svValue[$svBaslikKey][1] = $bolunenBaslik;
                                                                    $svValue[$svBaslikKey][2] = "";
                                                                    $svValue[$svBaslikKey][3] = $bolunenSize;
                                                                    $svValue[$svBaslikKey][4] = "";
                                                                    $svValue[$svBaslikKey][5] = $upcDegis;
                                                                    $svValue[$svBaslikKey][6] = "";
                                                                    $svValue[$svBaslikKey][7] = $priceDegis;

                                                                }

                                                            }

                                                        }
                                                        //baslik bilgilerinde ikinci satirin birleştirildiği yer
                                                        foreach($svValue as $svBaslikKey => $svBaslikValue){
                                                            // ikinci satirları birlestir
                                                            $svBaslikUzunluk = count ($svBaslikValue);
                                                            if (
                                                                $svBaslikUzunluk != 8 &&
                                                                $svBaslikValue[2] == "" &&
                                                                $svBaslikValue[3] == "" &&
                                                                isset($svBaslikValue[4]) && $svBaslikValue[4] == "" &&
                                                                isset($svBaslikValue[5]) && $svBaslikValue[5] == "" &&
                                                                isset($svBaslikValue[6]) ? $svBaslikValue[6] == "" : ''
                                                            ){
                                                                $baslik = $svBaslikValue[1];
                                                                $svValue[$svBaslikKey-1][1] = $svValue[$svBaslikKey-1][1].' '.$baslik;
                                                            }

                                                            if (
                                                                $svBaslikUzunluk == 8 &&
                                                                $svBaslikValue[2] == "" &&
                                                                $svBaslikValue[3] == "" &&
                                                                isset($svBaslikValue[4]) && $svBaslikValue[4] == "" &&
                                                                isset($svBaslikValue[5]) && $svBaslikValue[5] == "" &&
                                                                isset($svBaslikValue[6]) ? $svBaslikValue[6] == "" : ''
                                                            ){
                                                                $baslik = $svBaslikValue[1];
                                                                $svValue[$svBaslikKey-1][1] = $svValue[$svBaslikKey-1][1].' '.$baslik;
                                                            }
                                                            if (
                                                                $svBaslikUzunluk == 8 &&
                                                                $svBaslikValue[1] == "" &&
                                                                $svBaslikValue[3] == "" &&
                                                                isset($svBaslikValue[4]) && $svBaslikValue[4] == "" &&
                                                                isset($svBaslikValue[5]) && $svBaslikValue[5] == "" &&
                                                                isset($svBaslikValue[6]) ? $svBaslikValue[6] == "" : ''
                                                            ){
                                                                //echo 'sad<br>';
                                                                $baslik = $svBaslikValue[2];
                                                                $svValue[$svBaslikKey-1][1] = $svValue[$svBaslikKey-1][1].' '.$baslik;
                                                            }
                                                            // uzunluk degeri 6 olan pdf içeriği için baslik birlestirme
                                                            if (
                                                                $svBaslikUzunluk == 6 &&
                                                                $svBaslikValue[2] == "" &&
                                                                $svBaslikValue[3] == "" &&
                                                                isset($svBaslikValue[4]) && $svBaslikValue[4] == "" &&
                                                                isset($svBaslikValue[5]) && $svBaslikValue[5] == ""
                                                            ){
                                                                $baslik = $svBaslikValue[1];
                                                                $svValue[$svBaslikKey-1][1] = $svValue[$svBaslikKey-1][1].' '.$baslik;
                                                            }
                                                            // uzunluk degeri 5 olan pdf içeriği için baslik birlestirme
                                                            if (
                                                                $svBaslikUzunluk == 5 &&
                                                                $svBaslikValue[2] == "" &&
                                                                $svBaslikValue[3] == "" &&
                                                                isset($svBaslikValue[4]) && $svBaslikValue[4] == ""
                                                            ){
                                                                $baslik = $svBaslikValue[1];
                                                                $svValue[$svBaslikKey-1][1] = $svValue[$svBaslikKey-1][1].' '.$baslik;
                                                            }
                                                            // uzunluk degeri 4 olan pdf icerigi icin baslik birlestirme
                                                            if (
                                                                $svBaslikUzunluk == 4 &&
                                                                $svBaslikValue[2] == "" &&
                                                                $svBaslikValue[3] == ""
                                                            ){
                                                                $baslik = $svBaslikValue[1];
                                                                $svValue[$svBaslikKey-1][1] = $svValue[$svBaslikKey-1][1].' '.$baslik;
                                                            }

                                                        }
                                                        //birlestirilen ikinci satir bilgilerde gereksiz verilerin silindiği yer
                                                        foreach($svValue as $svTemizleKey => $svTemizleValue){

                                                            $svTemizleUzunluk = count($svTemizleValue);
                                                            if (
                                                                $svTemizleValue[2] == "" &&
                                                                $svTemizleValue[3] == "" &&
                                                                isset($svTemizleValue[4]) && $svTemizleValue[4] == "" &&
                                                                isset($svTemizleValue[5]) && $svTemizleValue[5] == "" &&
                                                                isset($svTemizleValue[6]) ? $svTemizleValue[6] == "" : ''
                                                            ){
                                                                unset($svValue[$svTemizleKey]);
                                                            }

                                                            //uzunluk degeri 8 olan pdf birlestirilen ikinci satir siliniyor
                                                            if (
                                                                $svTemizleValue[0] != "" &&
                                                                $svTemizleValue[1] != "" &&
                                                                $svTemizleValue[2] != "" &&
                                                                $svTemizleValue[3] == "" &&
                                                                isset($svTemizleValue[4]) && $svTemizleValue[4] == "" &&
                                                                isset($svTemizleValue[5]) && $svTemizleValue[5] == "" &&
                                                                isset($svTemizleValue[6]) && $svTemizleValue[6] == "" &&
                                                                isset($svTemizleValue[7]) ? $svTemizleValue[7] == "" : ''
                                                            ){
                                                                unset($svValue[$svTemizleKey]);
                                                            }

                                                            //uzunluk degeri 5 olan pdf birlestirilen ikinci satir siliniyor
                                                            if (
                                                                $svTemizleUzunluk == 5 &&
                                                                $svTemizleValue[2] == "" &&
                                                                $svTemizleValue[3] == "" &&
                                                                isset($svTemizleValue[4]) && $svTemizleValue[4] == ""
                                                            ){
                                                                unset($svValue[$svTemizleKey]);
                                                            }

                                                            // uzunluk degeri 4 olan pdf birlestirilen ikinci satir siliniyor
                                                            if (
                                                                $svBaslikUzunluk == 4 &&
                                                                $svTemizleValue[2] == "" &&
                                                                $svTemizleValue[3] == ""
                                                            ){
                                                                unset($svValue[$svTemizleKey]);
                                                            }

                                                            if (
                                                                $svBaslikUzunluk == 4 &&
                                                                $svTemizleValue[2] == "" &&
                                                                $svTemizleValue[3] == ""
                                                            ){
                                                                unset($svValue[$svTemizleKey]);
                                                            }

                                                        }
                                                        // baslik bilgilerinde ucuncu satirin birlesitirldigi yer
                                                        foreach($svValue as $svBaslikKey2 => $svBaslikValue2){
                                                            // ucuncu satirları birlestir
                                                            $svBaslikUzunluk = count ($svBaslikValue);
                                                            if (
                                                                $svBaslikUzunluk != 8 &&
                                                                $svBaslikValue2[3] == "" &&
                                                                isset($svBaslikValue2[4]) && $svBaslikValue2[4] == "" &&
                                                                isset($svBaslikValue2[5]) && $svBaslikValue2[5] == "" &&
                                                                isset($svBaslikValue2[6]) ? $svBaslikValue2[6] == "" : ''
                                                            ){
                                                                $baslikSatir1 = $svBaslikValue2[2];
                                                                $baslikSatir2 = $svBaslikValue2[1];
                                                                $svValue[$svBaslikKey2-1][1] = $svValue[$svBaslikKey2-1][1].' '.$baslikSatir1.' '.$baslikSatir2;
                                                            }

                                                            if (
                                                                $svBaslikUzunluk == 8 &&
                                                                $svBaslikValue2[1] == "" &&
                                                                $svBaslikValue2[3] == "" &&
                                                                isset($svBaslikValue2[4]) && $svBaslikValue2[4] == "" &&
                                                                isset($svBaslikValue2[5]) && $svBaslikValue2[5] == "" &&
                                                                isset($svBaslikValue2[6]) && $svBaslikValue2[6] == "" &&
                                                                isset($svBaslikValue2[7]) ? $svBaslikValue2[7] == "" : ''
                                                            ){
                                                                $baslikSatir1 = $svBaslikValue2[2];
                                                                //echo $baslikSatir1.'_sad<br>';
                                                                $svValue[$svBaslikKey2-2][1] = $svValue[$svBaslikKey2-2][1].''.$baslikSatir1;
                                                            }

                                                        }
                                                        //birlestirilen ucuncu satir bilgilerinde gereksiz verilerin silindigi yer
                                                        foreach($svValue as $svTemizleKey2 => $svTemizleValue2){
                                                            //birlestirilen ucuncu
                                                            if (
                                                                $svTemizleValue2[3] == "" &&
                                                                isset($svTemizleValue2[4]) && $svTemizleValue2[4] == "" &&
                                                                isset($svTemizleValue2[5]) && $svTemizleValue2[5] == "" &&
                                                                isset($svTemizleValue2[6]) ? $svTemizleValue2[6] == "" : ''
                                                            ){
                                                                unset($svValue[$svTemizleKey2]);
                                                            }

                                                        }
                                                        //birlestirilen bilgilerde silinen gereksiz bilgilerden sonra
                                                        //upc numarası olmayan verilerin silindiği yer
                                                        if ($svKey == 0){
                                                            $temizlikAralikSay = 0;
                                                        }

                                                        foreach($svValue as $svUpcTemizleKey => $svUpcTemizleValue){

                                                            $temizlikAralikSay++;
                                                            $valueSay = count($svUpcTemizleValue);
                                                            // ayrım için gerekli baslik bilgileri korunuyor sonrasında gereksizler siliniyor.
                                                            // ilk sayfa için ayrı diger sayfalar icin ayrı işlem gerçekleştiriliyor.
                                                            if ($sekmeKey == 'page-2-table-2' && $temizlikAralikSay >= $aralikDeger){

                                                                if ($valueSay == 4 && $svUpcTemizleValue[0] == ""){
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if ($valueSay == 4 && $svUpcTemizleValue[1] == ""){
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if ($valueSay == 4 && $svUpcTemizleValue[2] == ""){
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if ($valueSay == 4 && $svUpcTemizleValue[3] == ""){
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if (
                                                                    $valueSay == 4 &&
                                                                    $svUpcTemizleValue[0] != "" &&
                                                                    $svUpcTemizleValue[1] != "" &&
                                                                    $svUpcTemizleValue[2] != "" &&
                                                                    $svUpcTemizleValue[3] != ""
                                                                ){
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if ($valueSay == 5 && $svUpcTemizleValue[0] == ""){
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if ($valueSay == 5 && $svUpcTemizleValue[2] == ""){
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if ($valueSay == 5 && $svUpcTemizleValue[4] == ""){
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if (
                                                                    $valueSay == 5 &&
                                                                    $svUpcTemizleValue[0] != "" &&
                                                                    $svUpcTemizleValue[1] != "" &&
                                                                    $svUpcTemizleValue[2] != "" &&
                                                                    $svUpcTemizleValue[3] != "" &&
                                                                    $svUpcTemizleValue[4] != ""
                                                                ){
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if ($valueSay == 6 && $svUpcTemizleValue[5] == ""){
                                                                    //echo $svUpcTemizleKey.'key_6_5_olan_silinebilir<br>';
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if ($valueSay == 7 && $svUpcTemizleValue[2] != "" && $svUpcTemizleValue[4] == ""){
                                                                    //echo $svUpcTemizleKey.'key_7_2dolu_4bos_silinebilir<br>';
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }
                                                                // sayfa sonundaki farklı tapbloyu sil
                                                                if (
                                                                    $valueSay == 7 &&
                                                                    $svUpcTemizleValue[2] == "" &&
                                                                    $svUpcTemizleValue[3] == "" &&
                                                                    $svUpcTemizleValue[4] == ""
                                                                ){
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if (
                                                                    $valueSay == 7 &&
                                                                    $svUpcTemizleValue[2] == "" &&
                                                                    $svUpcTemizleValue[3] == "" &&
                                                                    $svUpcTemizleValue[5] == "" &&
                                                                    $svUpcTemizleValue[6] == ""
                                                                ){
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if (
                                                                    $valueSay == 8 &&
                                                                    $svUpcTemizleValue[2] == "" &&
                                                                    $svUpcTemizleValue[3] == "" &&
                                                                    $svUpcTemizleValue[4] == "" &&
                                                                    $svUpcTemizleValue[6] == "" &&
                                                                    $svUpcTemizleValue[7] == ""
                                                                ){
                                                                    //echo $svUpcTemizleKey.'key_8_2bos_3bos_4bos_6bos_7bos_silinebilir<br>';
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if ($valueSay == 8 && $svUpcTemizleValue[2] == "" && $svUpcTemizleValue[5] == ""){
                                                                    //echo $svUpcTemizleKey.'key_8_2bos_5bos_silinebilir<br>';
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if ($valueSay == 8 && $svUpcTemizleValue[2] != "" && $svUpcTemizleValue[4] == ""){
                                                                    //echo $svUpcTemizleKey.'key_8_2dolu_4bos_silinebilir<br>';
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if ($valueSay == 8 && $svUpcTemizleValue[1] == "" && $svUpcTemizleValue[2] == ""){
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if (
                                                                    $valueSay == 9 &&
                                                                    $svUpcTemizleValue[2] == "" &&
                                                                    $svUpcTemizleValue[3] == "" &&
                                                                    $svUpcTemizleValue[4] == "" &&
                                                                    $svUpcTemizleValue[6] == "" &&
                                                                    $svUpcTemizleValue[7] == "" &&
                                                                    $svUpcTemizleValue[8] == ""
                                                                ){
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                            }

                                                            if ($sekmeKey != 'page-2-table-2' && $temizlikAralikSay >= $aralikDeger2){

                                                                if ($valueSay == 4 && $svUpcTemizleValue[0] == ""){
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if ($valueSay == 4 && $svUpcTemizleValue[1] == ""){
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if ($valueSay == 4 && $svUpcTemizleValue[2] == ""){
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if ($valueSay == 4 && $svUpcTemizleValue[3] == ""){
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if (
                                                                    $valueSay == 4 &&
                                                                    $svUpcTemizleValue[0] != "" &&
                                                                    $svUpcTemizleValue[1] != "" &&
                                                                    $svUpcTemizleValue[2] != "" &&
                                                                    $svUpcTemizleValue[3] != ""
                                                                ){
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if ($valueSay == 5 && $svUpcTemizleValue[0] == ""){
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if ($valueSay == 5 && $svUpcTemizleValue[2] == ""){
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if ($valueSay == 5 && $svUpcTemizleValue[4] == ""){
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if (
                                                                    $valueSay == 5 &&
                                                                    $svUpcTemizleValue[0] != "" &&
                                                                    $svUpcTemizleValue[1] != "" &&
                                                                    $svUpcTemizleValue[2] != "" &&
                                                                    $svUpcTemizleValue[3] != "" &&
                                                                    $svUpcTemizleValue[4] != ""
                                                                ){
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if ($valueSay == 6 && $svUpcTemizleValue[5] == ""){
                                                                    //echo $svUpcTemizleKey.'key_6_5_olan_silinebilir<br>';
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if ($valueSay == 7 && $svUpcTemizleValue[2] != "" && $svUpcTemizleValue[4] == ""){
                                                                    //echo $svUpcTemizleKey.'key_7_2dolu_4bos_silinebilir<br>';
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }
                                                                // sayfa sonundaki farklı tapbloyu sil
                                                                if (
                                                                    $valueSay == 7 &&
                                                                    $svUpcTemizleValue[2] == "" &&
                                                                    $svUpcTemizleValue[3] == "" &&
                                                                    $svUpcTemizleValue[4] == ""
                                                                ){
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if (
                                                                    $valueSay == 7 &&
                                                                    $svUpcTemizleValue[2] == "" &&
                                                                    $svUpcTemizleValue[3] == "" &&
                                                                    $svUpcTemizleValue[5] == "" &&
                                                                    $svUpcTemizleValue[6] == ""
                                                                ){
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if (
                                                                    $valueSay == 8 &&
                                                                    $svUpcTemizleValue[2] == "" &&
                                                                    $svUpcTemizleValue[3] == "" &&
                                                                    $svUpcTemizleValue[4] == "" &&
                                                                    $svUpcTemizleValue[6] == "" &&
                                                                    $svUpcTemizleValue[7] == ""
                                                                ){
                                                                    //echo $svUpcTemizleKey.'key_8_2bos_3bos_4bos_6bos_7bos_silinebilir<br>';
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if ($valueSay == 8 && $svUpcTemizleValue[2] == "" && $svUpcTemizleValue[5] == ""){
                                                                    //echo $svUpcTemizleKey.'key_8_2bos_5bos_silinebilir<br>';
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if ($valueSay == 8 && $svUpcTemizleValue[2] != "" && $svUpcTemizleValue[4] == ""){
                                                                    //echo $svUpcTemizleKey.'key_8_2dolu_4bos_silinebilir<br>';
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if ($valueSay == 8 && $svUpcTemizleValue[1] == "" && $svUpcTemizleValue[2] == ""){
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                                if (
                                                                    $valueSay == 9 &&
                                                                    $svUpcTemizleValue[2] == "" &&
                                                                    $svUpcTemizleValue[3] == "" &&
                                                                    $svUpcTemizleValue[4] == "" &&
                                                                    $svUpcTemizleValue[6] == "" &&
                                                                    $svUpcTemizleValue[7] == "" &&
                                                                    $svUpcTemizleValue[8] == ""
                                                                ){
                                                                    unset($svValue[$svUpcTemizleKey]);
                                                                }

                                                            }

                                                        }
                                                        //print_r($svValue);
                                                        // upc numarası olan verilerin db ye kayıt islemlerinin yapıldıgı yer
                                                        if ($svKey == 0){
                                                            $aralikSay = 0;
                                                        }

                                                        foreach($svValue as $satirKey => $satirValue){

                                                            $aralikSay++;
                                                            $satirSay = count($satirValue);

                                                            //ilk sekme için baslik bilgisinden sonra degerler alınıyor.
                                                            if ($sekmeKey == 'page-2-table-2' && $aralikSay >= $aralikDeger){
                                                                //print_r($satirValue);
                                                                $colorName = $satirValue[1];
                                                                $primarySize = $satirValue[2] != "" ? $satirValue[2] : $satirValue[3];
                                                                $secondarySize = $satirValue[2] != "" ? $satirValue[3] : $satirValue[4];
                                                                $upcNumber = $satirValue[2] != "" ? $satirValue[4] : $satirValue[5];
                                                                $storyDesc = "";
                                                                $qtyReq ="";
                                                                $price = $satirValue[2] != "" ? $satirValue[6] : $satirValue[7];
                                                                if ($primarySize != "" && $secondarySize != ""){
                                                                    $sdsCode = $primarySize.'/'.$secondarySize;
                                                                }elseif ($primarySize != "" && $secondarySize == ""){
                                                                    $sdsCode = $primarySize;
                                                                }elseif ($primarySize == "" && $secondarySize != ""){
                                                                    $sdsCode = $secondarySize;
                                                                }

                                                                //CompanyMsDb::where('upc',$upcNumber)->delete();

                                                                $data = [
                                                                    'consignment_id' => $consignment->id,
                                                                    'order' => $request->get('po_no'),
                                                                    'season' => $seasonId,
                                                                    'description' => $colorName,
                                                                    'sds_code' => $sdsCode,
                                                                    'upc' => $upcNumber,
                                                                    'price' => $price,
                                                                    'story_desc' => $storyDesc,
                                                                    'qty_req' => $qtyReq,
                                                                    'user_id' => auth()->user()->id,
                                                                ];

                                                                CompanyMsDb::create($data);

                                                            }

                                                            // diger sekmelerdeki degerler alınıyor.
                                                            if ($sekmeKey != 'page-2-table-2' && $aralikSay >= $aralikDeger2){
                                                                //print_r($satirValue);
                                                                $colorName = $satirValue[1];
                                                                $primarySize = $satirValue[2] != "" ? $satirValue[2] : $satirValue[3];
                                                                $secondarySize = $satirValue[2] != "" ? $satirValue[3] : $satirValue[4];
                                                                $upcNumber = $satirValue[2] != "" ? $satirValue[4] : $satirValue[5];
                                                                $storyDesc = "";
                                                                $qtyReq ="";
                                                                $price = $satirValue[2] != "" ? $satirValue[6] : $satirValue[7];
                                                                if ($primarySize != "" && $secondarySize != ""){
                                                                    $sdsCode = $primarySize.'/'.$secondarySize;
                                                                }elseif ($primarySize != "" && $secondarySize == ""){
                                                                    $sdsCode = $primarySize;
                                                                }elseif ($primarySize == "" && $secondarySize != ""){
                                                                    $sdsCode = $secondarySize;
                                                                }

                                                                //CompanyMsDb::where('upc',$upcNumber)->delete();

                                                                $data = [
                                                                    'consignment_id' => $consignment->id,
                                                                    'order' => $request->get('po_no'),
                                                                    'season' => $seasonId,
                                                                    'description' => $colorName,
                                                                    'sds_code' => $sdsCode,
                                                                    'upc' => $upcNumber,
                                                                    'story_desc' => $storyDesc,
                                                                    'price' => $price,
                                                                    'qty_req' => $qtyReq,
                                                                    'user_id' => auth()->user()->id,
                                                                ];

                                                                CompanyMsDb::create($data);

                                                            }

                                                        }
                                                        //print_r($svValue);
                                                        //print_r($svValue);
                                                        //print_r($yazilacakBilgiler);
                                                    }

                                                }

                                            }else{

                                                echo 'xls okuma hatası';

                                            }


                                        }else{

                                            echo 'dosya convert hatası';

                                        }

                                    }
                                    //exit();
                                }

                                //$order->po_no = $supplierNo;
                                $pox = explode('/', $consignment->name);
                                if (is_array($pox)){
                                    $order->po_no = $pox[0];
                                }
                                $order->season = $seasonId;
                                $order->save();



                            }

                        } */

                        $this->createLog(
                            'Consignment','portal.log_create_consignment',
                            ['name' => $consignment->name, 'date' => $consignment->delivery_date],
                            $consignment->id
                        );

                        $this->createNotification(
                            'station.notification_create_shipment',
                            [
                                'name' => $consignment->name,
                                'date' => $consignment->delivery_date,
                                'user_id' => auth()->user()->id,
                                'company_id' => auth()->user()->company_id
                            ]
                        );

                    }

                    session()->flash(
                        'flash_message',
                        array(trans('station.successful'),trans('station.consignment_successfully'), 'success')
                    );

                    return response()->json([
                        'status' => 'ok',
                        'url' => route('consignment.index', ['consignment' => $consignment->id])
                    ]);

                } catch (\Exception $e) {

                    session()->flash(
                        'flash_message',
                        array(trans('station.failed'),trans('station.error_text'), 'error')
                    );
                    return redirect()->back()->withInput();

                }

                break;

            case 'consigmentDcStore':

                try {

                } catch (\Exception $e){

                }
                break;

            case 'consigmentHbStore':

                try {

                    $attribute = array(
                        //'po_no' => trans('station.po_number'),
                        'country_code' => trans('station.country'),
                        'item_count' => trans('station.product_quantity'),
                        //'consignee_id' => trans('station.consignee_name'),
                        'delivery_date' => trans('station.delivery_date'),
                        //'plate_no' => trans('station.plate_no')
                    );

                    $rules = array(
                        // 'po_no' => 'required',
                        'country_code' => 'required',
                        'item_count' => 'required|numeric|min:1',
                        // 'consignee_id' => 'required',
                        'delivery_date' => 'required|date|date_format:Y-m-d',
                        // 'plate_no' => 'nullable',
                    );

                    $validator = Validator::make($request->all(), $rules);
                    $validator->setAttributeNames($attribute);
                    if ($validator->fails()) {
                        return response()->json([
                            'status' => false,
                            'errors' => $validator->getMessageBag()->toArray()
                        ]);
                    }

                    $order = new \App\Order;

                    $order->order_code = $this->autoGenerateOrderCode();
                    $order->consignee_id = $request->get('sticker');
                    //$order->po_no = $request->get('po_no');
                    $order->name = $request->get('name') ?? "null";
                    $order->item_count = $request->get('item_count');
                    $order->created_user_id = auth()->user()->id;
                    $order->save();

                    if($order){

                        $this->createLog('Order','portal.log_create_order', ['name' => $order->po_no], $order->id);

                        $consignment = new \App\Consignment;
                        $consignment->order_id = $order->id;
                        $consignment->country_code = $request->get('country_code');
                        $consignment->company_id = auth()->user()->company_id;
                        //$consignment->name = $this->autoGeneratePoNo($order->id,$consignment->country_code);
                        // $consignment->plate_no = strtoupper(str_replace(" ", "", $request->get('plate_no')));
                        $consignment->item_count = $request->get('item_count');
                        $consignment->delivery_date = $request->get('delivery_date');
                        $consignment->consignee_id = $request->get('sticker');
                        $consignment->created_user_id = auth()->user()->id;
                        if($consignment->save()){

                            if($request->hasFile('db_list')){

                                $path = $_FILES["db_list"]['tmp_name'];
                                //$request->file('db_list')->getRealPath();
                                $path = $request->file('db_list')->getRealPath();
                                $inx = 0;
                                $model = "";
                                if ( $xlsx = \SimpleXLSX::parse($path) ) {

                                    foreach($xlsx->rows() as $sKey => $sValue){

                                        if ($sKey > 1){

                                            $sku = $sValue[0];
                                            $name = $sValue[3];
                                            $part = $sValue[4];
                                            $referans = $sValue[5];
                                            $season = $sValue[6];
                                            $size = $sValue[7];
                                            $color = $sValue[8];
                                            $count = $sValue[9];

                                            //CompanyDb::where('gtin',$line[0])->delete();

                                            if (substr($sku, 0,1) == 0 ){
                                                $sku = substr($sku, 1, strlen($sku));
                                            }

                                            $order->po_no = $part;
                                            $order->season = $season;
                                            if (!empty($color)){
                                                $model = $size.'/'.$color;
                                            }else{
                                                $model = $size;
                                            }

                                            $data = [
                                                'consignment_id' => $consignment->id,
                                                'order' => $part,
                                                'season' => $season,
                                                'product' => $part,
                                                'variant' => '',
                                                'gtin' => $sku,
                                                'article_number' => '',
                                                'sds_code' => $model,
                                                'description' => $name,
                                                'user_id' => auth()->user()->id
                                            ];

                                            CompanyDb::create($data);

                                        }

                                    }

                                }
                                // echo ('sevkiyat_adi => '.$consignment->name);
                                // exit();
                                $order->save();

                                $consigneeName = \App\Consignee::find((int) $request->get('sticker'));
                                if ($consigneeName){
                                    $conName = $consigneeName->name;
                                }
                                //$consignment->name = '_sad';
                                $consignment->name = $this->autoGeneratePoNo($order->id, $request->get('country_code'), $conName);

                                // echo ($consignment->name);
                                // exit();
                                $consignment->save();

                            }

                        }

                        $this->createLog(
                            'Consignment','portal.log_create_consignment',
                            ['name' => $consignment->name, 'date' => $consignment->delivery_date],
                            $consignment->id
                        );

                        $this->createNotification(
                            'station.notification_create_shipment',
                            [
                                'name' => $consignment->name,
                                'date' => $consignment->delivery_date,
                                'user_id' => auth()->user()->id,
                                'company_id' => auth()->user()->company_id
                            ]
                        );

                    }

                    session()->flash(
                        'flash_message',
                        array(trans('station.successful'),trans('station.consignment_successfully'), 'success')
                    );

                    return response()->json([
                        'status' => 'ok',
                        'consignmentId' => $consignment->id,
                        'url' => route('consignment.index', ['consignment' => $consignment->id])
                    ]);

                } catch (\Exception $e) {

                    session()->flash(
                        'flash_message',
                        array(trans('station.failed'),trans('station.error_text'), 'error')
                    );
                    return redirect()->back()->withInput();

                }

                break;

            case 'consigmentLevisStore':

                try {

                    $attribute = array(
                        //'po_no' => trans('station.po_number'),
                        'country_code' => trans('station.country'),
                        'item_count' => trans('station.product_quantity'),
                        //'consignee_id' => trans('station.consignee_name'),
                        'delivery_date' => trans('station.delivery_date'),
                        //'plate_no' => trans('station.plate_no')
                    );

                    $rules = array(
                        // 'po_no' => 'required',
                        'country_code' => 'required',
                        'item_count' => 'required|numeric|min:1',
                        // 'consignee_id' => 'required',
                        'delivery_date' => 'required|date|date_format:Y-m-d',
                        // 'plate_no' => 'nullable',
                    );

                    $validator = Validator::make($request->all(), $rules);
                    $validator->setAttributeNames($attribute);
                    if ($validator->fails()) {
                        return response()->json([
                            'status' => false,
                            'errors' => $validator->getMessageBag()->toArray()
                        ]);
                    }

                    $order = new \App\Order;

                    $order->order_code = $this->autoGenerateOrderCode();
                    $order->consignee_id = $request->get('sticker');
                    //$order->po_no = $request->get('po_no');
                    $order->name = $request->get('name') ?? "null";
                    $order->item_count = $request->get('item_count');
                    $order->created_user_id = auth()->user()->id;
                    $order->save();

                    if($order){

                        $this->createLog('Order','portal.log_create_order', ['name' => $order->po_no], $order->id);

                        $consignment = new \App\Consignment;
                        $consignment->order_id = $order->id;
                        $consignment->country_code = $request->get('country_code');
                        $consignment->company_id = auth()->user()->company_id;
                        //$consignment->name = $this->autoGeneratePoNo($order->id,$consignment->country_code);
                        // $consignment->plate_no = strtoupper(str_replace(" ", "", $request->get('plate_no')));
                        $consignment->item_count = $request->get('item_count');
                        $consignment->delivery_date = $request->get('delivery_date');
                        $consignment->consignee_id = $request->get('sticker');
                        $consignment->created_user_id = auth()->user()->id;
                        if($consignment->save()){

                            if ($request->hasFile('db_list')) {

                                $pathLevis = $_FILES["db_list"]['tmp_name'];
                                //$request->file('db_list')->getRealPath();
                                $pathLevis = $request->file('db_list')->getRealPath();
                                $pathLevisEx = $request->file('db_list')->extension();

                                //echo $pathTargetEx;
                                if ($pathLevisEx == 'xlsx'){

                                    $xlsx = \SimpleXLSX::parse($pathLevis);

                                }elseif ($pathLevisEx == 'xls'){

                                    $xlsx = SimpleXLS::parse($pathLevis);

                                }else{

                                    session()->flash(
                                        'flash_message',
                                        array(trans('station.failed'), 'hatalı dosya uzantısı', 'error')
                                    );

                                    return response()->json([
                                        'status' => false,
                                        'errors' => 'hatalı dosya uzantısı'
                                    ]);

                                }

                                $inx = 0;
                                $model = "";
                                if ($xlsx) {

                                    foreach ($xlsx->rows() as $sKey => $sValue) {

                                        if ($sKey > 0){

                                            if ( !empty(array_filter($sValue))) {

                                                $qty = $sValue[0];
                                                $dn = $sValue[1];
                                                $po = $sValue[2];
                                                $productCode = $sValue[3];
                                                $waistLine = $sValue[4];
                                                $legSize = $sValue[5];
                                                $eanCode = $sValue[6];
                                                $size = $sValue[7];
                                                $printSize = $sValue[8];
                                                $epc = $sValue[9];

                                                $order->po_no = $po;
                                                $order->season = $productCode;

                                                $data = [
                                                    'consignment_id' => $consignment->id,
                                                    'order' => $po,
                                                    'user_id' => auth()->user()->id,
                                                    'qty' => $qty,
                                                    'dn' => $dn,
                                                    'po' => $po,
                                                    'product_code' => $productCode,
                                                    'waist_line' => $waistLine,
                                                    'leg_size' => $legSize,
                                                    'ean_code' => $eanCode,
                                                    'size' => $size,
                                                    'print_size' => $printSize,
                                                    'epc' => $epc,
                                                ];

                                                CompanyLevisDb::create($data);

                                            }

                                        }

                                    }

                                }

                                $order->save();

                                $consigneeName = \App\Consignee::find((int)$request->get('sticker'));
                                if ($consigneeName) {
                                    $conName = $consigneeName->name;
                                }
                                //$consignment->name = '_sad';
                                $consignment->name = $this->autoGeneratePoNo($order->id, $request->get('country_code'), $conName);
                                $consignment->save();

                            }

                        }

                        $this->createLog(
                            'Consignment','portal.log_create_consignment',
                            ['name' => $consignment->name, 'date' => $consignment->delivery_date],
                            $consignment->id
                        );

                        $this->createNotification(
                            'station.notification_create_shipment',
                            [
                                'name' => $consignment->name,
                                'date' => $consignment->delivery_date,
                                'user_id' => auth()->user()->id,
                                'company_id' => auth()->user()->company_id
                            ]
                        );

                    }

                    session()->flash(
                        'flash_message',
                        array(trans('station.successful'),trans('station.consignment_successfully'), 'success')
                    );

                    return response()->json([
                        'status' => 'ok',
                        'consignmentId' => $consignment->id,
                        'url' => route('consignment.index', ['consignment' => $consignment->id])
                    ]);

                } catch (\Exception $e) {

                    session()->flash(
                        'flash_message',
                        array(trans('station.failed'),trans('station.error_text'), 'error')
                    );
                    return redirect()->back()->withInput();

                }

                break;

            case 'updateConsignment':

                try {

                    $attribute = array(
                        'name' => trans('station.model_name'),
                        'item_count' => trans('station.product_quantity'),
                        //'consignee_id' => trans('station.consignee_name'),
                        'delivery_date' => trans('station.delivery_date'),
                        'plate_no' => trans('station.plate_no'),
                        'country_code' => trans('station.country')
                    );

                    $rules = array(
                        'name' => 'nullable',
                        //'item_count' => 'required|numeric|min:1',
                        'item_count' => 'nullable',
                        //'consignee_id' => 'required',
                        'delivery_date' => 'required',
                        'plate_no' => 'nullable',
                        'country_code' => 'nullable'
                    );

                    $validator = Validator::make($request->all(), $rules);
                    $validator->setAttributeNames($attribute);
                    if ($validator->fails()) {
                        return response()->json([
                            'status' => false,
                            'errors' => $validator->getMessageBag()->toArray()
                        ]);
                    }

                    $id = $request->get('id');
                    $consignment = \App\Consignment::findOrFail($id);

                    //Sevkiyat ismi gelen ulke koduna göre degistiriliyor.
                    //isim parcalanıp, gelen ulke koduna gore yeniden formatlanıyor.
                    //po_no-1/ulke_kod/H&M
                    if ($request->get('country_code')){

                        $ex = explode('/', $consignment->name);
                        $newName = $ex[0].'/'.$request->get('country_code').'/'.$ex[2];

                    }else{

                        $newName = $consignment->name;

                    }

                    $consignment->name = $newName;
                    $consignment->plate_no = strtoupper(str_replace(" ", "", $request->get('plate_no')));
                    $consignment->item_count = $request->get('item_count');
                    $consignment->delivery_date = date('Y-m-d', strtotime($request->get('delivery_date')));
                    //$consignment->consignee_id = $request->get('consignee_id');
                    $consignment->country_code = $request->get('country_code');
                    $consignment->updated_user_id = auth()->user()->id;
                    $consignment->save();

                    // model adı sabit değer gerildiği için ordername güncellemesine gerek yok
                    // $order = \App\Order::findOrFail($consignment->order_id);
                    // $order->name = $request->get('name');
                    // $order->save();

                    $this->createLog(
                        'Consignment','portal.log_update_consignment',
                        ['name' => $consignment->name, 'date' => $consignment->delivery_date],
                        $consignment->id
                    );

                    $this->createNotification(
                        'station.notification_update_shipment',
                        [
                            'name' => $consignment->name,
                            'date' => $consignment->delivery_date,
                            'user_id' => auth()->user()->id,
                            'company_id' => auth()->user()->company_id
                        ]
                    );

                    session()->flash(
                        'flash_message',
                        array(trans('portal.successful'), trans('portal.err_update_consignment'), 'success')
                    );

                    return response()->json([
                        'status' => 'ok',
                        'consignmentId' => $consignment->id,
                        'url' => route('consignment.index', ['consignment' => $consignment->id])
                    ]);

                }catch (\Exception $e){

                    //session()->flash('flash_message', array(trans('station.failed'),trans('station.error_text'), 'error'));
                    return response()->json(['status' => false, 'message' => trans('station.error_text')]);

                }

                break;

        }

    }

    /**
    * Belirtilen kaynağı gösterir.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function show($id){

        if($this->roleCheck(config('settings.roles.uretici'))){ //üretici
            $consignment = Consignment::where('company_id', auth()->user()->company_id)
                ->with(['company', 'order', 'consignee', 'created_user'])
                ->withCount(['items'])
                ->findOrFail($id);

        }else if($this->roleCheck(config('settings.roles.anaUretici'))){ //ana üretici 
            $consignment = Consignment::with(['company', 'order', 'consignee', 'created_user',])
                ->whereHas('company', function($q) {
                $q->where('status','=',1)->where('main_company_id', auth()->user()->company_id);
                })->withCount(['items'])
                ->findOrFail($id);

        }else if($this->roleCheck(config('settings.roles.partner'))){ //partner
            $consignment = Consignment::with(['company', 'order', 'consignee', 'created_user',])
                ->whereHas('company', function($q) {
                $q->where('created_user_id', '=', auth()->user()->id)->where('status','=',1);
                })->withCount(['items'])
                ->findOrFail($id);

        }else if($this->roleCheck(config('settings.roles.admin'))){ //admin
            $consignment = Consignment::with(['company', 'order', 'consignee', 'created_user'])
            ->withCount(['items'])
            ->findOrFail($id);
        }




        if($consignment){
            $this->data['consignment'] = $consignment;
        }


        return view('consignment.show', $this->data);

    }

    /**
    * Belirtilen kaynağı düzenlemek için formu gösterir.
    *
    * @param int $id
    * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    */
    public function edit($id){

        if($this->roleCheck(config('settings.roles.uretici'))){ //üretici
            $consignment = Consignment::where('company_id', auth()->user()->company_id)
                ->with(['order'])->findOrFail($id);

        }else if($this->roleCheck(config('settings.roles.anaUretici'))){ //ana üretici 
            $consignment = Consignment::with(['order'])
                ->whereHas('company', function($q) {
                $q->where('status','=',1)->where('main_company_id', auth()->user()->company_id);
                })->findOrFail($id);

        }else if($this->roleCheck(config('settings.roles.partner'))){ //partner
            $consignment = Consignment::with(['order'])
                ->whereHas('company', function($q) {
                $q->where('created_user_id', '=', auth()->user()->id)->where('status','=',1);
                })->findOrFail($id);

        }else if($this->roleCheck(config('settings.roles.admin'))){ //admin
            $consignment = Consignment::with(['order'])->findOrFail($id);
        }

        if($consignment){
            $this->data['consignment'] = $consignment;
        }

        // echo '<pre>';
        // print_r($consignment);
        // exit();


        //$companies = Company::where('status', 1)->get();
        $companies = Company::with(['consignees'])->find(auth()->user()->company_id);
        if($companies){
            $this->data['companies'] = $companies;
        }

        // $orders = Order::where('status', 1)->get();
        // if($orders){
        //     $this->data['orders'] = $orders;
        // }

        $country_list = DB::table('country_list')->orderBy('country_list_name', 'ASC')->get();
        if ($country_list){
            $this->data['country_list'] = $country_list;
        }

        return view('consignment.edit', $this->data);

    }

    /**
    * @param Request $request
    * @param $id
    * @return \Illuminate\Http\RedirectResponse
    */
    public function update(Request $request, $id){

        try {

            $attribute = array(
                'order_id' => trans('portal.order'),
                'item_count' => trans('portal.piece'),
                'delivery_date' => trans('portal.delivery_date'),
            );

            $rules = array(
                'order_id' => 'required',
                'item_count' => 'required',
                'delivery_date' => 'required',
            );

            if($this->roleCheck(config('settings.roles.anaUretici'))){
                $attribute['company_id'] = trans('portal.company');
                $rules['company_id'] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);
            if ($validator->fails()) {

                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();

            }

            if($this->roleCheck(config('settings.roles.anaUretici'))){
                $consignment = Consignment::findOrFail($id);
            }else{
                $consignment = Consignment::where('company_id', auth()->user()->company_id)->findOrFail($id);
            }

            if($consignment->order_id != $request->get('order_id')){
                $consignment->name = $this->autoGeneratePoNo($request->get('order_id'));
            }

            $consignment->order_id = $request->get('order_id');
            if($this->roleCheck(config('settings.roles.anaUretici'))){
                $consignment->company_id = $request->get('company_id');
            }else{
                $consignment->company_id = auth()->user()->company_id;
            }

            $consignment->plate_no = $request->get('plate_no');
            $consignment->item_count = $request->get('item_count');
            $consignment->delivery_date = $request->get('delivery_date');
            $consignment->consignee_id = $this->getConsigneeId($request->get('order_id'));
            $consignment->updated_user_id = auth()->user()->id;
            $consignment->save();

            $this->createLog(
                'Consignment','portal.log_update_consignment',
                ['name' => $consignment->name, 'date' => $consignment->delivery_date],
                $consignment->id
            );

            $this->createNotification(
                'station.notification_update_shipment',
                [
                    'name' => $consignment->name,
                    'date' => $consignment->delivery_date,
                    'user_id' => auth()->user()->id,
                    'company_id' => auth()->user()->company_id
                ]
            );

            session()->flash(
                'flash_message',
                array(trans('portal.successful'), trans('portal.err_update_consignment'), 'success')
            );

        } catch (\Exception $e) {

            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
            return redirect()->back()->withInput();

        }

        return redirect()->route('consignment.index');

    }

    /**
    * Belirtilen kaynağı database üzerinden kaldırır.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function destroy($id){

        try {

           $consignment = Consignment::findOrFail($id);
            
            $consignment->updated_user_id = auth()->user()->id;
            $consignment->save();
            $consignment->packages()->delete();
            $consignment->items()->delete();
            $consignment->delete();

            $this->createLog(
                'Consignment',
                'portal.log_delete_consignment',
                ['name' => $consignment->name],
                $consignment->id
            );

            session()->flash(
                'flash_message',
                array(trans('portal.successful'), trans('portal.err_delete_consignment'), 'success')
            );

        } catch (\Exception $e) {
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
        }

        return redirect()->back();

    }

    /**
    * Belirtilen kaynağın durumunu değiştirmek için kullanılır.
    *
    * @param int $id
    * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    */
    public function status($id){

        try {
            if($this->roleCheck(config('settings.roles.admin'))){
                $consignment = Consignment::findOrFail($id);
            }else if($this->roleCheck(config('settings.roles.partner'))){
                $consignment = Consignment::whereHas('company', function($q) {
                    $q->where('created_user_id', '=', auth()->user()->id)->where('status','=',1);
                })->findOrFail($id);
            }
            else if($this->roleCheck(config('settings.roles.anaUretici'))){
                $consignment = Consignment::whereHas('company', function($q) {
                    $q->where('main_company_id', '=', auth()->user()->company_id)->where('status','=',1);
                })->findOrFail($id);
            }
            else if($this->roleCheck(config('settings.roles.uretici'))){
                $consignment = Consignment::whereHas('company', function($q) {
                    $q->where('id', '=', auth()->user()->company_id)->where('status','=',1);
                })->findOrFail($id);

                if($consignment->status == false)
                {
                    session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
                    return redirect()->back();
                }
            }

            $consignment->status = $consignment->status == true ? false : true;
            $consignment->updated_user_id = auth()->user()->id;
            $consignment->save();

            if($consignment->status == true){

                $this->createLog(
                    'Consignment','portal.log_statusopen_consignment',
                    ['name' => $consignment->name],
                    $consignment->id
                );

                session()->flash(
                    'flash_message',
                    array(
                        trans('portal.successful'),
                        trans('portal.log_statusopen_consignment', ['name' => $consignment->name]),
                        'success'
                    )
                );

            }else{

                $this->createLog(
                    'Consignment',
                    'portal.log_statusclose_consignment',
                    ['name' => $consignment->name],
                    $consignment->id
                );

                session()->flash(
                    'flash_message',
                    array(
                        trans('portal.successful'),
                        trans('portal.log_statusclose_consignment', ['name' => $consignment->name]),
                        'success'
                    )
                );

            }

        } catch (\Exception $e) {
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
        }

        return redirect()->back();

    }

    public function datatable(Request $request){

        $query = Consignment::with(['company', 'consignee']);

        //Filter
        if ($request->has('search.value') && $request->get('search')['value'] != '') {
            $text = $request->get('search')['value'];
            $query->orWhereHas('company', function($q) use ($text) {
                $q->where('name', 'like', "%" .$text. "%");
            })->orWhereHas('consignee', function($q) use ($text) {
                $q->where('name', 'like', "%" .$text. "%");
            })->orWhere('name', 'like', "%" .$text. "%");
        }

        if($this->roleCheck(config('settings.roles.uretici'))){ //üretici
            $query->where('company_id', auth()->user()->company_id);
        }else if($this->roleCheck(config('settings.roles.anaUretici'))){ //ana üretici 
            $query->whereHas('company', function($q) {
                $q->where('main_company_id', '=', auth()->user()->company_id)->where('status','=',1);
            });

        }else if($this->roleCheck(config('settings.roles.partner'))){ //partner
            $query->whereHas('company', function($q) {
                $q->where('created_user_id', '=', auth()->user()->id)->where('status','=',1);
            });
            
        }else if($this->roleCheck(config('settings.roles.admin'))){ //admin
            $query->whereHas('company', function($q) {
                $q->where('status','=',1);
            });    
        }
        if ($request->has('company')) {
            $query->where('company_id', $request->get('company'));
        }

        if ($request->has('consignee')) {
            $query->where('consignee_id', $request->get('consignee'));
        }
        
        //Sıralama
        if($request->has('order.0')){

            $dir = $request->get('order')[0]['dir'];
            $column = $request->get('order')[0]['column'];
            $query->orderBy($request->get('columns')[$column]['name'], $dir);

        }else{
            $query->orderBy('updated_at', 'desc');
        }

        return Datatables::of($query)->editColumn('name', function ($value){

            return '<a href="' . route('consignment.show', $value->id) . '">'. $value->name. '</a>';

        })->editColumn('item_count', function ($value){

            $conStatus = consignmentStatusPercent($value->items()->count(), $value->item_count);

            return '<div class="kt-widget__progress d-flex align-items-center">
                <div class="progress" style="height: 5px;width: 100%;">
                    <div
                        class="progress-bar '.consignmentProgressBg($conStatus).'"
                        role="progressbar"
                        style="width: '.$conStatus.'%;"
                        aria-valuenow="'.$conStatus.'"
                        aria-valuemin="0"
                        aria-valuemax="100"
                    ></div>
                </div>
                <span class="kt-widget__stats"> %'.$conStatus .'</span>
            </div>';

        })->editColumn('company', function ($value){

            return $value->company ? $value->company->name : '-';
          //  return strtoupper($value->company->name) == "BROSS"  ? "Takipsan" : $value->company->name;

        })->editColumn('consignee', function ($value){

            return $value->consignee ? $value->consignee->name : '-';

        })->editColumn('status', function ($value){

            return $value->status == 1 ?
                '<span class="badge badge-success">'.trans('portal.opened').'</span>' :
                '<span class="badge badge-danger">'.trans('portal.closed').'</span>';

        })->editColumn('delivery_date', function ($value){

            return empty($value->delivery_date) ? '-' : getLocaleDate($value->delivery_date);

        })->editColumn('action', function ($value){

            $status = $value->status == true ? trans('portal.close_consignmet') : trans('portal.open_consignmet');
            return '<span class="dropdown">
                <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                    <i class="la la-cogs"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="'.route('consignment.show', $value->id).'">
                        <i class="la la-search"></i> '.trans('portal.show').'
                    </a>
                    '.(($this->roleCheck(config('settings.roles.uretici')) && $value->status==false)?'':'<a class="dropdown-item" href="'.route('consignment.status', $value->id).'"><i class="la la-truck"></i> '.$status.'</a>').
                    '<a class="dropdown-item" href="'.route('consignment.edit', $value->id).'">
                        <i class="la la-edit"></i> '.trans('portal.edit').'
                    </a>
                    <a
                        class="dropdown-item"
                        href="'.route('consignment.destroy', $value->id).'"
                        data-method="delete"
                        data-token="'.csrf_token().'"
                        data-confirm="'.trans('portal.delete_text').'"
                    >
                        <i class="la la-trash"></i> '.trans('portal.delete').'
                    </a>
                </div>
            </span>';

        })->rawColumns(['name', 'action', 'status', 'item_count'])->make(true);

    }

    public function packageZara(Request $request){

        if($request->has("consignmentId")){

            $packages = \App\Package::select(
                'id',
                'package_no',
                'model',
                'size',
                'load_type',
                'box_type_id',
                'status',
                'created_user_id',
                'created_at'
            )->where('consignment_id', $request->get('consignmentId'))
            ->with(['items','created_user'])
            ->orderBy('id', 'asc')
            ->get();

            // $consignment = Consignment::with(['company', 'packages', 'packages.items'])
            //     ->withCount(['items', 'packages'])
            //     ->where('id', '=', $request->get('consignmentId'))
            //     ->select('id')->get();

            return Datatables::of($packages)->editColumn('package_no', function ($value){

                return trans('portal.package').' '.$value->package_no;

            })->editColumn('items', function ($value){

                return count($value->items).' '.trans('portal.piece');

            })->editColumn('status', function ($value){

                return $value->status == 1 ?
                '<span class="badge badge-success">'.trans('portal.opened').'</span>' :
                '<span class="badge badge-danger">'.trans('portal.closed').'</span>';

            })->editColumn('desc', function ($value){

                return '-';

            })->editColumn('created_user_id', function ($value){

                return $value->created_user->name;

            })->rawColumns(['packages_no','items','status', 'desc','created_user','create_date'])->make(true);

        }else{

            return response()->json(['message' => 'Sevkiyat Bulunamadı'], 400);

        }

    }

    public function packageHm(Request $request){

        if($request->has("consignmentId")){

            $packages = \App\Package::select(
                'id',
                'package_no',
                'model',
                'size',
                'load_type',
                'box_type_id',
                'status',
                'created_user_id',
                'created_at'
            )->where('consignment_id', $request->get('consignmentId'))
            ->with(['items','created_user'])
            ->orderBy('id', 'asc')
            ->get();

            foreach($packages as $pValue){
                foreach($pValue->items as $item){

                    $d = \App\CompanyDb::where('gtin', $item->gtin)->first();

                    if(is_null($d)==false){
                        array_push($item->itemDetails, $d);
                    }

                }
            }

            return Datatables::of($packages)->editColumn('package_no', function ($value){

                return trans('portal.package').' '.$value->package_no;

            })->editColumn('size', function ($value){

                $itemDetailSize = array();
                foreach($value->items as $itemValue){
                    foreach($itemValue->itemDetails as $itemDetails){
                        $itemDetailSize[] = explode(',', $itemDetails->description)[1];
                    }
                }

                return count($itemDetailSize) > 0 ? $itemDetailSize : 'UND';

            })->editColumn('items', function ($value){

                return count($value->items).' '.trans('portal.piece');

            })->editColumn('status', function ($value){

                return $value->status == 1 ?
                '<span class="badge badge-success">'.trans('portal.opened').'</span>' :
                '<span class="badge badge-danger">'.trans('portal.closed').'</span>';

            })->editColumn('desc', function ($value){

                $itemDetailsDesc = array();
                foreach($value->items as $itemValue){
                    foreach($itemValue->itemDetails as $itemDetails){
                        $itemDetailsDesc[] = explode(',', $itemDetails->description)[0];
                    }
                }

                return count($itemDetailsDesc) > 0 ? $itemDetailsDesc : 'UND';

            })->editColumn('created_user_id', function ($value){

                return $value->created_user->name;

            })->rawColumns(['packages_no','items','status', 'desc','created_user','create_date'])->make(true);

        }else{

            return response()->json(['message' => 'Sevkiyat Bulunamadı'], 400);

        }

    }

    public function packageLevis(Request $request){

        if($request->has("consignmentId")){

            $packages = \App\Package::select(
                'id',
                'package_no',
                'model',
                'size',
                'load_type',
                'box_type_id',
                'status',
                'created_user_id',
                'created_at'
            )->where('consignment_id', $request->get('consignmentId'))
                ->with(['items','created_user'])
                ->orderBy('id', 'asc')
                ->get();

            foreach($packages as $pValue){
                foreach($pValue->items as $item){

                    $d = \App\CompanyLevisDb::where('gtin', $item->gtin)->first();

                    if(is_null($d)==false){
                        array_push($item->itemDetails, $d);
                    }

                }
            }

            return Datatables::of($packages)->editColumn('package_no', function ($value){

                return trans('portal.package').' '.$value->package_no;

            })->editColumn('product_code', function ($value){

                $itemDetailSize = array();
                foreach($value->items as $itemValue){
                    foreach($itemValue->itemDetails as $itemDetails){
                        $itemDetailSize[] = $itemDetails->product_code;
                    }
                }

                return count($itemDetailSize) > 0 ? $itemDetailSize : 'UND';

            })->editColumn('items', function ($value){

                return count($value->items).' '.trans('portal.piece');

            })->editColumn('status', function ($value){

                return $value->status == 1 ?
                    '<span class="badge badge-success">'.trans('portal.opened').'</span>' :
                    '<span class="badge badge-danger">'.trans('portal.closed').'</span>';

            })->editColumn('po', function ($value){

                $itemDetailsDesc = array();
                foreach($value->items as $itemValue){
                    foreach($itemValue->itemDetails as $itemDetails){
                        $itemDetailsDesc[] = $itemDetails->po;
                    }
                }

                return count($itemDetailsDesc) > 0 ? $itemDetailsDesc : 'UND';

            })->editColumn('created_user_id', function ($value){

                return $value->created_user->name;

            })->rawColumns(['packages_no','items','status', 'po','created_user','create_date'])->make(true);

        }else{

            return response()->json(['message' => 'Sevkiyat Bulunamadı'], 400);

        }

    }

    public function packageMs(Request $request){

        if($request->has("consignmentId")){

            $model = DB::table('ms_upc_cartons')->where(['consignment_id' => $request->get('consignmentId')])->get();
            foreach ($model as $item) {

                /**
                 * Bir UPC de okunması gereken toplam adeti hesaplar
                 */
                $totalCount = DB::table('ms_cartons')
                    ->where(['consignment_id' => $item->consignment_id])
                    ->where(['upc' => $item->upc])
                    ->select(DB::raw("SUM(singles) as sum"))
                    ->first();

                /**
                 * Bir UPCdeki okunan toplam etiket sayısını getirir
                 */
                $totalCounted = DB::table('ms_carton_epcs')
                    ->where([
                        'consigment_id' => $item->consignment_id,
                        'upc' => $item->upc
                    ])->count('*');

                $UndCounted = DB::table('ms_carton_epcs')
                    ->where([
                        'consigment_id' => $item->consignment_id,
                        'upc' => $item->upc,
                        ['gittinCheck', '=', 0]
                    ])->count('*');

                /**
                 * Upc
                 */
                $results = DB::select(DB::raw(
                    "select upc,cartonID,consignment_id,series,colour,singles,barcode,
                            (
                                select count(*) from
                                                    ms_carton_epcs mce
                                                where
                                                    mce.barcode = mc.barcode and
                                                    gittinCheck <> 0 and
                                                    consigment_id = $item->consignment_id
                            ) as counted,
                            (
                                select count(*) from
                                                    ms_carton_epcs mce
                                                where
                                                    mce.barcode = mc.barcode and
                                                    gittinCheck = 0 and
                                                    consigment_id = $item->consignment_id
                            ) as Undefinecounted,
                            case
                                when
                                    (
                                        select count(*) from
                                                            ms_carton_epcs mce
                                                        where mce.barcode = mc.barcode and
                                                              consigment_id = $item->consignment_id
                                    ) <> singles and
                                    (
                                        select count(*) from
                                                            ms_carton_epcs mce
                                                        where mce.barcode = mc.barcode and
                                                              consigment_id = $item->consignment_id
                                    ) <> 0
                                then
                                    'bg-danger text-white'
                                else
                                    ''
                            end as CssClass from
                                                ms_cartons mc
                                            where
                                                mc.consignment_id = :consignment_id and
                                                mc.upc = :upc"
                    ),
                        array('upc' => $item->upc,'consignment_id' => $item->consignment_id)
                );

                $cssBolean = false;
                foreach ($results as $cssItem) {
                    if ($cssItem->CssClass == 'bg-danger text-white')
                        $cssBolean = true;
                }

                $data['data'][] = [
                    'UPC' => $item->upc,
                    'SIZE' => $item->size,
                    'targetCount' => $totalCount->sum,
                    'counted' => $totalCounted - $UndCounted,
                    'undcounted' => $UndCounted,
                    'boxes' => json_encode($results),
                    'description' => $item->descriptions,
                    'baseRowClass' => $cssBolean ? 'bg-danger' : '',
                    'baseTextClass' => $cssBolean ? 'text-white' : ''
                ];
            }

            return json_encode($data);

        }else{

            return response()->json(['message' => 'Sevkiyat Bulunamadı'], 400);

        }

    }

    public function viewSor(Request $request){

        // Gelen consignee ye ait view id den read ekranı alınıyor.
        // Diğer seçeneği seçilirse default olan ekranın ekleme ekranı alınıyor.
        $viewCon = $request->post('consingneeId');

        if ($viewCon != 'other'){

            $consignee = \App\Consignee::find((int) $viewCon);
            $reading = \App\ViewScreen::where('id', $consignee->viewid)->limit(1)->get();
            $readScreen = $reading[0]['reading'];
            $other = false;

        }else{

            $defaultViewSor = \App\ViewScreen::where('status', 'Default')->first();
            if ($defaultViewSor){

                $readScreen = $defaultViewSor['reading'];
                $other = true;

            }
        }

        return response()->json(['view' => $readScreen, 'other' => $other]);

    }

    function get_absolute_path($path) {

        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();

        foreach ($parts as $part){
            if ('.' == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }

        return implode(DIRECTORY_SEPARATOR, $absolutes);

    }

}
