<?php

namespace App\Http\Controllers;

// require_once '../vendor/autoload.php';

use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use Shuchkin\SimpleXlsx\SimpleXLSX;
use Shuchkin\SimpleXLS;
use Illuminate\Support\Facades\Auth;
//use File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
//use Illuminate\Support\Facades\URL;
//use Illuminate\Support\ServiceProvider;
//require_once '../vendor/shuchkin/simplexlsx/src/SimpleXLSX.php';
//include(__DIR__."/../vendor/shuchkin/simplexlsx/src/SimpleXLSX.php");
//namespace Shuchkin\Simplexlsx\Src\SimpleXLSX


class TestController extends Controller{

    public function index(){
        phpinfo();
        exit();

        // $pdf_file = 'station/4907M.pdf';
        // if (!is_readable($pdf_file)) {
        //     print("Error: file does not exist or is not readable: $pdf_file\n");
        //     return;
        // }

        // $c = curl_init();

        // $cfile = curl_file_create($pdf_file, 'application/pdf');

        // //pdf convert api kullanici bilgileri
        // // from https://pdftables.com/api
        // //ali.gulcan@takipsan.com
        // //takipsan3535

        // $apikey = 'iui7yu3u2ffp';
        // curl_setopt($c, CURLOPT_URL, "https://pdftables.com/api?key=$apikey&format=xlsx-single");
        // curl_setopt($c, CURLOPT_POSTFIELDS, array('file' => $cfile));
        // curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($c, CURLOPT_FAILONERROR,true);
        // curl_setopt($c, CURLOPT_ENCODING, "gzip,deflate");

        // $result = curl_exec($c);

        // if (curl_errno($c) > 0) {
        //     print('Error calling PDFTables: '.curl_error($c).PHP_EOL);
        // } else {
        //   // save the CSV we got from PDFTables to a file
        //   file_put_contents ($pdf_file . ".xlsx", $result);
        // }

        // curl_close($c);


        //  $PDFParser = new Parser();



    }

    public function oku(){

        ini_set('error_reporting', E_ALL );
        ini_set('display_errors', 1 );

        echo '<pre>';
        if ( $xls = SimpleXLS::parseFile('station/test.xls') ) {
            print_r( $xls->rows() );
            // echo $xls->toHTML();
        } else {
            echo SimpleXLS::parseError();
        }

        exit();

        if ( $xlsx = \SimpleXLSX::parse('station/test.xlsx') ) {

            foreach ($xlsx->rows() as $sKey => $sValue) {

                if ($sKey > 0) {

                    if ( !empty(array_filter($sValue))) {

                        if (
                            $sValue[0] != "" &&
                            $sValue[1] != "" &&
                            $sValue[2] != "" &&
                            $sValue[3] != "" &&
                            $sValue[4] != "" &&
                            $sValue[5] != ""
                        ){

                            $po_number = $sValue[0];
                            $vendor_id = $sValue[1];
                            $department = $sValue[2];
                            $class = $sValue[3];
                            $item = $sValue[4];
                            $item_description = $sValue[5];
                            $vendor_style = $sValue[6];
                            $color = $sValue[7];
                            $size = $sValue[8];
                            $item_barcode = $sValue[9];
                            $vcp_quantity = $sValue[10];
                            $ssp_quantity = $sValue[11];
                            $total_item_qty = $sValue[12];
                            $item_unit_cost = $sValue[13];
                            $item_unit_retail = $sValue[14];
                            $country_origin = $sValue[15];
                            $hs_tariff = $sValue[16];
                            $shipping_documents = $sValue[17];
                            $assortment_item = $sValue[18];
                            $component_department = $sValue[19];
                            $component_class = $sValue[20];
                            $component_item = $sValue[21];
                            $component_item_desciprtion = $sValue[22];
                            $component_style = $sValue[23];
                            $component_assort_qty = $sValue[24];
                            $component_item_total_qty = $sValue[25];
                            $item_changed_date = $sValue[26];

                            /*po_number
                            vendor_id
                            department
                            class
                            item
                            item_description
                            vendor_style
                            color
                            size
                            item_barcode
                            vcp_quantity
                            ssp_quantity
                            total_item_qty
                            item_unit_cost
                            item_unit_retail
                            country_origin
                            hs_tariff
                            shipping_documents
                            assortment_item
                            component_department
                            component_class
                            component_item
                            component_item_desciprtion
                            component_style
                            component_assort_qty
                            component_item_total_qty
                            item_changed_date*/

                            echo '<pre>';
                            print_r($sValue);
                        }

                    }

                }

            }

        } else {
            echo SimpleXLS::parseError();
        }

        /*if ( $xlsx = \SimpleXLSX::parse('station/4907M.pdf.xlsx') ) {
            // echo '<pre>';
            // print_r( $xlsx->rows() );

            $satirSutunArray = array();
            $dokumanBaslik = array();
            $dokuman = array();
            $careLabel = array();
            $carpolateLabel = array();
            $sutun = 0;
            $basliklarArray = array();
            $bolum1 = array();
            $bolum2 = array();
            $bolum3 = array();
            $bolum4 = array();
            $bolum5 = array();
            $bolum6 = array();
            $bolum7 = array();
            $sabitler = array(
                'Care Label',
                'Corporate Labels and Packaging',
                'Non Corporate Labels and Packaging',
                'Transit Modularity',
                'UPCLabel Information',
                'Colourway Season Status',
            );
            echo '<pre>';
            $aralikSay = 0;
            //$art = 0;
            foreach($xlsx->rows() as $s => $satir){
                $aralikSay++;
                //$dokuman['satir_'.$s] = $satir;
                $sabitDizi = array();
                foreach($satir as $r => $row){

                    $satirSutunArray[$s][] = $row;

                    //echo $s.' = satir ___ '.$r.' = key ___ '.'row = '.$row.'<br>';

                    if (in_array($row, $sabitler)){
                        echo 'test_'.$aralikSay.'<br>';
                        $basliklarArray[$aralikSay] = $row;
                        $aralikSay = 0;
                    }


                    // if ($s <= 7){
                    //     //echo $s.' = satir ___ '.$r.' = key ___ '.'row = '.$row.'<br>';
                    //     $dokumanBaslik['dokumanBaslik']['satir_'.$s][] = $row;
                    // }
                    // if ( in_array($row, $sabitler)){
                    //     echo $r.' = key ___ '.'row = '.$row.'<br>';
                    // }
                    // if ($r == 0 && $row == "Care Label"){
                    //     echo 'Care Label Verileri Başlıyor<br>';
                    // }

                    // if ($r == 0 && $row == "Corporate Labels and Packaging"){
                    //     echo 'Corporate Labels and Packaging Verileri Başlıyor<br>';
                    // }

                    // if ($r == 0 && $row == "Non Corporate Labels and Packaging"){
                    //     echo 'Non Corporate Labels and Packaging Verileri Başlıyor<br>';
                    // }

                    // if ($r == 0 && $row == "Transit Modularity"){
                    //     echo 'Transit Modularity Verileri Başlıyor<br>';
                    // }

                    // if ($r == 0 && $row == "UPCLabel Information"){
                    //     echo 'UPCLabel Information Verileri Başlıyor<br>';
                    // }

                    // if ($r == 0 && $row == "Colourway Season Status"){
                    //     echo "Colourway Season Status Verileri Başlıyor<br>";
                    // }

                }

            }

            $sonIndis = 0;
            foreach($basliklarArray as $bk => $bv){
                if ($bv == 'Care Label'){
                    $bolum1 = array_slice($satirSutunArray, $sonIndis, $bk-1);
                    $sonIndis = $sonIndis + $bk;
                    echo $sonIndis;
                }
                if ($bv == 'Corporate Labels and Packaging'){
                    $bolum2 = array_slice($satirSutunArray, $sonIndis, $bk-1);
                    $sonIndis = $sonIndis + $bk;
                    echo $sonIndis.'-'.$bk;
                }
                if ($bv == 'Non Corporate Labels and Packaging'){
                    $bolum3 = array_slice($satirSutunArray, $sonIndis, $bk-1);
                    $sonIndis = $sonIndis + $bk;
                    echo $sonIndis.'-dsf'.$bk;
                   // exit();
                }
                if ($bv == 'Transit Modularity'){
                    $bolum4 = array_slice($satirSutunArray, $sonIndis, $bk-1);
                    $sonIndis = $sonIndis + $bk;
                    echo $sonIndis.'-sad'.$bk;
                }
                if ($bv == 'UPCLabel Information'){
                    $bolum5 = array_slice($satirSutunArray, $sonIndis, $bk-1);
                    $sonIndis = $sonIndis + $bk;
                    echo $sonIndis.'-'.$bk;
                    //exit();
                }
                if ($bv == 'Colourway Season Status'){
                    echo $sonIndis.'-'.$bk;
                    //exit();
                    $bolum6 = array_slice($satirSutunArray, $sonIndis, $bk-1);
                    $sonIndis = $sonIndis + $bk;
                    echo $sonIndis.'-'.$bk;
                }
                echo $bk.' -- '.$bv.'<br>';
            }

            // $baslikDeger = null;
            // foreach ($satirSutunArray as $satirSutun){
            //     foreach($satirSutun as $k => $value){
            //         foreach($value as $v => $str){
            //             if ($v == 0 && $str == "Care Label"){
            //                 $baslikDeger = $k;
            //             }
            //             //array_push($dokumanBaslik, )
            //             if($baslikDeger > 0 && $baslikDeger = $k){

            //                 echo 'burda';
            //                 $baslikDeger = null;

            //             }
            //         }
            //         echo $baslikDeger.'_'.$k.'<br>';



            //         // if ($v == 5 && $str == "Style File Report" ){

            //         // }
            //     }
            //     //echo $baslikDeger;
            // }

            echo '<pre>';
            // print_r(array_unique($satirSutunArray, SORT_REGULAR));
            //print_r($satirSutunArray);
            //print_r($bolum1);
            //print_r($bolum2);
            //print_r($bolum3);
            //print_r($bolum4);
            //print_r($bolum5);
            //print_r($bolum6);
            //print_r($careLabel);
            exit();

        } else {
            echo \SimpleXLSX::parse_error();
        }*/

    }

    public function bol(){

        $file = "station/test.xlsx";

        // $test = date_default_timezone_get();
        // date_default_timezone_set($test);
        // // date_default_timezone_set("Asia/Kolkata");
        // date_default_timezone_set("Europe/Madrid");
        // echo date('d-m-y h:i:s');
        // exit();
        // echo $file;
        // date_default_timezone_set('Europe/Madrid');
        // $date=date("H:i:s");
        // echo $date;
        // exit();

        if ( $xlsx = \SimpleXLSX::parse($file) ) {

            echo '<pre>';
            // print_r( $xlsx->sheetNames() );
            // print_r( $xlsx->sheetName( $xlsx->activeSheet ) );

            $sekmeler[] = $xlsx->sheetNames();
            $okunacakSekmeler = array();
            $aralikDeger = 10;
            $aralikDeger2 = 4;
            $flag = false;
            //$yazilacakBilgiler = array();
            //$test = "";
            //Sekmeler Birlestiriliyor
            foreach($sekmeler as $sk => $sekme){
                //print_r($sekme);
                foreach($sekme as $s => $sekmeValue){
                    $sekmeBirlestir[$sekmeValue] = $xlsx->rows($s);
                }

            }

            // print_r($sekmeBirlestir);
            // exit();
            //Birleştirilen Sekmelerden Sabit Değerler İlk Sayfadan Manuel Alınıyor.
            foreach ($sekmeBirlestir as $sabitBKey => $sabitBValue){
                if ($sabitBKey == 'page-2-table-1'){
                    //echo count($sabitBValue).'<br>';
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
                    // echo $satirKey.'__<br>';
                    foreach ($satirValue as $sutunKey => $sutunValue){

                        // if ($sutunValue === 'UPCLabel Information'){
                        //     $okunacakSekmeler[] = $sBKey;
                        //     //$okunacakSekmeler[$sBKey][] = $sekmeBirlestir[$sBKey];
                        // }
                        //echo $satirKey.'_key_value_'.$sutunValue.'<br>';
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
                            //$okunacakSekmeler = [];
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
                //    if (in_array('UPCLabel Information', $v)){
                //       //echo $okunacakSekmeler[$sBKey].'<br>';
                //       $okunacakSekmeler['UPCLabel Information'][$sBKey][$k] = $sBValue[$k];
                //       //print_r($v);
                //    }
                }

            }


            echo $aralikDeger.'_aralik_deger<br>';
            //print_r($okunacakSekmeler);
            // exit();

            $aralikSay = 0;
            $tamizlikAralikSay = 0;
            $yazilacakBilgiler = array();
            $belgeBilgi = array();

            echo 'supplierNo : '. $supplierNo.'<br>';
            echo 'strokeNumber : '.$strokeNumber.'<br>';
            echo 'seasonId : '.$seasonId.'<br>';
            //exit();
            //$seasonId = $okunacakSekmeler['page-2-table-2'][0][1][2];
            //Alınan Veriler Koşula Göre Ayarlanıp DB ye kayıt edilecek.
            foreach($okunacakSekmeler as $sekmeKey => $sekmeValue){

                //echo $sekmeKey.'<br>';
                foreach($sekmeValue as $svKey => $svValue){

                    //Size bilgisi Baslik ile birlesik gelirse ayır
                    //price bilgisi upc bilgisi yerine gelirse yer degistir
                    //farklı bir durum cikarsa yeni kosul belirt
                    foreach($svValue as $svBaslikKey => $svBaslikValue){

                        $baslikUzunluk = count($svBaslikValue);
                        //echo $baslikUzunluk.'<br>';
                        //print_r($svBaslikValue);
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

                            //echo 'baslik 3 bos <br>';
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

                            //echo $svBaslikValue[2].'<br>';
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

                    // print_r($svValue);
                    //baslik bilgilerinde ikinci satirin birleştirildiği yer
                    foreach($svValue as $svBaslikKey => $svBaslikValue){

                        // ikinci satirları birlestir
                        $svBaslikUzunluk = count ($svBaslikValue);
                        //echo $svBaslikUzunluk.'<br>';
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

                    //print_r($svValue);

                    //birlestirilen ikinci satir bilgilerde gereksiz verilerin silindiği yer
                    foreach($svValue as $svTemizleKey => $svTemizleValue){

                        //birlestirilen ikinci satir siliniyor
                        $svTemizleUzunluk = count($svTemizleValue);
                        //echo $svTemizleUzunluk.'<br>';
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

                    // print_r($svValue);
                    // baslik bilgilerinde ucuncu satirin birlesitirldigi yer
                    foreach($svValue as $svBaslikKey2 => $svBaslikValue2){

                        // ucuncu satirları birlestir
                        $svBaslikUzunluk = count ($svBaslikValue);
                        //echo $svBaslikUzunluk.'<br>';
                        if (
                            $svBaslikUzunluk != 8 &&
                            $svBaslikValue2[3] == "" &&
                            isset($svBaslikValue2[4]) && $svBaslikValue2[4] == "" &&
                            isset($svBaslikValue2[5]) && $svBaslikValue2[5] == "" &&
                            isset($svBaslikValue2[6]) ? $svBaslikValue2[6] == "" : ''
                        ){
                            $baslikSatir1 = $svBaslikValue2[2];
                            $baslikSatir2 = $svBaslikValue2[1];
                            //echo $baslikSatir1.'1 - 2'.$baslikSatir2.'<br>';
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

                    // print_r($svValue);

                    //birlestirilen ucuncu satir bilgilerinde gereksiz verilerin silindigi yer
                    foreach($svValue as $svTemizleKey2 => $svTemizleValue2){

                        //birlestirilen ucuncu
                        if (
                            $svTemizleValue2[3] == "" &&
                            isset($svTemizleValue2[4]) && $svTemizleValue2[4] == "" &&
                            isset($svTemizleValue[5]) && $svTemizleValue2[5] == "" &&
                            isset($svTemizleValue2[6]) ? $svTemizleValue2[6] == "" : ''
                        ){
                            unset($svValue[$svTemizleKey2]);
                        }

                    }

                    //print_r($svValue);
                    //birlestirilen bilgilerde silinen gereksiz bilgilerden sonra upc numarası olmayan verilerin silindiği yer
                    if ($svKey == 0){
                        $tamizlikAralikSay = 0;
                    }

                    foreach($svValue as $svUpcTemizleKey => $svUpcTemizleValue){

                        $tamizlikAralikSay++;
                        $valueSay = count($svUpcTemizleValue);
                        //echo $valueSay.'<br>';
                        // echo $tamizlikAralikSay.'_temizlik<br>';
                        //echo $aralikDeger.'_aralikDeger<br>';
                        // echo $svUpcTemizleKey.'_key<br>';
                        // ayrım için gerekli baslik bilgileri korunuyor sonrasında gereksizler siliniyor.
                        // ilk sayfa için ayrı diger sayfalar icin ayrı işlem gerçekleştiriliyor.
                        if ($sekmeKey == 'page-2-table-2' && $tamizlikAralikSay >= $aralikDeger){
                            //echo $valueSay.'_melisa__<br>';

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

                        if ($sekmeKey != 'page-2-table-2' && $tamizlikAralikSay >= $aralikDeger2){

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

                    // echo '<pre>';
                    // print_r($svValue);
                    // upc numarası olan verilerin db ye kayıt islemlerinin yapıldıgı yer
                    //echo $aralikDeger.'_aralik_deger<br>';
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
                            $qtyReq = "";
                            $price = $satirValue[2] != "" ? $satirValue[6] : $satirValue[7];
                            if ($primarySize != "" && $secondarySize != ""){
                                $sdsCode = $primarySize.'/'.$secondarySize;
                            }elseif ($primarySize != "" && $secondarySize == ""){
                                $sdsCode = $primarySize;
                            }elseif ($primarySize == "" && $secondarySize != ""){
                                $sdsCode = $secondarySize;
                            }
                            // echo $colorName.'_ilksayfadan<br>';
                            // echo $primarySize.'_primarySize_ilksayfadan<br>';
                            // echo $secondarySize.'_secondarySize_ilksayfadan<br>';
                            // echo $upcNumber.'_upcNumber_ilksayfadan<br>';
                            // echo $price.'_price_ilksayfadan<br>';
                            // echo $sdsCode.'_sdscode_ilksayfalardan<br>';

                            $data = [
                                'consignment_id' => 1,
                                'order' => '1515',
                                'season' => $seasonId,
                                'description' => $colorName,
                                'sds_code' => $sdsCode,
                                'upc' => $upcNumber,
                                'price' => $price,
                                'story_desc' => $storyDesc,
                                'qty_req' => $qtyReq,
                                'user_id' => 1,
                            ];

                            print_r($data);

                            // foreach($satirValue as $sutunKey => $sutunValue){

                            //     if ($sutunKey > 0 ){
                            //         //echo $sutunKey.'__key__value__'.$sutunValue.'<br>';
                            //     }

                            // }

                            //$aralikSay = 0;

                        }
                        //echo $cols.'<br>';
                        //echo $aralikSay.'___Aralik<br>';

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
                            // echo $colorName.'_digersayfalardan<br>';
                            // echo $primarySize.'_primarySize_digersayfalardan<br>';
                            //echo $secondarySize.'_secondarySize_digersayfalardan<br>';
                            //echo $upcNumber.'_upcNumber_digersayfalardan<br>';
                            //echo $price.'_price_digersayfalardan<br>';
                            //echo $sdsCode.'_sdscode_digersayfalardan<br>';

                            $data = [
                                'consignment_id' => 1,
                                'order' => '1515',
                                'season' => $seasonId,
                                'description' => $colorName,
                                'sds_code' => $sdsCode,
                                'upc' => $upcNumber,
                                'story_desc' => $storyDesc,
                                'qty_req' => $qtyReq,
                                'user_id' => 1,
                            ];

                            // echo '<pre>';
                            print_r($data);
                            // foreach($satirValue as $sutunKey => $sutunValue){

                            //     if ($sutunKey > 0 ){

                            //         // farklı bir tablo varsa bitir
                            //         if ($sutunValue == "Colourway Season Status"){
                            //             exit();
                            //         }

                            //     }

                            // }

                            //echo $aralikSay.'_______<br>';
                            //$aralikSay = 0;

                        }

                    }

                    // print_r($svValue);
                    //print_r($svValue);
                    //print_r($yazilacakBilgiler);
                }

            }


        } else {
            echo 'error';
        }


        // silinmesin
        // $pdfName = "4907M.pdf";
        // $pdfFile = 'station/4907M.pdf';
        // $command = escapeshellcmd("test.py $pdfName $pdfFile");
        // $output = shell_exec($command);
        // echo $output;

        //$file = 'station/4907M.pdf';
        // include('class.pdf2text.php');
        // $a = new PDF2Text();
        // $a->setFilename('station/4907M.pdf');
        // $a->decodePDF();
        // echo $a->output();

        // $parser = new \Smalot\PdfParser\Parser();
        // $pdf    = $parser->parseFile($file);

        // $text = $pdf->getText();
        // //echo '<pre>';
        // echo $text;
        // $text2 = array();
        // $text = $pdf->getText();
        // $text2[] = nl2br($text);
        // if (in_array('UPCLabel Information', $text2)){
        //     echo 'var kardesim';
        // }else{
        //     echo 'yok';
        // }
        // echo '<pre>';
        // print_r($text);
        //$pdfText= nl2br($text);
        //echo $pdfText;

        // Parse pdf file and build necessary objects.
        // $parser = new \Smalot\PdfParser\Parser();
        // $pdf    = $parser->parseFile('station/4907M.pdf');

        // // Retrieve all pages from the pdf file.
        // $pages  = $pdf->getPages();

        // // Loop over each page to extract text.
        // foreach ($pages as $page) {
        //     echo $page->getText();
        // }

        // Parse pdf file and build necessary objects.
        // $parser = new \Smalot\PdfParser\Parser();
        // $pdf    = $parser->parseFile('station/4907M.pdf');

        // // Retrieve all details from the pdf file.
        // $details  = $pdf->getDetails();
        // print_r($details);
        // // Loop over each property to extract values (string or array).
        // foreach ($details as $property => $value) {
        //     if (is_array($value)) {
        //         $value = implode(', ', $value);
        //     }
        //     echo $property . ' => ' . $value . "\n";
        // }

        // $config = new \Smalot\PdfParser\Config();
        // $config->setFontSpaceLimit(-60);
        // $config->setRetainImageContent(false);
        // $config->setHorizontalOffset('');

        // $parser = new \Smalot\PdfParser\Parser([], $config);
        // $pdf    = $parser->parseFile('station/4907M.pdf');

        // $text = $pdf->getText();

        // echo $text;

    }

    public function gonder(){

        $file = "station/hb.xlsx";

        $epc = '3035EBBE4854A90000002717';
        $length = strlen($epc);
        $binaryEpc = "";
        for ($i=0; $i<$length; $i++) {
            $binaryEpc .= substr("0000".decbin(hexdec($epc[$i])), -4);
        }


        $companyBinary = substr($binaryEpc, 15, 23);
        $itemBinary = substr($binaryEpc, 41, 17);

        $company = bindec($companyBinary);
        $item = bindec($itemBinary);
        //$item = '0'.$item;
        // 01111010111011111001
        // 01111010111011111001
        // 0866928056722866921
        // 8056722866921
        echo $binaryEpc.'<br>';
        echo $companyBinary.'<br>';
        echo $itemBinary.'<br>';
        echo $company.'<br>';
        echo '0'.$item;

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

        echo $gtin;

        // 08056722866495
        // 008664980567220866490
        //8056722

        // 00110000001101011110101110111110010010000 10101001001111001 00000000000000000000000010011100010111
        //10101001001111001
        // 01111010111011111001
        // 01111010111011111001
        // 11110101110111110010010
        // 010100000011010101000101
        // 503545
        exit();

        if($gtin[0] == '0'){
            //echo 'sad';
            $gtin = substr($gtin, 1, strlen($gtin));
        }

        echo $gtin;

        exit();

        if ( $xlsx = \SimpleXLSX::parse($file) ) {

            echo '<pre>';
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
                    echo $sku.'_<br>';
                    echo $name.'_<br>';
                    echo $part.'_<br>';
                    echo $referans.'_<br>';
                    echo $season.'_<br>';
                    echo $size.'_<br>';
                    echo $color.'_<br>';
                    echo $count.'_<br>';

                }
                // foreach($sValue as $sutunKey => $sutunValue){

                //     echo $sutunKey.'_key_value_'.$sutunValue.'<br>';

                // }

            }

        }
        // $pdf = new PdfToText ('station/4907M.pdf');
        // echo $pdf->Text;

        // $address = '127.0.0.1';
        // $port = '8025';

        // $dataPost = array(
        //     "ad" => "sadik",
        //     "soyad" => "bilgin2",
        //     "eposta" => "sadik.bilgin@takipsan.com"
        // );





        // $fp = fsockopen("127.0.0.1", 8025, $errno, $errstr, 30);
        // if (!$fp) {

        //     echo "sad <br />\n";

        // }else{

        //     fwrite($fp, json_encode($dataPost));
        //     while (!feof($fp)) {
        //         echo fgets($fp, 128);
        //     }
        //     fclose($fp);

        // }
    }

    public function csv(){

        $epcArray = array(
            "303ACA47831CCD599C82CCBD",
            "303ACA47831CCD599C82CCBE",
            "303ACA47831CCD599C82CCBF",
            "303ACA47831CCD599C82CCC0",
            "303ACA47831CCD599C82CCC1",
            "303ACA47831CCD599C82CCC2",
            "303ACA47831CCD599C82CCC3",
            "303ACA47831CCD599C82CCC4",
            "303ACA47831CCD599C82CCC5",
            "303ACA47831CCD599C82CCC6",
            "303ACA47831CCD599C82CCC8",
            "303ACA47831CCD599C82CCC9",
            "303ACA47831CCD599C82CCCA",
            "303ACA47831CCD599C82CCCB",
            "303ACA47831CCD599C82CCEE",
            "303ACA47831CCD599C82CD3C",
            "303ACA47831CCD599C82CD3D",
            "303ACA47831CCD599C82CD53",
            "303ACA47831CCD599C82CF64",
            "303ACA47831CCD599C82CF7D",
            "303ACA47831CCD599C82CF7E",
            "303ACA47831CCD599C82CF81",
            "303ACA47831CCD599C82CF8A",
            "303ACA47831CCD599C82CF8B",
            "303ACA47831CCD599C82CF8C",
            "303ACA47831CCD599C82CF8E",
            "303ACA47831CCD599C82D17F",
            "303ACA47831CCD599C82D180",
            "303ACA47831CCD599C82D181",
            "303ACA47831CCD599C82D183",
            "303ACA47831CCD599C82D184",
            "303ACA47831CCD599C82D185",
            "303ACA47831CCD599C82D187",
            "303ACA47831CCD599C82D188",
            "303ACA47831CCD599C82D189",
            "303ACA47831CCD599C82D18A",
            "303ACA47831CCD599C82D18B",
            "303ACA47831CCD599C82D18C",
            "303ACA47831CCD599C82D18D",
            "303ACA47831CCD599C82D191",
            "303ACA47831CCD599C82D192",
            "303ACA47831CCD599C82D193",
            "303ACA47831CCD599C82D194",
            "303ACA47831CCD599C82D195",
            "303ACA47831CCD599C82D196",
            "303ACA47831CCD599C82D197",
            "303ACA47831CCD599C82D198",
            "303ACA47831CCD599C82D199",
            "303ACA47831CCD599C82D19A",
            "303ACA47831CCD599C82D19B",
            "303ACA47831CCD599C82D19D",
            "303ACA47831CCD599C82D19E",
            "303ACA47831CCD599C82D19F",
            "303ACA47831CCD599C82D1A0",
            "303ACA47831CCD599C82D1A1",
            "303ACA47831CCD599C82D1A2",
            "303ACA47831CCD599C82D1A3",
            "303ACA47831CCD599C82D207",
            "303ACA47831CCD599C82D208",
            "303ACA47831CCD599C82D209",
            "303ACA47831CCD599C82D20E",
            "303ACA47831CCD599C82D221",
            "303ACA47831CCD599C82D222",
            "303ACA47831CCD599C82D224",
            "303ACA47831CCD599C82D225",
            "303ACA47831CCD599C82D226",
            "303ACA47831CCD599C82D227",
            "303ACA47831CCD599C82D228",
            "303ACA47831CCD599C82D229",
            "303ACA47831CCD599C82D22A",
        );

        $epcArray2 = array(
            "303ACA47831CCDD99C82CD67",
            "303ACA47831CCDD99C82CED6",
            "303ACA47831CCDD99C82CED2",
            "303ACA47831CCDD99C82CD63",
            "303ACA47831CCDD99C82CD2A",
            "303ACA47831CCDD99C832B36",
            "303ACA47831CCDD99C82CEEA",
            "303ACA47831CCDD99C82CF00",
            "303ACA47831CCDD99C82CEFD",
            "303ACA47831CCDD99C82CED7",
            "303ACA47831CCDD99C82D06D",
            "303ACA47831CCDD99C82CEEB",
            "303ACA47831CCDD99C82CEE8",
            "303ACA47831CCDD99C82EF1E",
            "303ACA47831CCDD99C82CEFE",
            "303ACA47831CCDD99C82EF1C",
            "303ACA47831CCDD99C82CEFC",
            "303ACA47831CCDD99C82EF7D",
            "303ACA47831CCDD99C82EF7B",
            "303ACA47831CCDD99C82CEFA",
            "303ACA47831CCDD99C82CED4",
            "303ACA47831CCDD99C82EF22",
            "303ACA47831CCDD99C82CED0",
            "303ACA47831CCDD99C82EF20",
            "303ACA47831CCDD99C82EF81",
            "303ACA47831CCDD99C82CD65",
            "303ACA47831CCDD99C82CECF",
            "303ACA47831CCDD99C82CD6E",
            "303ACA47831CCDD99C82CD4F",
            "303ACA47831CCDD99C82CD2E",
            "303ACA47831CCDD99C82CD2C",
            "303ACA47831CCDD99C82CEE9",
            "303ACA47831CCDD99C82EF79",
            "303ACA47831CCDD99C82EF54",
            "303ACA47831CCDD99C82CD51",
            "303ACA47831CCDD99C82EF5F",
            "303ACA47831CCDD99C82CEFF",
            "303ACA47831CCDD99C82EF7E",
            "303ACA47831CCDD99C82EF5D",
            "303ACA47831CCDD99C82CEFB",
            "303ACA47831CCDD99C82EF7A",
            "303ACA47831CCDD99C82CD66",
            "303ACA47831CCDD99C82CED3",
            "303ACA47831CCDD99C82CED5",
            "303ACA47831CCDD99C82CD62",
            "303ACA47831CCDD99C82CD64",
            "303ACA47831CCDD99C82CD4E",
            "303ACA47831CCDD99C82CD2B",
            "303ACA47831CCDD99C82EF78",
            "303ACA47831CCDD99C82EF76",
            "303ACA47831CCDD99C82CF01",
            "303ACA47831CCDD99C82CD50",
            "303ACA47831CCDD99C82CD52",
            "303ACA47831CCDD99C82EF5E",
            "303ACA47831CCDD99C82EF7F",
            "303ACA47831CCDD99C82EF1D",
            "303ACA47831CCDD99C82CD2F",
            "303ACA47831CCDD99C82CD2D",
            "303ACA47831CCDD99C82CD53",
            "303ACA47831CCDD99C82EF7C",


        );

        foreach ($epcArray2 as $value){

            $test = GetGTINFromEPC($value);
            echo $test.'<br>';

        }
        exit();

    }

    public function xml(){


        ini_set('memory_limit','123999M');

        $ftp = Storage::disk('custom-ftp');
        $files = Storage::disk('custom-ftp')->files('_files');
        echo 'Dosya Sayısı : '.count($files).'<br>';
        //file yolu filesystems.php iceriginden geliyo /files/dosya seklinde tanımlı
        $fixedPath = "/home/admin/domains/takipsanplus.com/public_html/msfilerepo/";
        //$ftp->makeDirectory('test');
        //$ftp->getDriver()->getAdapter()->disconnect();
        // echo '<pre>';
        // print_r($dosyalar);
        // exit();
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

                //dosya uzantılarını kontrol ettir.
                //zip dosyalarını dısarı cıkart dosya isimlerini degistir.
				// echo '<pre>';
				// print_r ($pathinfo);
				// echo $pathinfo['dirname'].'<br>';

                if ($extension == 'zip'){

                    $zip = new ZipArchive();
                    //$zip->open('/home/admin/domains/takipsanplus.com/public_html/msfilerepo/'.$dosya, ZipArchive::CREATE | ZipArchive::OVERWRITE);
                    $zipFile = $zip->open($fixedPath.$file);
					//echo '<pre>';
					//var_dump($zip);
					//print_r($zip);
					//echo "dosya sayısı: " . $zip->numFiles . "<br>";
					//for ($i = 0; $i < $zip->numFiles; $i++) {

						//$name = $zip->getNameIndex($i);
						//echo $name.'<br>';

					//}
                    //echo $dosya.'_dosya<br>';
                    if ($zipFile === true){

						// for ($i = 0; $i < $zip->numFiles; $i++) {

						// 	$name = $zip->getNameIndex($i);
						// 	echo $name.'<br>';

						// }

                        $extract = $zip->extractTo($fixedPath.'_files/');
                        //$test = $zip->extractTo($pathinfo['dirname'].'/test/');
                        if ($extract){

                            //rename($pathinfo['basename'], $fixedPath.'_files/extracteds/ex_'.$pathinfo['basename']);
                            rename($fixedPath.'_files/'.$pathinfo['basename'], $fixedPath.'_files/extracteds/'.date('d.m.y').'_'.$pathinfo['basename']);
                            echo $extract.'ok';

                        }else{

                            echo 'nok';

                        }
                        $zip->close();


                    }else{
						echo 'failed';
                    }


                    //echo $zipper;
                    // $res = $zip->open($filePath, ZipArchive::CREATE);
                    // echo $res;
                    // $filePath = Storage::disk('custom-ftp')->get($dosya);
                    // $test = $zip->addFromString($filePath, $dosya);
                    // echo $test.'<br>';
                    // echo $res;
                    // if ($res === 5) {
                    //     $zip->extractTo('files/');
                    //     $zip->close();
                    //     echo 'ok';
                    // } else {
                    //     echo 'failed';
                    // }

                }
                // xml dosyalarını oku
                if ($extension == 'xml'){

                    $xmlFileRead = simplexml_load_string($filePull);
                    if ($xmlFileRead){

                        foreach($xmlFileRead as $xmlFileKey => $xmlFileContent){

                            //echo $file.'_________<br>';
                            $country = $xmlFileContent->Country;
                            $dep = $xmlFileContent->Dept;
                            $poNumber = $xmlFileContent->PONumber;
                            $stroke = $xmlFileContent->Stroke;
                            $incotermDate = $xmlFileContent->IncotermDate;
                            $packType = $xmlFileContent->PackType;
                            $supplierDesc= $xmlFileContent->SupplierDescription;
                            $ratioPackIndicator= $xmlFileContent->RatioPackIndicator;

                            $xmlFixArray[] =  [
                                'poNumber' => strval($poNumber),
                                'stroke' => strval($stroke)
                            ];
                            //echo $CartonID= $dosyaIcerik->Cartons->Carton->CartonID.'<br>';
                            foreach($xmlFileContent->Cartons->Carton as $carton){

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

                                    //db kayit işlemleri burada gerceklesecek

                                }

                            }


                        }

                    }

                }
                // txt dosyalarını oku
                /*
                if ($extension == 'txt'){
                    // if ($fh = fopen($fixedPath.$file, "r")) {
                    //     $left='';
                    //     while (!feof($fh)) {// read the file
                    //         $temp = fread($fh);
                    //         $fgetslines = explode("\n",$temp);
                    //         $fgetslines[0]=$left.$fgetslines[0];
                    //         if(!feof($fh) )$left = array_pop($lines);
                    //         foreach ($fgetslines as $k => $line) {
                    //             //do smth with $line
                    //         }
                    //     }
                    // }
                    // fclose($fh);
                    //$txtFileRead = fopen($fixedPath.$file, "rb");
                    $txtFileRead = file_get_contents($fixedPath.$file, false, null);
                    $txtContents = explode("\n", $txtFileRead);
                    $txtContentsArr = array();
                    $i = 0;
                    // $testPo = "2012113843";
                    // $testStroke = "3332";
                    // $testPo = "2012107117";
                    // $testStroke = "9056G";

                    $testPo = "2012107116";
                    $testStroke = "9026";
                    // $testColorDesc = "ZZ MULTI";
                    // $testColorDescEx = explode(" ", $testColorDesc);
                    // $tColorDesc = $testColorDescEx[1];
                    // $testSupplierDescription = "GULER DIS TIC VE TEKS PAZ LTD";
                    // $tSDEx = explode(" ", $testSupplierDescription);
                    // $tSupplierDescription = end($tSDEx);

                    // echo '<pre>';
                    // print_r($xmlFixArray);

                    foreach($txtContents as $k => $txtContent){

                        if (strlen($txtContent) > 300){
                            //$i = 0;
                            $txtContentsArr[] = $txtContent;
                            //echo $txtContent.'_'.strlen($txtContent).'_'.$k.'_sad<br>';
                        }

                    }
                    // echo '<pre>';
                    // print_r($txtContentsArr);

                    if (isset($txtContentsArr)){

                        $sortRest = array();
                        $sortRestAsc = array();
                        foreach($txtContentsArr as $key => $txtContentValue){

                            //echo $txtContentValue.'<br>';
                            $p1 = explode(" ", $txtContentValue);
                            $poNo = substr($p1[0], (-strlen($testPo) -3), 10);
                            $stroke = substr($p1[6], 0, strlen($testStroke));
                            //echo $p1[0].'-'.$poNo.'<br>';
                            //echo $stroke.'sad_<br>';
                            //echo $poNo.'<br>';
                            // echo '<pre>';
                            // print_r($p1);
                            if ($poNo == $testPo && $stroke == $testStroke){

                                // echo 'ftmsdk';
                                // echo '<pre>';
                                //$sortRest = array_filter($p1);
                                $sortRest[] = $txtContentValue;
                                //echo $key.'<br>'.$txtContentValue.'<br>';
                                //$test2 = array_reverse($test);
                                //print_r($sortRest);
                                //echo preg_replace('/\s+/', ' ', $txtContentValue);
                                //echo str_replace(" ", "-", $txtContentValue).'<br>';
                            //     echo $p1[6].'_2<br>';

                            }

                        }

                        // echo '<pre>';
                        // print_r($sortRest);

                        if (isset($sortRest) && !empty($sortRest) > 0){

                            foreach ($sortRest as $sValue){

                                echo $sValue.'<br>';
                                // echo $recordType = substr($sValue, 0, 2).'<br>';
                                // echo $buid = substr($sValue, 1, 3).'<br>';
                                // echo $poNumber = substr($sValue, 4,10).'<br>';
                                // echo $poVersionNumber = substr($sValue, 14, 2).'<br>';
                                // //O – Original, A – amended, C –cancelled.
                                // echo $poStatusType = substr($sValue, 16, 1).'<br>';
                                // echo $dateLastAmended = substr($sValue, 22, 8).'<br>';
                                // echo $departmentCode = substr($sValue, 30, 4).'<br>';
                                // echo $strokeNumber = substr($sValue, 34, 5).'<br>';
                                // echo $colourCode = substr($sValue, 39, 3).'<br>';
                                // echo $departmentDesc = substr($sValue, 42, 30).'<br>';
                                // echo $strokeDesc = substr($sValue, 72, 30).'<br>';
                                // echo $colourDesc = substr($sValue, 102, 16).'<br>';
                                // echo $story = substr($sValue, 118, 30).'_<br>';
                                // echo $commodityCode = substr($sValue, 148, 15).'<br>';
                                // echo $season = substr($sValue, 163, 4).'<br>';
                                // echo $phase = substr($sValue, 167, 2).'<br>';
                                // echo $deliveryDate = substr($sValue, 169, 8).'<br>';
                                // echo $cargoReadyDate = substr($sValue, 177, 8).'<br>';
                                // echo $franchiseOrder = substr($sValue, 185, 1).'<br>';
                                // echo $manufacturerCode = substr($sValue, 186, 5).'<br>';
                                // echo $manufacturerDesc = substr($sValue, 191, 30).'<br>';
                                // echo $factoryCode = substr($sValue, 221, 10).'<br>';
                                // echo $factoryDescription = substr($sValue, 231, 30).'<br>';
                                // echo $freightManagerDesc = substr($sValue, 261, 30).'<br>';
                                // echo $paymentMethod = substr($sValue, 291, 30).'<br>';
                                // echo $paymentTerms = substr($sValue, 321, 30).'<br>';
                                // echo $incotermType = substr($sValue, 351, 30).'<br>';
                                // echo $contractNo = substr($sValue, 381, 8).'<br>';
                                // echo $countryId = substr($sValue, 389, 3).'<br>';
                                // echo $countryDescription = substr($sValue, 392, 30).'<br>';
                                // echo $region = substr($sValue, 422, 2).'<br>';
                                // echo $portLoadingCode = substr($sValue, 424, 5).'<br>';
                                // echo $freightId = substr($sValue, 429, 4).'<br>';
                                // echo $freightDesc = substr($sValue, 433, 20).'<br>';
                                // echo $paymentCurrency = substr($sValue, 453, 30).'<br>';
                                // echo $shipmentMethod = substr($sValue, 483, 30).'<br>';
                                // //B = Boxed,H = Hanging,C = Converted,D = Boxed to Tote,P = Converted when picked for despatch
                                // echo $packType = substr($sValue, 513, 1).'<br>';
                                // echo $reprocessorIndicator = substr($sValue, 514, 10).'<br>';
                                // echo $orderNotes = substr($sValue, 524, 100).'<br>';
                                // echo $finalWarehouseId = substr($sValue, 624, 4).'<br>';
                                // echo $finalWarehouseDesc = substr($sValue, 628, 20).'<br>';
                                // echo $destination = substr($sValue, 648, 25).'<br>';


                            }

                        }

                        // if (isset($sortRest) && !empty($sortRest) > 0){

                        //     $colorKey = null;
                        //     $description = null;
                        //     $sDescKey = null;
                        //     // id sıralamaları yeniden düzenleniyor.
                        //     foreach ($sortRest as $sValue){
                        //         $sortRestAsc[] = $sValue;
                        //     }

                        //     // color bilgisinin olduğu key bilgisi alınıyor.
                        //     foreach($sortRestAsc as $sKey => $sValue){

                        //         if ($sValue == $tColorDesc){
                        //             $colorKey = $sKey;
                        //         }

                        //         if ($sValue == $tSupplierDescription){
                        //             $sDescKey = $sKey;
                        //         }

                        //     }

                        //     // echo $sDescKey.'<br>';

                        //     foreach($sortRestAsc as $key => $value){

                        //         if ($key > 2 && $key < $colorKey){

                        //             echo $value.'_';
                        //             $description = $description.' '.$value;
                        //             $description = ltrim($description);

                        //         }

                        //         // if (){

                        //         // }

                        //     }
                        //     echo '<pre>';
                        //     print_r($sortRestAsc);
                        //     // echo $tColorDesc.'<br>';
                        //     // echo $description.'<br>';


                        // }
                    }


                    // if ($txtFileRead){

                    //     while(!feof($txtFileRead)) {
                    //         echo fgets($txtFileRead). "_sad<br>";
                    //     }
                    //     echo '________________________<br>';

                    // }

                    //fclose($txtFileRead);
                    //$txtFileRead = Storage::disk('custom-ftp')->get($file);
                    // echo '<pre>';
                    // print_r($txtFileRead);
                    //echo '<pre>';
                    //print_r($filePull);
                    // foreach(file($txtFileRead) as $txtFile){
                    //     echo $txtFile.'___<br>';
                    // }
                    // fclose($handle);
                    // $handle = fopen($txtFileRead, "r");
                    // if ($handle) {

                    //     while (($line = fgets($handle)) !== false) {

                    //         echo $line.'<br>';

                    //     }

                    //     fclose($handle);

                    // }
                    //echo $file.'_____<br>';

                }
                */

            }else{

                echo 'Dosya Okunamadı<br>';

            }
            // $dosyaBol = explode("/", $dosya);
            // $dosyaAdi = $dosyaBol[1];
            //echo $dosya.'<br>';
            // xml dosyalarını okuyacak
            //$test = Storage::disk('local')->put($dosya, 'Contents');
            // $xml = simplexml_load_file(Storage::get($dosya));
            // print_r($xml);
            // $xmlString = file_get_contents(storage_path().'/'.$dosya);
            // $xmlObject = simplexml_load_string($xmlString);
            // $json = json_encode($xmlObject);
            // $array = json_decode($json, true);
            // dd($array);

        }

        foreach($xmlFixArray as $xmlFix){

            $xmlPoNumber = $xmlFix['poNumber'];
            $xmlStroke = $xmlFix['stroke'];

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

                                }elseif (strlen($txtContentValue) >= 50 && strlen($txtContentValue) < 300){

                                    $p1 = explode(" ", $txtContentValue);
                                    //echo $p1[0].'<br>';
                                    $poNo = substr($p1[0], 4, 10);
                                    $orderQuantity = substr($p1[0], 25, 10);
                                    $primarySize = substr($p1[0], 35,5);
                                    $totalQuantity = 0;
                                    //echo $orderQuantity.'-'.$primarySize.'<br>';
                                    if ($poNo == $xmlPoNumber){
                                        echo $txtContentValue.'_diger_satirlar<br>';
                                        //echo $orderQuantity.'_order<br>';
                                        //echo $primarySize.'_primary<br>';
                                        //$sortRestSize[] = $txtContentValue;
                                        $totalQuantity = $totalQuantity + $orderQuantity;
                                        $sortRestSize[] = [
                                            'orderQuantity' =>  $orderQuantity,
                                            'primarySize' => $primarySize,
                                        ];
                                    }
                                    // echo $txtContentValue.'_diger_satirlar<br>';
                                    //po numarası eslesen 300 den kucuk olan diger kayıtlardan karakteri s den sonrası
                                    //10 karakten olandan adet sayısını ve bedeb sayısını al toplam sevkiyat sayısını bul

                                }

                            }

                            $totalQuantity = 0;
                            foreach($sortRestSize as $sRestSize){
                                $totalQuantity = $totalQuantity + $sRestSize['orderQuantity'];
                            }
                            $sortRestSize[] = [
                                'totalQuantity' => $totalQuantity
                            ];
                            echo '<pre>';
                            // print_r($sortRest);
                            print_r($sortRestSize);
                            echo json_encode($sortRestSize);


                            // if (isset($sortRest) && !empty($sortRest) > 0){

                            //     foreach ($sortRest as $sValue){

                            //         //echo $sValue.'<br>';
                            //         // echo $recordType = substr($sValue, 0, 2).'<br>';
                            //         // echo $buid = substr($sValue, 1, 3).'<br>';
                            //         // echo $poNumber = substr($sValue, 4,10).'<br>';
                            //         // echo $poVersionNumber = substr($sValue, 14, 2).'<br>';
                            //         // //O – Original, A – amended, C –cancelled.
                            //         // echo $poStatusType = substr($sValue, 16, 1).'<br>';
                            //         // echo $dateLastAmended = substr($sValue, 22, 8).'<br>';
                            //         // echo $departmentCode = substr($sValue, 30, 4).'<br>';
                            //         // echo $strokeNumber = substr($sValue, 34, 5).'<br>';
                            //         // echo $colourCode = substr($sValue, 39, 3).'<br>';
                            //         // echo $departmentDesc = substr($sValue, 42, 30).'<br>';
                            //         // echo $strokeDesc = substr($sValue, 72, 30).'<br>';
                            //         // echo $colourDesc = substr($sValue, 102, 16).'<br>';
                            //         // echo $story = substr($sValue, 118, 30).'_<br>';
                            //         // echo $commodityCode = substr($sValue, 148, 15).'<br>';
                            //         // echo $season = substr($sValue, 163, 4).'<br>';
                            //         // echo $phase = substr($sValue, 167, 2).'<br>';
                            //         // echo $deliveryDate = substr($sValue, 169, 8).'<br>';
                            //         // echo $cargoReadyDate = substr($sValue, 177, 8).'<br>';
                            //         // echo $franchiseOrder = substr($sValue, 185, 1).'<br>';
                            //         // echo $manufacturerCode = substr($sValue, 186, 5).'<br>';
                            //         // echo $manufacturerDesc = substr($sValue, 191, 30).'<br>';
                            //         // echo $factoryCode = substr($sValue, 221, 10).'<br>';
                            //         // echo $factoryDescription = substr($sValue, 231, 30).'<br>';
                            //         // echo $freightManagerDesc = substr($sValue, 261, 30).'<br>';
                            //         // echo $paymentMethod = substr($sValue, 291, 30).'<br>';
                            //         // echo $paymentTerms = substr($sValue, 321, 30).'<br>';
                            //         // echo $incotermType = substr($sValue, 351, 30).'<br>';
                            //         // echo $contractNo = substr($sValue, 381, 8).'<br>';
                            //         // echo $countryId = substr($sValue, 389, 3).'<br>';
                            //         // echo $countryDescription = substr($sValue, 392, 30).'<br>';
                            //         // echo $region = substr($sValue, 422, 2).'<br>';
                            //         // echo $portLoadingCode = substr($sValue, 424, 5).'<br>';
                            //         // echo $freightId = substr($sValue, 429, 4).'<br>';
                            //         // echo $freightDesc = substr($sValue, 433, 20).'<br>';
                            //         // echo $paymentCurrency = substr($sValue, 453, 30).'<br>';
                            //         // echo $shipmentMethod = substr($sValue, 483, 30).'<br>';
                            //         // //B = Boxed,H = Hanging,C = Converted,D = Boxed to Tote,P = Converted when picked for despatch
                            //         // echo $packType = substr($sValue, 513, 1).'<br>';
                            //         // echo $reprocessorIndicator = substr($sValue, 514, 10).'<br>';
                            //         // echo $orderNotes = substr($sValue, 524, 100).'<br>';
                            //         // echo $finalWarehouseId = substr($sValue, 624, 4).'<br>';
                            //         // echo $finalWarehouseDesc = substr($sValue, 628, 20).'<br>';
                            //         // echo $destination = substr($sValue, 648, 25).'<br>';


                            //     }

                            // }

                        }

                    }

                }else{
                    echo 'Dosya Okunamadı<br>';
                }

            }

        }
        //echo '<pre>';
        //echo 'sad____<br>';
        //print_r($txtContentsArr);

        // foreach($xmlFixArray as $test){
        //     echo $test['poNumber'].'<br>';
        //     echo $test['stroke'].'<br>';
        // }

        $ftp->getDriver()->getAdapter()->disconnect();
        exit();

        // $conn = ftp_connect("msfilerepo.takipsanplus.com");
        // ftp_login($conn, "msfilerepo@takipsanplus.com", "IFjeWJDjf7");
        // $dosyalar = ftp_nlist($conn, "/files");
        // // $içerik çıktısı
        // echo '<pre>';
        // print_r($dosyalar);

        // foreach($dosyalar as $dosya){
        //     $dosyaBol = explode("/", $dosya);
        //     $dosyaAdi = $dosyaBol[2];
        //     if ($dosyaAdi == ".." || $dosyaAdi == "."){
        //         continue;
        //     }else{
        //         echo $dosyaAdi.'<br>';

        //     }
        // }

        // ftp_close($conn);

        //$file = "https://msfilerepo.takipsanplus.com/files/CFSEPLTR323460_test.xml";

        //$directory = "https://msfilerepo.takipsanplus.com/files/";

        // $files = Storage::files($directory);
        // $files = Storage::allFiles($directory);
        // print_r($files);

        //echo 'sad';
        //$files = Storage::disk('ftp')->allFiles();
        // echo $files;
        // exit();

        // Open a known directory, and proceed to read its contents
        // if (is_dir($dir)) {
        //     if ($dh = opendir($dir)) {
        //         while (($file = readdir($dh)) !== false) {
        //             echo "filename: $file:filetype:".filetype($dir.$file)."\n";
        //         }
        //         closedir($dh);
        //     }
        // }else{
        //     echo 'okumadı';
        // }

    }
    //  $pdf = $PDFParser->parseFile("station/4907M.pdf");
    //  $text = $pdf->getText();
    //  echo '<pre>';
    //  print_r (explode(" ",$text));
    //  $exp= explode(" ",$text);

    //     for ($i=0; $i < 15; $i++) {
    //         echo "<br/>";
    //     }
    // echo "<hr/> <br/> Basic information  <br/> <hr/> <br/> " ;
    // echo  "Supplier No : ".$exp[33]."<br/>";
    // echo  "Supplier Name : ".$exp[35].$exp[36].$exp[37]."<br/>";
    // echo  "Country of Manufacture : ".$exp[40]."<br/>";
    // echo  " Style Short Description :".$exp[43].$exp[44].$exp[45].$exp[46]."<br/>";
    // echo  " Product Source Season ID : ".$exp[50]."<br/>";
    // echo  " P&L Approved :".$exp[52]."<br/>";
    // echo  " Stroke Number :".$exp[54]."<br/>";
    // echo  " Lead Factory :".$exp[56]."<br/>";
    // echo  " Department name : ".$exp[58].$exp[59].$exp[60]."<br/>";
    // echo  " Season  : ".$exp[61].$exp[62].$exp[63].$exp[64].$exp[65]."<br/>";
    // echo  " Product Type : ".$exp[66]."<br/>";
    // echo  " P&L Approved Date : ".$exp[69]."<br/>";



    // for ($i=0; $i < 15; $i++) {
    //     echo "<br/>";
    // }
    // print_r (explode("ColourWay",$text));
    // $exp2= explode("ColourWay",$text);
    // echo "<hr/> <br/>  Care Label <br/> <hr/> <br/> " ;
    // echo  "  ".$exp2[2]."<br/>";
    // echo  "  ".$exp2[4]."<br/>";








    //  for ($i=0; $i < 15; $i++) {
    //     echo "<br/>";
    // }

    // echo "<hr/> <br/>  Corporate Labels and Packaging
    // <br/> <hr/> <br/> " ;
    // echo  "  ".$exp2[6]."<br/>";
    // echo  "  ".$exp2[7]."<br/>";
    // echo  "  ".$exp2[8]."<br/>";
    // echo  "  ".$exp2[9]."<br/>";
    // echo  "  ".$exp2[10]."<br/>";
    // echo  "  ".$exp2[11]."<br/>";
    // echo  "  ".$exp2[12]."<br/>";
    // echo  "  ".$exp2[13]."<br/>";
    // echo  "  ".$exp2[14]."<br/>";
    // echo  "  ".substr($exp2[15],0,46)."<br/>";


    // for ($i=0; $i < 15; $i++) {
    //     echo "<br/>";
    // }
    // print_r(explode("T0",$text));
    // $exp3=explode("T0",$text);






/*


İşime yarıyacak array methodları
array_diff()
array_filter()
array_flip() // key value değiştirir

array_key_exists() // verilen anahtarın dizide olup olmadığına bakar
array_search()// keyini döndürür
array_slice() // bellir bir yere kadar döndürür
array_splice() // Bir dizinin belirtilen öğelerini kaldırır ve değiştirir
array_unique() // bir dizide yinelenen değerleri kaldırır
array_values()// bir dizinin tüm değerlerini döndürür

in_array()// dizide varmı diye kontrol eder










function myfunction($value,$key)
{
echo "The key $key has the value $value<br>";
}
$a=array("a"=>"red","b"=>"green","c"=>"blue");
array_walk($a,"myfunction");



$a = "Original";
$my_array = array("a" => "Cat","b" => "Dog", "c" => "Horse");
extract($my_array);
echo "\$a = $a; \$b = $b; \$c = $c";


*/
}


class PDF2Text {
    // Some settings
    var $multibyte = 4; // Use setUnicode(TRUE|FALSE)
    var $convertquotes = ENT_QUOTES; // ENT_COMPAT (double-quotes), ENT_QUOTES (Both), ENT_NOQUOTES (None)
    var $showprogress = true; // TRUE if you have problems with time-out

    // Variables
    var $filename = '';
    var $decodedtext = '';

    function setFilename($filename) {
        // Reset
        $this->decodedtext = '';
        $this->filename = $filename;
    }

    function output($echo = false) {
        if($echo) echo $this->decodedtext;
        else return $this->decodedtext;
    }

    function setUnicode($input) {
        // 4 for unicode. But 2 should work in most cases just fine
        if($input == true) $this->multibyte = 4;
        else $this->multibyte = 2;
    }

    function decodePDF() {
        // Read the data from pdf file
        $infile = @file_get_contents($this->filename, FILE_BINARY);
        if (empty($infile))
            return "";

        // Get all text data.
        $transformations = array();
        $texts = array();

        // Get the list of all objects.
        preg_match_all("#obj[\n|\r](.*)endobj[\n|\r]#ismU", $infile . "endobj\r", $objects);
        $objects = @$objects[1];

        // Select objects with streams.
        for ($i = 0; $i < count($objects); $i++) {
            $currentObject = $objects[$i];

            // Prevent time-out
            @set_time_limit ();
            if($this->showprogress) {
//              echo ". ";
                flush(); ob_flush();
            }

            // Check if an object includes data stream.
            if (preg_match("#stream[\n|\r](.*)endstream[\n|\r]#ismU", $currentObject . "endstream\r", $stream )) {
                $stream = ltrim($stream[1]);
                // Check object parameters and look for text data.
                $options = $this->getObjectOptions($currentObject);

                if (!(empty($options["Length1"]) && empty($options["Type"]) && empty($options["Subtype"])) )
//              if ( $options["Image"] && $options["Subtype"] )
//              if (!(empty($options["Length1"]) &&  empty($options["Subtype"])) )
                    continue;

                // Hack, length doesnt always seem to be correct
                unset($options["Length"]);

                // So, we have text data. Decode it.
                $data = $this->getDecodedStream($stream, $options);

                if (strlen($data)) {
                    if (preg_match_all("#BT[\n|\r](.*)ET[\n|\r]#ismU", $data . "ET\r", $textContainers)) {
                        $textContainers = @$textContainers[1];
                        $this->getDirtyTexts($texts, $textContainers);
                    } else
                        $this->getCharTransformations($transformations, $data);
                }
            }
        }

        // Analyze text blocks taking into account character transformations and return results.
        $this->decodedtext = $this->getTextUsingTransformations($texts, $transformations);
    }


    function decodeAsciiHex($input) {
        $output = "";

        $isOdd = true;
        $isComment = false;

        for($i = 0, $codeHigh = -1; $i < strlen($input) && $input[$i] != '>'; $i++) {
            $c = $input[$i];

            if($isComment) {
                if ($c == '\r' || $c == '\n')
                    $isComment = false;
                continue;
            }

            switch($c) {
                case '\0': case '\t': case '\r': case '\f': case '\n': case ' ': break;
                case '%':
                    $isComment = true;
                break;

                default:
                    $code = hexdec($c);
                    if($code === 0 && $c != '0')
                        return "";

                    if($isOdd)
                        $codeHigh = $code;
                    else
                        $output .= chr($codeHigh * 16 + $code);

                    $isOdd = !$isOdd;
                break;
            }
        }

        if($input[$i] != '>')
            return "";

        if($isOdd)
            $output .= chr($codeHigh * 16);

        return $output;
    }

    function decodeAscii85($input) {
        $output = "";

        $isComment = false;
        $ords = array();

        for($i = 0, $state = 0; $i < strlen($input) && $input[$i] != '~'; $i++) {
            $c = $input[$i];

            if($isComment) {
                if ($c == '\r' || $c == '\n')
                    $isComment = false;
                continue;
            }

            if ($c == '\0' || $c == '\t' || $c == '\r' || $c == '\f' || $c == '\n' || $c == ' ')
                continue;
            if ($c == '%') {
                $isComment = true;
                continue;
            }
            if ($c == 'z' && $state === 0) {
                $output .= str_repeat(chr(0), 4);
                continue;
            }
            if ($c < '!' || $c > 'u')
                return "";

            $code = ord($input[$i]) & 0xff;
            $ords[$state++] = $code - ord('!');

            if ($state == 5) {
                $state = 0;
                for ($sum = 0, $j = 0; $j < 5; $j++)
                    $sum = $sum * 85 + $ords[$j];
                for ($j = 3; $j >= 0; $j--)
                    $output .= chr($sum >> ($j * 8));
            }
        }
        if ($state === 1)
            return "";
        elseif ($state > 1) {
            for ($i = 0, $sum = 0; $i < $state; $i++)
                $sum += ($ords[$i] + ($i == $state - 1)) * pow(85, 4 - $i);
            for ($i = 0; $i < $state - 1; $i++) {
                try {
                    if(false == ($o = chr($sum >> ((3 - $i) * 8)))) {
                        throw new Exception('Error');
                    }
                    $output .= $o;
                } catch (Exception $e) { /*Dont do anything*/ }
            }
        }

        return $output;
    }

    function decodeFlate($data) {
        return @gzuncompress($data);
    }

    function getObjectOptions($object) {
        $options = array();

        if (preg_match("#<<(.*)>>#ismU", $object, $options)) {
            $options = explode("/", $options[1]);
            @array_shift($options);

            $o = array();
            for ($j = 0; $j < @count($options); $j++) {
                $options[$j] = preg_replace("#\s+#", " ", trim($options[$j]));
                if (strpos($options[$j], " ") !== false) {
                    $parts = explode(" ", $options[$j]);
                    $o[$parts[0]] = $parts[1];
                } else
                    $o[$options[$j]] = true;
            }
            $options = $o;
            unset($o);
        }

        return $options;
    }

    function getDecodedStream($stream, $options) {
        $data = "";
        if (empty($options["Filter"]))
            $data = $stream;
        else {
            $length = !empty($options["Length"]) ? $options["Length"] : strlen($stream);
            $_stream = substr($stream, 0, $length);

            foreach ($options as $key => $value) {
                if ($key == "ASCIIHexDecode")
                    $_stream = $this->decodeAsciiHex($_stream);
                elseif ($key == "ASCII85Decode")
                    $_stream = $this->decodeAscii85($_stream);
                elseif ($key == "FlateDecode")
                    $_stream = $this->decodeFlate($_stream);
                elseif ($key == "Crypt") { // TO DO
                }
            }
            $data = $_stream;
        }
        return $data;
    }

    function getDirtyTexts(&$texts, $textContainers) {
        for ($j = 0; $j < count($textContainers); $j++) {
            if (preg_match_all("#\[(.*)\]\s*TJ[\n|\r]#ismU", $textContainers[$j], $parts))
                $texts = array_merge($texts, array(@implode('', $parts[1])));
            elseif (preg_match_all("#T[d|w|m|f]\s*(\(.*\))\s*Tj[\n|\r]#ismU", $textContainers[$j], $parts))
                $texts = array_merge($texts, array(@implode('', $parts[1])));
            elseif (preg_match_all("#T[d|w|m|f]\s*(\[.*\])\s*Tj[\n|\r]#ismU", $textContainers[$j], $parts))
                $texts = array_merge($texts, array(@implode('', $parts[1])));
        }

    }

    function getCharTransformations(&$transformations, $stream) {
        preg_match_all("#([0-9]+)\s+beginbfchar(.*)endbfchar#ismU", $stream, $chars, PREG_SET_ORDER);
        preg_match_all("#([0-9]+)\s+beginbfrange(.*)endbfrange#ismU", $stream, $ranges, PREG_SET_ORDER);

        for ($j = 0; $j < count($chars); $j++) {
            $count = $chars[$j][1];
            $current = explode("\n", trim($chars[$j][2]));
            for ($k = 0; $k < $count && $k < count($current); $k++) {
                if (preg_match("#<([0-9a-f]{2,4})>\s+<([0-9a-f]{4,512})>#is", trim($current[$k]), $map))
                    $transformations[str_pad($map[1], 4, "0")] = $map[2];
            }
        }
        for ($j = 0; $j < count($ranges); $j++) {
            $count = $ranges[$j][1];
            $current = explode("\n", trim($ranges[$j][2]));
            for ($k = 0; $k < $count && $k < count($current); $k++) {
                if (preg_match("#<([0-9a-f]{4})>\s+<([0-9a-f]{4})>\s+<([0-9a-f]{4})>#is", trim($current[$k]), $map)) {
                    $from = hexdec($map[1]);
                    $to = hexdec($map[2]);
                    $_from = hexdec($map[3]);

                    for ($m = $from, $n = 0; $m <= $to; $m++, $n++)
                        $transformations[sprintf("%04X", $m)] = sprintf("%04X", $_from + $n);
                } elseif (preg_match("#<([0-9a-f]{4})>\s+<([0-9a-f]{4})>\s+\[(.*)\]#ismU", trim($current[$k]), $map)) {
                    $from = hexdec($map[1]);
                    $to = hexdec($map[2]);
                    $parts = preg_split("#\s+#", trim($map[3]));

                    for ($m = $from, $n = 0; $m <= $to && $n < count($parts); $m++, $n++)
                        $transformations[sprintf("%04X", $m)] = sprintf("%04X", hexdec($parts[$n]));
                }
            }
        }
    }
    function getTextUsingTransformations($texts, $transformations) {
        $document = "";
        for ($i = 0; $i < count($texts); $i++) {
            $isHex = false;
            $isPlain = false;

            $hex = "";
            $plain = "";
            for ($j = 0; $j < strlen($texts[$i]); $j++) {
                $c = $texts[$i][$j];
                switch($c) {
                    case "<":
                        $hex = "";
                        $isHex = true;
                        $isPlain = false;
                    break;
                    case ">":
                        $hexs = str_split($hex, $this->multibyte); // 2 or 4 (UTF8 or ISO)
                        for ($k = 0; $k < count($hexs); $k++) {

                            $chex = str_pad($hexs[$k], 4, "0"); // Add tailing zero
                            if (isset($transformations[$chex]))
                                $chex = $transformations[$chex];
                            $document .= html_entity_decode("&#x".$chex.";");
                        }
                        $isHex = false;
                    break;
                    case "(":
                        $plain = "";
                        $isPlain = true;
                        $isHex = false;
                    break;
                    case ")":
                        $document .= $plain;
                        $isPlain = false;
                    break;
                    case "\\":
                        $c2 = $texts[$i][$j + 1];
                        if (in_array($c2, array("\\", "(", ")"))) $plain .= $c2;
                        elseif ($c2 == "n") $plain .= '\n';
                        elseif ($c2 == "r") $plain .= '\r';
                        elseif ($c2 == "t") $plain .= '\t';
                        elseif ($c2 == "b") $plain .= '\b';
                        elseif ($c2 == "f") $plain .= '\f';
                        elseif ($c2 >= '0' && $c2 <= '9') {
                            $oct = preg_replace("#[^0-9]#", "", substr($texts[$i], $j + 1, 3));
                            $j += strlen($oct) - 1;
                            $plain .= html_entity_decode("&#".octdec($oct).";", $this->convertquotes);
                        }
                        $j++;
                    break;

                    default:
                        if ($isHex)
                            $hex .= $c;
                        elseif ($isPlain)
                            $plain .= $c;
                    break;
                }
            }
            $document .= "\n";
        }

        return $document;
    }
}
