<?php

namespace App\Http\Controllers;

use App\BoxType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Helpers\OptionTrait;
use App\CompanyDb;
use App\CompanyMsDb;
use App\Helpers\NotificationTrait;
use App\ReadFile;
use App\ViewScreen;
//use App\Parser;
use Smalot\PdfParser\Parser;
use Shuchkin\SimpleXlsx\SimpleXLSX;
use App\ParserConfig;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
//use Validator;
use Validator,Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Cache\Factory;
use App\Http\Resources\Consignment as ConsignmentResource;

class ConsignmentsApiController extends Controller{

    use OptionTrait;
    use NotificationTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $userId = $request->get('gtin');
        $companyId = $request->get('company_id');
        $consigneeId = $request->get('consignee_id');
        $gtin = $request->get('gtin');

        // gelen kullanıcının actıgı tum sevkiyatlar
        $consignments = \App\Consignment::where('status', true)
            ->where('company_id', $companyId)
            ->where('consignee_id', $consigneeId)
            ->with(['consignee', 'order'])
            ->orderBy('id', 'desc')
            ->get();

        // sevkiyatların paket ve paket detay bilgileri
        if ($consignments){
            $data = array();
            foreach($consignments as $conKey => $conVal){

                $consignment = \App\Consignment::with(['company', 'packages', 'packages.items', 'order'])
                    ->withCount(['items', 'packages'])
                    ->find($conVal->id);

                if ($consignment){

                    $data = [
                        'title' => $consignment->name,
                        'consignment' => $consignment
                    ];
                    
                    foreach ($consignment->packages as $package) {
                        foreach ($package->items as $item) {
                            // gtin son 8 i upc ye eşit olanlar
                            $gtinEpcUzunluk = substr($item->gtin, - 8);
                            if (substr($gtinEpcUzunluk, 0,1) == 0 ){
                                $gtinEpcUzunluk = substr($item->gtin, - 7);
                            }
                            $d = \App\CompanyMsDb::where('upc', $gtinEpcUzunluk)->first();
                            
                            if(is_null($d)==false){
                                array_push($item->itemDetails, $d);
                            }
            
                        }
                    }


                }

            }

        }

        $gtinSor = \App\CompanyMsDb::where('upc', $gtin)->first();
        // echo '<pre>';
        // print_r($gtinSor);
        // exit();

        $test = array(
            'season' => $gtinSor->season,
            'description' => $gtinSor->description,
            'sds_code' => $gtinSor->sds_code,
            'story_desc' => $gtinSor->story_desc,
            'price' => $gtinSor->price,
            'qty_req' => $gtinSor->qty_req,
        );

        return new ConsignmentResource($test);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $attribute = array(
                'po_no' => trans('station.po_number'),
                //'country_code' => trans('station.country'),
                'item_count' => trans('station.product_quantity'),
                'consignee_id' => trans('station.consignee_name'),
                'delivery_date' => trans('station.delivery_date'),
                'user_id' => 'Kullanıcı Id',
                'db_list' => trans('station.file_upload')
                //'plate_no' => trans('station.plate_no')
            );

            $rules = array(
                'po_no' => 'required',
                //'country_code' => 'required',
                'item_count' => 'required|numeric|min:1',
                'consignee_id' => 'required',
                'delivery_date' => 'required|date|date_format:Y-m-d',
                // 'plate_no' => 'nullable',
                'db_list' => 'required|mimes:pdf|max:2048',
                'user_id' => 'required'
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ]);
            }

            $order = \App\Order::where('po_no', $request->post('po_no'))->first();
            $user = \App\User::where('id', $request->post('user_id'))->first();
            $durum = 'ok';
            
            if(!$order){

                $order = new \App\Order;
                $order->order_code = $this->autoGenerateOrderCode();
                $order->consignee_id = $request->post('consignee_id');
                $order->po_no = $request->post('po_no');
                $order->name = $request->post('name') ?? "null";
                $order->item_count = $request->post('item_count');
                $order->created_user_id = $user->id;
                $order->save();

            }
            
            if($order){
                
                $this->createLog('Order','portal.log_create_order', ['name' => $order->po_no], $order->id);

                $consigneeName = \App\Consignee::find((int) $request->post('consignee_id'));
                if ($consigneeName){
                    $conName = $consigneeName->name;
                }

                $consignment = new \App\Consignment;
                $consignment->order_id = $order->id;
                //$consignment->country_code = "RUW337";
                $consignment->company_id = $user->company_id;
                $consignment->name = $this->autoGeneratePoNo($order->id, $consignment->country_code, $conName);
                $consignment->delivery_date = date('Y-m-d', strtotime($request->post('delivery_date')));
                $consignment->consignee_id = $request->post('consignee_id') != 'other' ? $request->post('consignee_id') : $company->id;
                $consignment->item_count = $request->post('item_count');
                $consignment->created_user_id = $user->id;

                if($consignment->save()){
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
                            $fileModel->company_id = $user->company_id;
                            $fileModel->consignee_id = $request->post('consignee_id');
                            $fileModel->name = $fileName;
                            $fileModel->file_path = config('settings.media.files.full_path').$fileName;
                            $fileModel->created_user_id = $user->id;
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
                        'user_id' => $user->id,
                        'company_id' => $user->company_id
                    ]
                );

            }
        
            // session()->flash(
            //     'flash_message', 
            //     array(trans('station.successful'),trans('station.consignment_successfully'), 'success')
            // );
            
            $resData = array(
                'status' => $durum,
                'url' => route('station.index', ['consignment' => $consignment->id])
            );

        } catch (\Exception $e) {

            $resData = array(
                'status' => 'Hata'
            );

        }

        // return response()->json([
        //     'status' => 'ok',
        //     'url' => route('station.index', ['consignment' => $consignment->id])
        // ]);

        return new ConsignmentResource($resData);

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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
