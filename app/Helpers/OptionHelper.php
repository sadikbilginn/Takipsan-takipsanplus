<?php

use App\Helpers\OptionTrait;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\XmlFileRepo;
use App\TxtFileRepo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

if (!function_exists('getSettings')) {
    function getSettings($key, $locale = false)
    {
        $value = config('db_settings.' . $key);

        if($locale)
        {
            $arr_tmp = json_decode($value, true);
            if (is_array($arr_tmp)) {
                $value = $arr_tmp[ $locale ];
            }
        }

        return $value;
    }
}

/*
if (!function_exists('customPermissionCheck')) {
    function customPermissionCheck($permissionId)
    {
        $return = false;

        if (in_array($permissionId, session('user_custom_permission_ids'))) {
            $return = true;
        }

        if (auth()->user()->is_admin) {
            $return = true;
        }

        return $return;
    }
}
*/

/**
 * Kullanıcıya verilen roller ile user istenilen rol bilgisini
 *  kontrol eder, user erişim izni varsa true döner.
 *
 * @param  integer  $roleId
 * @return boolean $return
 */
if (!function_exists('roleCheck')) {

    function roleCheck($roleId):bool
    {
        $return = false;

        $roleIds =  session('user_roles')->pluck('id')->toArray();
        if(in_array($roleId, $roleIds)){
            $return = true;
        }
        return $return;
    }
}

if (!function_exists('MenuRoleCheck')) {
    function MenuRoleCheck($menu_roles, $user_roles)
    {
        $return = false;

        $menu_roles_ids = [];
        foreach ($menu_roles as $key => $value) {
            $menu_roles_ids[] = $value->id;
        }

        $user_roles_ids = [];
        foreach ($user_roles as $key => $value) {
            $user_roles_ids[] = $value->id;
        }

        $x = array_intersect($menu_roles_ids, $user_roles_ids);

        if (count($x) > 0) {
            $return = true;
        }
        return $return;
    }
}

if (!function_exists('ChildMenuCheckUrl')) {
    function ChildMenuCheckUrl($menus)
    {

        $return = false;
        $menu_urls = [];
        if (isset($menus['uri'])) {
            $menu_urls[] = $menus['uri'];
            if (isset($menus['children'])) {
                foreach ($menus['children'] as $key2 => $value2) {
                    if (isset($value2['uri'])) {
                        $menu_urls[] = $value2['uri'];
                        if (isset($value2['children'])) {
                            foreach ($value2['children'] as $key3 => $value3) {
                                if (isset($value3['uri'])) {
                                    $menu_urls[] = $value3['uri'];
                                }
                            }
                        }
                    }
                }
            }
        }

        $menu_urls = array_unique($menu_urls);

        foreach ($menu_urls as $menu_url) {
            if (request()->is($menu_url) || request()->segment(1) == $menu_url) {
                $return = true;
                break;
            }
        }

        return $return;
    }
}

if (!function_exists('GetMethodFont')) {
    function GetMethodFont($method)
    {
        $return = false;

        switch ($method) {
            case "GET" :
                $return = "kt-font-info";
                break;
            case "POST" :
                $return = "kt-font-success";
                break;
            case "PUT" :
            case "PATCH" :
                $return = "kt-font-warning";
                break;
            case "DELETE" :
                $return = "kt-font-danger";
                break;
        }
        return $return;
    }
}

if (!function_exists('ControllerGetShowLink')) {
    function ControllerGetShowLink($model, $controller, $action, $text)
    {
        $return = false;

        if($action == 'destroy'){
            $return = $text;
        }else{

            switch ($model) {
                case "Company" :

                    $return = '<a href="'.route('company.index').'">'.$text.'</a>';

                    break;
                case "Order" :

                    $return = '<a href="'.route('order.index').'">'.$text.'</a>';

                    break;
                case "Consignment" :

                    $return = '<a href="'.route('consignment.index').'">'.$text.'</a>';

                    break;
                case "Consignee" :

                    $return = '<a href="'.route('consignee.index').'">'.$text.'</a>';

                    break;
                default:
                    $return = $text;
            }
        }

        return $return;
    }
}

if (!function_exists('consignmentStatusPercent')) {
    function consignmentStatusPercent($a = 0, $b = 0)
    {

        if($a != 0 && $b != 0){
            $x = ($a / $b) * 100;
        }elseif($a != 0 && $b == 0){
            $x = $a;
        }elseif($a == 0){
            $x = 0;
        }else{
            $x = 0;
        }

        return floor($x);
    }
}

if (!function_exists('consignmentProgressBg')) {
    function consignmentProgressBg($percent):string
    {

        $bg = "bg-danger";

        if($percent > 0 && $percent <=25){
            $bg = "bg-danger";
        }

        if($percent > 25 && $percent <=75){
            $bg = "bg-warning";
        }

        if($percent > 75){
            $bg = "bg-success";
        }

        return $bg;
    }
}

if (!function_exists('getChartBg')) {
    function getChartBg($key):string
    {

        $bg = "kt-bg-danger";

        if($key == 0){
            $bg = "kt-bg-danger";
        }

        if($key == 1){
            $bg = "kt-bg-brand";
        }

        if($key == 2){
            $bg = "kt-bg-success";
        }

        return $bg;
    }
}

if (!function_exists('tdCompletion')) {
    function tdCompletion($count, $index):string
    {
        $missing = 0;
        $row        = ceil($count / $index);
        $col        = floor($row * $index);
        $missing    = floor($col - $count);

        return $missing;
    }
}

if (!function_exists('getMacAddress')) {
    function getMacAddress():string
    {
        $exec = exec('getmac');
        $mac = strtok($exec, ' ');

        return $mac;
    }
}

if (!function_exists('getLocaleDate')) {
    function getLocaleDate($date)
    {
        if(strlen($date) == 10){
            if(app()->getLocale() == 'tr'){
                $date = date('d-m-Y', strtotime($date));
            }else{
                $date = date('Y-m-d', strtotime($date));
            }
        }else{
            if(app()->getLocale() == 'tr'){
                $date = date('d-m-Y H:i:s', strtotime($date));
            }else{
                $date = date('Y-m-d H:i:s', strtotime($date));
            }
        }


        return $date;
    }
}

if (!function_exists('getEpcListSize')) {
    function getEpcListSize($epcList)
    {
        $list = [];

        if(is_object($epcList)){
            foreach ($epcList as $key => $value){

                if(isset($list[getEpcSize($value->epc)])){
                    $list[getEpcSize($value->epc)] = $list[getEpcSize($value->epc)] + 1;
                }else{
                    $list[getEpcSize($value->epc)] = 1;
                }
            }
        }

        return $list;
    }
}
$sizes = [];
if (!function_exists('getPackageItemCount')) {
    function getPackageItemCount($package,$gtin)
    {
        $count = 0;

        foreach ($package->items as $item) {
            if($item->gtin==$gtin){
                $count = $count+1;
            }
        }

        return $count;
    }
}

if (!function_exists('getPackageTypeCount')) {
    function getPackageTypeCount($packages,$packageType)
    {
        $count = 0;
        foreach ($packages as $package) {

                if($packageType == $package->box_type_id){
                    $count += count($package->items);
                }
        }

        return $count;
    }
}

if (!function_exists('getPackageMsmrnt')) {
    function getPackageMsmrnt($boxTypes,$packageType)
    {
        foreach ($boxTypes as $boxTypes) {

                if($packageType == $boxTypes->name){
                    return $boxTypes->length.'*'.$boxTypes->width.'*'.$boxTypes->height;
                }
        }

        return null;
    }
}

if (!function_exists('getPackageNosByType')) {
    function getPackageNosByType($packages,$packageType)
    {
        $sPNo = 0;
        $ePNo = 0;
        foreach ($packages as $package) {

                if($packageType == $package->box_type_id){
                    if ($sPNo == 0) {
                        $sPNo = $package->package_no;
                    }
                    $ePNo = $package->package_no;
                }
        }

        if ($sPNo == $ePNo) {
            return strval($sPNo);
        }
        else
        {
            return strval($sPNo)."-".strval($ePNo);
        }
    }
}

if (!function_exists('getPackageSizeCount')) {
    function getPackageSizeCount($packages,$size,$packageType)
    {
        $count = 0;
        foreach ($packages as $package) {

            if($packageType == $package->box_type_id)
            {
                foreach ($package->items as $item) {
                    if(isset($item->itemDetails) && count($item->itemDetails)>0)
                    {
                        if(preg_replace('/\s+/', '', $item->itemDetails[0]->sds_code)==$size){
                            $count = $count+1;
                        }
                    }
                    else
                    {
                        if ($size == "UND") {
                            $count = $count+1;
                        }
                    }
                }
            }
        }

        return $count;
    }
}

if (!function_exists('getAssrCount')) {
    function getAssrCount($packages)
    {
        $count = 0;
        foreach ($packages as $package) {
            if ($package->load_type == "Assortment") {
                foreach ($package->items as $item) {
                    if($item->gtin!= null){
                        return getPackageItemCount($package,$package->items[0]->gtin);
                    }
                }

            }
        }
        return $count;
    }
}

if (!function_exists('getPackageCountByLT')) {
    function getPackageCountByLT($packages,$loadType)
    {
        $count = 0;
        foreach ($packages as $package) {

            if ($package->load_type == $loadType) {
                    $count = $count + 1;
            }
        }
        return $count;
    }
}

if (!function_exists('getItemCount')) {
    function getItemCount($packages,$gtin)
    {
        $count = 0;
    foreach ($packages as $package) {

        foreach ($package->items as $item) {
            if($item->gtin==$gtin){
                $count = $count+1;
            }
        }
    }
        return $count;
    }
}

if (!function_exists('getEpcSize')) {
    function getEpcSize($epc)
    {
        $retVal = false;

        if($epc && strlen($epc) >= 24){
            $sizeHex        = substr($epc,12, 3);
            $retVal         = intval($sizeHex, 16);
            $retVal         = $retVal >> 3;
        }

        $size = 'UND - ' . $retVal;

        switch ($retVal) {

            case 1 : $size = 'XS - 01 - 100';    break;
            case 2 : $size = 'S';                break;
            case 3 : $size = 'M';                break;
            case 4 : $size = 'L';                break;
            case 5 : $size = 'XL';               break;
            case 6 : $size = 'XXL';              break;
            case 7 : $size = 'XXXL';             break;
            case 8 : $size = 'XXS';              break;
            case 10 : $size = 'XS';              break;
            case 32 : $size = '32';              break;
            case 34 : $size = '34';              break;
            case 36 : $size = '36';              break;
            case 38 : $size = '38';              break;
            case 40 : $size = '40';              break;
            case 42 : $size = '42';              break;
            case 44 : $size = '44';              break;
            case 46 : $size = '46';              break;
            case 48 : $size = '48';              break;
            case 75 : $size = '75';              break;
            case 85 : $size = '85';              break;
            case 95 : $size = '95';              break;
            case 96 : $size = 'L - XL';          break;
            case 97 : $size = 'S-M';             break;
            case 98 : $size = 'M - L';           break;
        }

        return $size;
    }
}

function getOrderedBySize(array $data): array {
    $result = [];

    //foreach (["XXS", "XS", "S", "M", "L", "XL", "XXL", "XXXL", "UND"] as $key) {
    foreach (["XXS", "XS", "S", "M", "L", "XL", "XXL", "XXXL", "134/140", "146/152", "158/164", "170", "UND"] as $key) {
        if (array_key_exists($key, $data)) {
            //echo 'helper_'.$data[$key].'<br>';
            $result[$key] = $data[$key];
        }
    }

    //exit();

    return $result;
}

function _xmlFetch(){

    return "ok";

}

function xmlFetch(){

    // xmlFileRepoController ile aynı

    ini_set('memory_limit','123999M');
    $ftp = Storage::disk('custom-ftp')->path();
    $files = Storage::disk('custom-ftp')->files('_files')->path();

    $fixedPath = "/home/admin/domains/takipsanplus.com/public_html/msfilerepo/";

    $country = null;
    $dep = null;
    $poNumber = null;
    $xmlFixArray = [];
    $stroke = null;
    $incotermDate = null;
    $packType = null;
    $supplierDesc = null;
    $ratioPackIndicator = null;
    foreach($files as $file){

        $fileControl = Storage::disk('custom-ftp')->exists($file);
        if ($fileControl){

            $url = "http://msfilerepo.takipsanplus.com/".$file;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_ENCODING, 0);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ch,CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            $filePull = curl_exec($ch);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $pathinfo = pathinfo($url);
            $extension = $pathinfo['extension'];
            curl_close($ch);

            if ($extension == 'zip'){

                $zip = new ZipArchive();
                $zipFile = $zip->open($fixedPath.$file);
                if ($zipFile === true){

                    $extract = $zip->extractTo($fixedPath.'_files/');
                    if ($extract){
                        rename($fixedPath.'_files/'.$pathinfo['basename'], $fixedPath.'_files/extracteds/'.date('d.m.y').'_'.$pathinfo['basename']);
                        //echo $extract.'ok';
                    }else{
                        echo 'nok';
                    }

                    $zip->close();

                }else{
                    echo 'failed';
                }

            }

            if ($extension == 'xml'){

                $xmlFileRead = simplexml_load_string($filePull);
                if ($xmlFileRead){

                    foreach($xmlFileRead as $xmlFileKey => $xmlFileContent){

                        $country = $xmlFileContent->Country;
                        $dep = $xmlFileContent->Dept;
                        $poNumber = $xmlFileContent->PONumber;
                        $stroke = $xmlFileContent->Stroke;
                        $incotermDate = $xmlFileContent->IncotermDate;
                        $packType = $xmlFileContent->PackType;
                        $supplierDesc = $xmlFileContent->SupplierDescription;
                        $ratioPackIndicator = $xmlFileContent->RatioPackIndicator;

                        $xmlFixArray[] =  [
                            'poNumber' => strval($poNumber),
                            'stroke' => strval($stroke)
                        ];

                        $xmlCartonArray = [];
                        foreach($xmlFileContent->Cartons->Carton as $carton){

                            $xmlUpcArray = [];

                            $cartonID = $carton->CartonID;
                            $series = $carton->Series;
                            $colour = $carton->Colour;
                            $singles = $carton->Singles;
                            $grossWeight = $carton->GrossWeight;
                            $barcode = $carton->SSCCBarCode;
                            $cartonUPCType = $carton->CartonUPCType;
                            $goh = $carton->GOH;
                            $srp = $carton->SRP;
                            $crc = $carton->CRC;

                            foreach($carton->UPCs->UPCItem as $upcItem){

                                $upc = $upcItem->UPC;
                                $size = $upcItem->Size;
                                $mixedQuantity = $upcItem->MixedQuantity;
                                $poUpcQuantity = $upcItem->POUPCQuantity;

                                $xmlUpcArray[] = [
                                    "upc" => strval($upc),
                                    "size" => strval($size),
                                    "mixedQuantity" => strval($mixedQuantity),
                                    "poUpcQuantity" => strval($poUpcQuantity),
                                ];

                                // echo '<pre>';
                                // print_r($data);

                            }

                            $xmlCartonArray[] = [
                                'cartonID' => strval($cartonID),
                                'series' => strval($series),
                                'colour' => strval($colour),
                                'singles' => strval($singles),
                                'grossWeight' => strval($grossWeight),
                                'barcode' => strval($barcode),
                                'cartonUPCType' => strval($cartonUPCType),
                                'goh' => strval($goh),
                                'srp' => strval($srp),
                                'crc' => strval($crc),
                                'upcs' => $xmlUpcArray
                            ];

                        }

                        $xmlFileRepoQuery =\App\XmlFileRepo::where('poNumber', $poNumber)
                            ->where('stroke', $stroke)
                            ->orderBy('id', 'asc')
                            ->get();

                        if (count($xmlFileRepoQuery) == 0){

                            $xmlFileRepo = new XmlFileRepo;
                            $xmlFileRepo->country = strval($country);
                            $xmlFileRepo->dep = strval($dep);
                            $xmlFileRepo->poNumber = strval($poNumber);
                            $xmlFileRepo->stroke = strval($stroke);
                            $xmlFileRepo->incotermDate = strval($incotermDate);
                            $xmlFileRepo->packType = strval($packType);
                            $xmlFileRepo->supplierDesc = strval($supplierDesc);
                            $xmlFileRepo->ratioPackIndicator = strval($ratioPackIndicator);
                            $xmlFileRepo->cartons = json_encode($xmlCartonArray);
                            $xmlFileRepo->save();

                            //XmlFileRepo::create($data);

                        }

                    }

                    // echo '<pre>';
                    // print_r($data);
                    //echo json_encode($xmlCartonArray);
                    //echo json_encode($xmlUpcArray);

                }

            }

        }else{

            echo 'Dosya Okunamadı';

        }

    }

    // $xmlFileRepoQuery = DB::table('xml_file_repos')
    //     ->select('*')
    //     ->join('txt_file_repos', function($join){ $join->on('txt_file_repos.xmlid', '=', 'xml_file_repos.id');})
    //     ->where('xml_file_repos.poNumber', "2012107117")
    //     ->orderBy('xml_file_repos.id', 'ASC')
    //     ->get();

    // echo '<pre>';
    // print_r($xmlFileRepoQuery);
    // exit();

    foreach($xmlFixArray as $xmlFix){

        $xmlPoNumber = $xmlFix['poNumber'];
        $xmlStroke = $xmlFix['stroke'];

        $xmlFileRepoQuery =\App\XmlFileRepo::where('poNumber', $xmlPoNumber)
            ->where('stroke', $xmlStroke)
            ->orderBy('id', 'asc')
            ->get();

        $xmlId = $xmlFileRepoQuery[0]['id'];
        // echo '<pre>';
        // print_r($xmlFileRepoQuery);

        foreach($files as $filesTxt){

            $fileControl = Storage::disk('custom-ftp')->exists($filesTxt);
            if ($fileControl){

                $url = "http://msfilerepo.takipsanplus.com/".$filesTxt;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_ENCODING, 0);
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
                curl_setopt($ch,CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                $filePull = curl_exec($ch);
                $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
                $pathinfo = pathinfo($url);
                $extension = $pathinfo['extension'];
                curl_close($ch);

                if ($extension == 'txt'){

                    $txtFileRead = file_get_contents($fixedPath.$filesTxt, false, null);
                    $txtContents = explode("\n", $txtFileRead);
                    $txtContentsArr = array();

                    foreach($txtContents as $k => $txtContent){

                        //if (strlen($txtContent) > 300){
                        //$i = 0;
                        $txtContentsArr[] = $txtContent;
                        //echo $txtContent.'_'.strlen($txtContent).'_'.$k.'_sad<br>';
                        //}

                    }

                    if (isset($txtContentsArr)){

                        $sortRest = array();
                        $sortRestSize = array();
                        foreach($txtContentsArr as $key => $txtContentValue){

                            if (strlen($txtContentValue) >= 50 && strlen($txtContentValue) > 300){

                                $p1 = explode(" ", $txtContentValue);
                                $poNo = substr($p1[0], (-strlen($xmlPoNumber) -3), 10);
                                $stroke = substr($p1[6], 0, strlen($xmlStroke));
                                if ($poNo == $xmlPoNumber && $stroke == $xmlStroke){
                                    //echo $txtContentValue.'_ana_satir<br>';
                                    $sortRest[] = $txtContentValue;

                                }

                            }elseif(strlen($txtContentValue) >= 50 && strlen($txtContentValue) < 300){

                                $p1 = explode(" ", $txtContentValue);
                                //echo $p1[0].'<br>';
                                $poNo = substr($p1[0], 4, 10);
                                $orderQuantity = substr($p1[0], 25, 10);
                                $primarySize = substr($p1[0], 35,5);
                                $totalQuantity = 0;
                                //echo $orderQuantity.'-'.$primarySize.'<br>';
                                if ($poNo == $xmlPoNumber){
                                    //echo $txtContentValue.'_diger_satirlar<br>';
                                    $totalQuantity = $totalQuantity + $orderQuantity;
                                    $sortRestSize[] = [
                                        'orderQuantity' =>  $orderQuantity,
                                        'primarySize' => $primarySize,
                                    ];
                                }

                            }

                        }

                        if (isset($sortRest) && !empty($sortRest) > 0){

                            $totalQuantity = 0;
                            foreach($sortRestSize as $sRestSize){
                                $totalQuantity = $totalQuantity + $sRestSize['orderQuantity'];
                            }

                            $sortRestSize[] = [
                                'totalQuantity' => $totalQuantity
                            ];

                            foreach ($sortRest as $sValue){

                                //echo $sValue.'<br>';
                                $recordType = substr($sValue, 0, 2);
                                $buid = substr($sValue, 1, 3);
                                $poNumber = substr($sValue, 4,10);
                                $poVersionNumber = substr($sValue, 14, 2);
                                //O – Original, A – amended, C –cancelled.
                                $poStatusType = substr($sValue, 16, 1);
                                $dateLastAmended = substr($sValue, 22, 8);
                                $departmentCode = substr($sValue, 30, 4);
                                $strokeNumber = substr($sValue, 34, 5);
                                $colourCode = substr($sValue, 39, 3);
                                $departmentDesc = substr($sValue, 42, 30);
                                $strokeDesc = substr($sValue, 72, 30);
                                $colourDesc = substr($sValue, 102, 16);
                                $story = substr($sValue, 118, 30);
                                $commodityCode = substr($sValue, 148, 15);
                                $season = substr($sValue, 163, 4);
                                $phase = substr($sValue, 167, 2);
                                $deliveryDate = substr($sValue, 169, 8);
                                $cargoReadyDate = substr($sValue, 177, 8);
                                $franchiseOrder = substr($sValue, 185, 1);
                                $manufacturerCode = substr($sValue, 186, 5);
                                $manufacturerDesc = substr($sValue, 191, 30);
                                $factoryCode = substr($sValue, 221, 10);
                                $factoryDescription = substr($sValue, 231, 30);
                                $freightManagerDesc = substr($sValue, 261, 30);
                                $paymentMethod = substr($sValue, 291, 30);
                                $paymentTerms = substr($sValue, 321, 30);
                                $incotermType = substr($sValue, 351, 30);
                                $contractNo = substr($sValue, 381, 8);
                                $countryId = substr($sValue, 389, 3);
                                $countryDescription = substr($sValue, 392, 30);
                                $region = substr($sValue, 422, 2);
                                $portLoadingCode = substr($sValue, 424, 5);
                                $freightId = substr($sValue, 429, 4);
                                $freightDesc = substr($sValue, 433, 20);
                                $paymentCurrency = substr($sValue, 453, 30);
                                $shipmentMethod = substr($sValue, 483, 30);
                                //B = Boxed,H = Hanging,C = Converted,D = Boxed to Tote,P = Converted when picked for despatch
                                $packType = substr($sValue, 513, 1);
                                $reprocessorIndicator = substr($sValue, 514, 10);
                                $orderNotes = substr($sValue, 524, 100);
                                $finalWarehouseId = substr($sValue, 624, 4);
                                $finalWarehouseDesc = substr($sValue, 628, 20);
                                $destination = substr($sValue, 648, 25);

                                $txtFileRepoQuery =\App\TxtFileRepo::where('xmlid', $xmlId)
                                    ->orderBy('id', 'asc')
                                    ->get();

                                if (count($txtFileRepoQuery) == 0){

                                    $txtFileRepo = new TxtFileRepo;
                                    $txtFileRepo->xmlid = $xmlId;
                                    $txtFileRepo->recordType = $recordType;
                                    $txtFileRepo->buid = $buid;
                                    $txtFileRepo->poVersionNumber = $poVersionNumber;
                                    $txtFileRepo->poStatusType = $poStatusType;
                                    $txtFileRepo->dateLastAmended = $dateLastAmended;
                                    $txtFileRepo->departmentCode = $departmentCode;
                                    $txtFileRepo->colourCode = $colourCode;
                                    $txtFileRepo->departmentDesc = $departmentDesc;
                                    $txtFileRepo->strokeDesc = $strokeDesc;
                                    $txtFileRepo->colourDesc = $colourDesc;
                                    $txtFileRepo->story = $story;
                                    $txtFileRepo->commodityCode = $commodityCode;
                                    $txtFileRepo->season = $season;
                                    $txtFileRepo->phase = $phase;
                                    $txtFileRepo->deliveryDate = $deliveryDate;
                                    $txtFileRepo->cargoReadyDate = $cargoReadyDate;
                                    $txtFileRepo->franchiseOrder = $franchiseOrder;
                                    $txtFileRepo->manufacturerCode = $manufacturerCode;
                                    $txtFileRepo->factoryDescription = $factoryDescription;
                                    $txtFileRepo->freightManagerDesc = $freightManagerDesc;
                                    $txtFileRepo->paymentMethod = $paymentMethod;
                                    $txtFileRepo->paymentTerms = $paymentTerms;
                                    $txtFileRepo->incotermType = $incotermType;
                                    $txtFileRepo->contractNo = $contractNo;
                                    $txtFileRepo->countryId = $countryId;
                                    $txtFileRepo->countryDescription = $countryDescription;
                                    $txtFileRepo->region = $region;
                                    $txtFileRepo->portLoadingCode = $portLoadingCode;
                                    $txtFileRepo->freightId = $freightId;
                                    $txtFileRepo->freightDesc = $freightDesc;
                                    $txtFileRepo->paymentCurrency = $paymentCurrency;
                                    $txtFileRepo->shipmentMethod = $shipmentMethod;
                                    $txtFileRepo->reprocessorIndicator = $reprocessorIndicator;
                                    $txtFileRepo->orderNotes = $orderNotes;
                                    $txtFileRepo->finalWarehouseId = $finalWarehouseId;
                                    $txtFileRepo->finalWarehouseDesc = $finalWarehouseDesc;
                                    $txtFileRepo->destination = $destination;
                                    $txtFileRepo->totalSizeQuantity = json_encode($sortRestSize);
                                    $txtFileRepo->save();

                                }

                            }

                        }

                    }

                }

            }else{
                echo 'Dosya Okunamadı<br>';
            }

        }

    }

    return 'ok';

}

function _socketCon(){

    $host = 'localhost';
    $port = '8025';

}

function GetGTINFromEPC($epc){

    $length = strlen($epc);
    $binaryEpc = "";
    for ($i=0; $i<$length; $i++) {
        $binaryEpc .= substr("0000".decbin(hexdec($epc[$i])), -4);
    }

    $companyBinary = substr($binaryEpc, 14, 20);
    $itemBinary = substr($binaryEpc, 34, 24);

    $company = bindec($companyBinary);
    $item = bindec($itemBinary);
    if(strlen($item) < 6){
        $item = "0".$item;
    }

    $gtin = $company.$item;
    $dual = intval(substr($gtin, 1, 1)) +
            intval(substr($gtin, 3, 1)) +
            intval(substr($gtin, 5, 1)) +
            intval(substr($gtin, 7, 1)) +
            intval(substr($gtin, 9, 1)) +
            intval(substr($gtin, 11, 1));
    $odd =  intval(substr($gtin, 0, 1)) +
            intval(substr($gtin, 2, 1)) +
            intval(substr($gtin, 4, 1)) +
            intval(substr($gtin, 6, 1)) +
            intval(substr($gtin, 8, 1)) +
            intval(substr($gtin, 10, 1));
    $sum = ($dual * 3) + $odd;
    $digit = (ceil($sum / 10) * 10) - $sum;
    $gtin = $gtin.$digit;

    if($gtin[0] == '0'){
        $gtin  = substr($gtin,1,strlen($gtin));
    }

    return $gtin;

}

function GetGTINFromEPCHb($epc)
{

    $length = strlen($epc);
    $binaryEpc = "";
    for ($i = 0; $i < $length; $i++) {
        $binaryEpc .= substr("0000" . decbin(hexdec($epc[$i])), -4);
    }

    // echo $binaryEpc;
    // exit();

    $companyBinary = substr($binaryEpc, 15, 23);
    $itemBinary = substr($binaryEpc, 41, 17);

    $company = bindec($companyBinary);
    $item = bindec($itemBinary);
    // if(strlen($item) < 6){
    //     $item = "0".$item;
    // }

    $gtin = $company . $item;
    $dual = intval(substr($gtin, 1, 1)) +
        intval(substr($gtin, 3, 1)) +
        intval(substr($gtin, 5, 1)) +
        intval(substr($gtin, 7, 1)) +
        intval(substr($gtin, 9, 1)) +
        intval(substr($gtin, 11, 1));
    $odd = intval(substr($gtin, 0, 1)) +
        intval(substr($gtin, 2, 1)) +
        intval(substr($gtin, 4, 1)) +
        intval(substr($gtin, 6, 1)) +
        intval(substr($gtin, 8, 1)) +
        intval(substr($gtin, 10, 1));
    $sum = ($dual * 3) + $odd;
    $digit = (ceil($sum / 10) * 10) - $sum;
    $gtin = $gtin . $digit;

    if ($gtin[0] == '0') {
        $gtin = substr($gtin, 1, strlen($gtin));
    }

    return $gtin;

}

function GetGTINFromEPCTarget($epc)
{

    $length = strlen($epc);
    $binaryEpc = "";
    for ($i = 0; $i < $length; $i++) {
        $binaryEpc .= substr("0000" . decbin(hexdec($epc[$i])), -4);
    }

    $companyBinary = substr($binaryEpc, 14, 24);
    $itemBinary = substr($binaryEpc, 39, 19);

    $company = bindec($companyBinary);
    $item = bindec($itemBinary);
//    if(strlen($item) < 6){
//     $item = "0".$item;
//    }

    $gtin = '0'.$company . $item;
    $dual = intval(substr($gtin, 1, 1)) +
        intval(substr($gtin, 3, 1)) +
        intval(substr($gtin, 5, 1)) +
        intval(substr($gtin, 7, 1)) +
        intval(substr($gtin, 9, 1)) +
        intval(substr($gtin, 11, 1));

    $odd = intval(substr($gtin, 0, 1)) +
        intval(substr($gtin, 2, 1)) +
        intval(substr($gtin, 4, 1)) +
        intval(substr($gtin, 6, 1)) +
        intval(substr($gtin, 8, 1)) +
        intval(substr($gtin, 10, 1));

    $sum = ($dual * 3) + $odd;
    $digit = (ceil($sum / 10) * 10) - $sum;
    $gtin = $gtin . $digit;

    if ($gtin[0] == '0') {
        $gtin = substr($gtin, 1, strlen($gtin));
    }

    return $gtin;

}
