<?php

namespace App\Http\Controllers;

use App\Helpers\OptionTrait;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\XmlFileRepo;
use App\TxtFileRepo;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class XmlFileRepoController extends Controller{

    /**
     * Kaynaktan bir liste görüntüler.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){

        /*$xmlFetch = xmlFetch();
        if ($xmlFetch == 'ok'){

            return redirect()->route('station.ms');

        }*/

        ini_set('memory_limit','123999M');
        $ftp = Storage::disk('custom-ftp');
        $files = Storage::disk('custom-ftp')->files('_files');

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

        return redirect()->route('station.ms');

    }

    public function _index(){

        $xmlFetch = xmlFetch();
        if ($xmlFetch == "ok"){

            return redirect()->route('station.ms');

        }

    }

}
