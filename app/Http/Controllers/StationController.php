<?php

namespace App\Http\Controllers;
//commit
use App\BoxType;
use App\CartoonIntoEpcs;
use App\MsCartonEpcs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Helpers\OptionTrait;
use App\CompanyDb;
use App\CompanyMsDb;
use App\CompanyTargetDb;
use App\CompanyLevisDb;
use App\MsCarton;
use App\MsUpcCarton;
use App\MsCartonEpc;
use App\Helpers\NotificationTrait;
use App\ReadFile;
use App\Licence;
use App\ViewScreen;
use Carbon\Carbon;


//use App\Parser;
use Smalot\PdfParser\Parser;
use Shuchkin\SimpleXlsx\SimpleXLSX;
use Shuchkin\SimpleXLS;
use App\ParserConfig;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

//use Validator;
use Validator, Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Cache\Factory;
use App\XmlFileRepo;
use App\TxtFileRepo;

class StationController extends Controller
{

    use OptionTrait;
    use NotificationTrait;

    public function __construct()
    {

        $this->middleware('station', ['except' => ['loginShow', 'login', 'device', 'deviceCheck']]);

    }

    public function loginShow()
    {
        //Login olmuş ise okuma sayfaına yönlendirelim
        if (auth()->check()) {
            return redirect()->route('station.index');
        }

        return view('station.login', $this->data);
    }

    public function login(Request $request)
    {

        try {

            $attribute = array(
                'email' => 'Username',
                'password' => 'Password'
            );

            $rules = array(
                'email' => 'required|string',
                'password' => 'required'
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            if (Auth::attempt(['username' => $request->get('email'), 'password' => $request->get('password')])) {

                if (auth()->user()->company_id && auth()->user()->company_id != 0) {
                    $company_check = \App\Company::where('status', true)->find(auth()->user()->company_id);
                    if (!$company_check) {
                        session()->flash('flash_message', array(trans('portal.failed'), trans('portal.system_shut_down'), 'error'));
                        auth()->logout();
                        return redirect()->back()->withInput();
                    }
                } else {
                    session()->flash('flash_message', array(trans('station.failed'), trans('station.auth_company_fail'), 'error'));
                    auth()->logout();
                    return redirect()->back()->withInput();
                }


                //Özel izinler atanıyor
                $permissionsIds = auth()->user()->custom_permission->pluck('id')->toArray();
                session()->put('user_custom_permission_ids', $permissionsIds);

                //izinler atanıyor
                $permissions = auth()->user()->permissions;
                session()->put('user_permissions', $permissions);

                //rol bilgileri atanıyor
                $roles = auth()->user()->roles;
                session()->put('user_roles', $roles);

                //cihaz bilgileri atanıyor
                if ($request->has('device_id') && $request->get('device_id') != 0 && $request->get('device_id') != '') {
                    $device = \App\Device::with('readType')->findOrFail($request->get('device_id'));
                    if ($device) {
                        session()->put('device', $device);
                    }
                }

                if ($request->has('remember')) {
                    Cookie::queue('email', $request->get('email'), 120);
                    Cookie::queue('password', $request->get('password'), 120);
                    Cookie::queue('remember', $request->get('remember'), 120);
                } else {
                    Cookie::queue(Cookie::forget('email'));
                    Cookie::queue(Cookie::forget('password'));
                    Cookie::queue(Cookie::forget('remember'));
                }


                return redirect()->route('station.index');
            } else {
                return redirect()->back()->withInput();
            }

        } catch (\Exception $e) {
            return redirect()->back()->withInput();
        }

    }

    public function logout()
    {

        auth()->logout();
        session()->flush();

        return redirect()->route('station.loginShow');
    }

    public function device()
    {
            if (!auth()->user()->company_id || auth()->user()->company_id == 0) {
                session()->flash('flash_message', array(trans('station.failed'), trans('station.auth_company_fail'), 'error'));
                auth()->logout();
                return redirect()->route('station.index');
            }

            $devices = \App\Device::where('company_id', auth()->user()->company_id)->where('status','!=', 2)->get();
            if ($devices) {
                $this->data['devices'] = $devices;
            }

            return view('station.device', $this->data);

    }

    public function deviceCheck(Request $request)
    {

        $attribute = array(
            'device_id' => 'Cihaz',
        );

        $rules = array(
            'device_id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attribute);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        //cihaz bilgileri atanıyor
        $device = \App\Device::with('readType')->findOrFail($request->get('device_id'));
        if ($device) {
            session()->put('device', $device);
        }

        return redirect()->route('station.index');
    }

    /*
    *   Read Ekranları
    *
    */
    public function stationviewAjax(Request $request)
    {

        $defaultGorunum = ViewScreen::find($request->get('gorunumId')) ?? "null";
        //default olarak gorunum ile view listeleniyor. consigment ten geln consignee id ile consignee nin viewid si yakalanıyor
        $selectGorunum = \App\Consignee::where('id', (int)$request->get('consingneeId'))->orderBy('id', 'desc')->get() ?? "null";

        if (isset($defaultGorunum) && count($selectGorunum) == 0) {

            $gorunum = $defaultGorunum->id;

        } else {

            $gorunum = $selectGorunum[0]['viewid'];

        }

        $selectId = (int)$request->get('selectId');

        switch ($gorunum) {
            // Görünüm zara
            case 1:

                $consignments = \App\Consignment::where('status', true)
                    ->where('company_id', auth()->user()->company_id)
                    ->with(['consignee', 'order'])
                    ->orderBy('id', 'desc')
                    ->get();

                if ($consignments) {
                    $this->data['consignments'] = $consignments;
                    $this->data['gorunum'] = $gorunum;
                }
                if ($selectId) {
                    $this->data['selectId'] = $selectId;
                }

                return view('station.read.zara', $this->data);
                break;

            // Görünüm HM
            case 2:

                $consignments = \App\Consignment::where('status', true)
                    ->where('company_id', auth()->user()->company_id)
                    ->with(['consignee', 'order'])
                    ->orderBy('id', 'desc')
                    ->get();

                if ($consignments) {
                    $this->data['consignments'] = $consignments;
                    $this->data['gorunum'] = $gorunum;
                }
                if ($selectId) {
                    $this->data['selectId'] = $selectId;
                }
                return view('station.read.hm', $this->data);
                break;

            // Görünüm MS
            case 3:

                $consignments = \App\Consignment::where('status', true)
                    ->where('company_id', auth()->user()->company_id)
                    ->with(['consignee', 'order'])
                    ->orderBy('id', 'desc')
                    ->get();

                if ($consignments) {
                    $this->data['consignments'] = $consignments;
                    $this->data['gorunum'] = $gorunum;
                }
                if ($selectId) {
                    $this->data['selectId'] = $selectId;
                }
                return view('station.read.ms', $this->data);
                break;

            // Görünüm Decathlon
            case 4:

                $consignments = \App\Consignment::where('status', true)
                    ->where('company_id', auth()->user()->company_id)
                    ->with(['consignee', 'order'])
                    ->orderBy('id', 'desc')
                    ->get();

                if ($consignments) {
                    $this->data['consignments'] = $consignments;
                    $this->data['gorunum'] = $gorunum;
                }
                if ($selectId) {
                    $this->data['selectId'] = $selectId;
                }
                return view('station.read.decathlon', $this->data);
                break;

            default:

                $consignments = \App\Consignment::where('status', true)
                    ->where('company_id', auth()->user()->company_id)
                    ->with(['consignee', 'order'])
                    ->orderBy('id', 'desc')
                    ->get();

                if ($consignments) {
                    $this->data['consignments'] = $consignments;
                    $this->data['gorunum'] = $gorunum;
                }
                if ($selectId) {
                    $this->data['selectId'] = $selectId;
                }
                return view('station.read.zara', $this->data);

                break;

        }

    }

    public function viewSor(Request $request)
    {

        // Gelen consignee ye ait view id den read ekranı alınıyor.
        // Diğer seçeneği seçilirse default olan ekranın ekleme ekranı alınıyor.
        $viewCon = $request->get('consingneeId');
        if ($viewCon != 'other') {

            $consignee = \App\Consignee::find((int)$viewCon);
            $reading = \App\ViewScreen::where('id', $consignee->viewid)->limit(1)->get();
            $readScreen = $reading[0]['reading'];
            $other = false;

        } else {

            $defaultViewSor = \App\ViewScreen::where('status', 'Default')->first();
            if ($defaultViewSor) {

                $readScreen = $defaultViewSor['reading'];
                $other = true;

            }
        }

        return response()->json(['view' => $readScreen, 'other' => $other]);

    }

    // consigment selectbox ı ajaxtan yüklemek için eklendi. daha sonra çalıştırılacak.
    public function selectSor()
    {

        $selectConsignments = \App\Consignment::where('status', true)
            ->where('company_id', auth()->user()->company_id)
            ->with(['consignee', 'order'])
            ->orderBy('id', 'desc')
            ->get();

        if ($selectConsignments) {
            $this->data['consignments'] = $selectConsignments;
        }

        return response()->json($selectConsignments);

    }
    // index_zara
    public function index()
    {

            $consignments = \App\Consignment::where('status', true)
                ->where('company_id', auth()->user()->company_id)
                ->with(['consignee', 'order'])
                ->orderBy('id', 'desc')
                ->get();

            // echo '<pre>';
            // print_r($consignments);
            // exit();

            if ($consignments) {
                $this->data['consignments'] = $consignments;
            }

            // Default olarak eklenen görünüm getiriliyor.
            $defaultView = \App\ViewScreen::where('status', 'Default')->first();

            if ($defaultView) {
                $this->data['gorunum'] = $defaultView->id;
            }

            return view('station.index_zara', $this->data);

    }
    // index_hm
    public function index2()
    {

        $consignments = \App\Consignment::where('status', true)
            ->where('company_id', auth()->user()->company_id)
            ->with(['consignee', 'order'])
            ->orderBy('id', 'desc')
            ->get();

        if ($consignments) {
            $this->data['consignments'] = $consignments;
        }

        return view('station.index_hm', $this->data);

    }
    // index_ms
    public function index3()
    {

        $consignments = \App\Consignment::where('status', true)
            ->where('company_id', auth()->user()->company_id)
            ->with(['consignee', 'order'])
            ->orderBy('id', 'desc')
            ->get();

        if ($consignments) {
            $this->data['consignments'] = $consignments;
        }

        return view('station.index_ms', $this->data);

    }
    // index_decathlon
    public function index4()
    {

        $consignments = \App\Consignment::where('status', true)
            ->where('company_id', auth()->user()->company_id)
            ->with(['consignee', 'order'])
            ->orderBy('id', 'desc')
            ->get();

        if ($consignments) {
            $this->data['consignments'] = $consignments;
        }

        return view('station.index_decathlon', $this->data);

    }
    // index_hb
    public function index5()
    {

        $consignments = \App\Consignment::where('status', true)
            ->where('company_id', auth()->user()->company_id)
            ->with(['consignee', 'order'])
            ->orderBy('id', 'desc')
            ->get();

        if ($consignments) {
            $this->data['consignments'] = $consignments;
        }

        return view('station.index_hb', $this->data);

    }
    // index_target
    public function index6()
    {
        //echo GetGTINFromEPC('30340BFC344F5DCBA43B7AFE').'<br>';
//        echo GetGTINFromEPCTarget('30340BFC344F5DCBA43B8315');
//        exit();

        $consignments = \App\Consignment::where('status', true)
            ->where('company_id', auth()->user()->company_id)
            ->with(['consignee', 'order'])
            ->orderBy('id', 'desc')
            ->get();

        if ($consignments) {
            $this->data['consignments'] = $consignments;
        }

        return view('station.index_target', $this->data);

    }
    // index_levis(dhl)
    public function index7(){

        $consignments = \App\Consignment::where('status', true)
            ->where('company_id', auth()->user()->company_id)
            ->with(['consignee', 'order'])
            ->orderBy('id', 'desc')
            ->get();

        if ($consignments) {
            $this->data['consignments'] = $consignments;
        }

        return view('station.index_levis', $this->data);

    }

    function hex2bin($hex){
        return ("0000" + (parseInt($hex, 16)).toString(2)).substr(-4);
    }

    //Gtin Data
    public function GetGTINFromEPC($epc)
    {

        $length = strlen($epc);
        $binaryEpc = "";
        for ($i = 0; $i < $length; $i++) {
            $binaryEpc .= substr("0000" . decbin(hexdec($epc[$i])), -4);
        }

        $companyBinary = substr($binaryEpc, 14, 20);
        $itemBinary = substr($binaryEpc, 34, 24);

        $company = bindec($companyBinary);
        $item = bindec($itemBinary);
        if (strlen($item) < 6) {
            $item = "0" . $item;
        }

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

    public function GetGTINFromEPCHb($epc)
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

    public function GetGTINFromEPCTarget($epc)
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

    public function ajax(Request $request)
    {

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');

        // ajax with sent task ->  process
        switch ($request->get('process')) {

            /*
            * @desc    all boxtypes
            * @param   not parameter
            * @return  array convert json
            */

            case 'getBoxTypes':

                $boxTypes = BoxType::get();
                if ($boxTypes) {
                    return response()->json(['boxes' => $boxTypes]);
                } else {
                    return response()->json('empty list');
                }
                break;

            /*
            * @desc    details company
            * @param   consignmentId
            * @return  array convert json
            */
            case 'getProductDetails':

                if ($request->has("consignmentId")) {

                    $prods = CompanyDb::where('consignment_id', $request->get('consignmentId'))->get();
                    if ($prods) {
                        return response()->json(['prods' => $prods]);
                    } else {
                        return response()->json('empty list');
                    }

                } else {
                    return response()->json(['message' => 'Missing parameter-Consignment Id'], 400);
                }
                break;

            case 'getProductMsDetails':

                if ($request->has("consignmentId")) {

                    $prods = CompanyMsDb::where('consignment_id', $request->get('consignmentId'))->get();
                    // echo '<pre>';
                    // echo $request->get('consignmentId').'_sad<br>';
                    // print_r($prods);
                    // exit();
                    if ($prods) {
                        return response()->json(['prods' => $prods]);
                    } else {
                        return response()->json('empty list');
                    }

                } else {
                    return response()->json(['message' => 'Missing parameter-Consignment Id'], 400);
                }
                break;

            case 'getProductTargetDetails':

                if ($request->has("consignmentId")) {

                    $prods = CompanyTargetDb::where('consignment_id', $request->get('consignmentId'))->get();
                    if ($prods) {
                        return response()->json(['prods' => $prods]);
                    } else {
                        return response()->json('empty list');
                    }

                } else {
                    return response()->json(['message' => 'Missing parameter-Consignment Id'], 400);
                }
                break;

            case 'getProductLevisDetails':

                if ($request->has("consignmentId")) {

                    $prods = CompanyLevisDb::where('consignment_id', $request->get('consignmentId'))->get();
                    if ($prods) {
                        return response()->json(['prods' => $prods]);
                    } else {
                        return response()->json('empty list');
                    }

                } else {
                    return response()->json(['message' => 'Missing parameter-Consignment Id'], 400);
                }
                break;

            case 'consignmentVote':

                // status 1 olan firma sec
                $company = DB::table('company_consignee')
                    ->select('*')
                    ->join('consignees', function ($join) {
                        $join->on('company_consignee.consignee_id', '=', 'consignees.id');
                    })
                    ->where('company_consignee.company_id', auth()->user()->company_id)
                    ->where('status', 1)
                    ->orderBy('id', 'ASC')
                    ->get();
                //$company = \App\Consignee::where('status', 1)->orderBy('id', 'ASC')->get();
                // echo '<pre>';
                // print_r($company);
                // exit();
                $companyArray = array();

                if (count($company) > 0) {

                    foreach ($company as $key => $value) {
                        // firma bilgileri
                        $com = array(
                            'id' => $value->id,
                            'name' => $value->name,
                            'logo' => $value->logo,
                            'status' => $value->status
                        );
                        // firmaların sevkiyat sayısını bul
                        $consignmetSor = \App\Consignment::where('consignee_id', $value->id)
                            ->orderBy('id', 'desc')
                            ->get();

                        $companyArray[count($consignmetSor)][] = $com;

                    }

                }
                // en cok sevkiyat yapana gore sıralat
                krsort($companyArray);

                if ($companyArray) {
                    $this->data['company'] = $companyArray;
                }
                return response()->json(
                    ['status' => 'ok', 'html' => view('station.consigments.consignmentVote', $this->data)->render()]
                );
                break;

            case 'consigmentZara':

                $company = \App\Company::with(['consignees'])->find(auth()->user()->company_id);
                if ($company) {
                    $this->data['company'] = $company;
                }

                return response()->json(
                    ['status' => 'ok', 'html' => view('station.consigments.consigmentZara', $this->data)->render()]
                );
                break;

            case 'consigmentHm':

                $company = \App\Company::with(['consignees'])->find(auth()->user()->company_id);
                $country_list = DB::table('country_list')->orderBy('country_list_name', 'ASC')->get();
                if ($company) {
                    $this->data['company'] = $company;
                }
                $this->data['country_list'] = $country_list;
                return response()->json(
                    ['status' => 'ok', 'html' => view('station.consigments.consigmentHm', $this->data)->render()]
                );
                break;

            case 'consigmentMs':

                $company = \App\Company::with(['consignees'])->find(auth()->user()->company_id);
                $country_list = DB::table('country_list')->orderBy('country_list_name', 'ASC')->get();
                if ($company) {
                    $this->data['company'] = $company;
                }
                $this->data['country_list'] = $country_list;
                return response()->json(
                    ['status' => 'ok', 'html' => view('station.consigments.consigmentMs', $this->data)->render()]
                );
                break;

            case 'consigmentDc':

                $company = \App\Company::with(['consignees'])->find(auth()->user()->company_id);
                if ($company) {
                    $this->data['company'] = $company;
                }
                return response()->json(
                    ['status' => 'ok', 'html' => view('station.consigments.consigmentDc', $this->data)->render()]
                );
                break;

            case 'consigmentHb':

                $company = \App\Company::with(['consignees'])->find(auth()->user()->company_id);
                $country_list = DB::table('country_list')->orderBy('country_list_name', 'ASC')->get();
                if ($company) {
                    $this->data['company'] = $company;
                }
                $this->data['country_list'] = $country_list;
                return response()->json(
                    ['status' => 'ok', 'html' => view('station.consigments.consigmentHb', $this->data)->render()]
                );
                break;

            case 'consigmentTarget':

                $company = \App\Company::with(['consignees'])->find(auth()->user()->company_id);
            //    $country_list = DB::table('country_list')->orderBy('country_list_name', 'ASC')->get();
                if ($company) {
                    $this->data['company'] = $company;
                }
            //    $this->data['country_list'] = $country_list;
                return response()->json(
                    ['status' => 'ok', 'html' => view('station.consigments.consigmentTarget', $this->data)->render()]
                );
                break;

            case 'consigmentLevis':

                $company = \App\Company::with(['consignees'])->find(auth()->user()->company_id);
                $country_list = DB::table('country_list')->orderBy('country_list_name', 'ASC')->get();
                if ($company) {
                    $this->data['company'] = $company;
                }
                $this->data['country_list'] = $country_list;
                return response()->json(
                    ['status' => 'ok', 'html' => view('station.consigments.consigmentLevis', $this->data)->render()]
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

                    //diğer butonundan gelen firmayı yeni olarak kaydet
                    if ($request->get('company_name')) {

                        $company = new \App\Consignee;
                        $company->name = $request->get('company_name');
                        $company->logo = 'takipsan.jpg';
                        $company->phone = '+905555555555';
                        $company->address = '-';
                        $company->auth_name = '-';
                        $company->auth_phone = '-';
                        $company->created_user_id = auth()->user()->id;
                        $company->status = 1;
                        $company->viewid = 1; //default gorunum zara
                        if ($company->save()) {
                            //markaya firma bilgisi ekleniyor.
                            // $companies = new \App\Company;
                            // $companies->company_id = auth()->user()->company_id;
                            // $companies->consignee_id = $company->id;
                            // $companies->save();
                            // echo $companies->company_id.'__company<br>'.$companies->consignee_id.'__consigne';
                            // exit();

                            // $companyConsignee->consignee_id = $company->id;
                            // $companyConsignee->company_id = auth()->user()->company_id;


                        }

                    }

                    $order = \App\Order::where('po_no', $request->get('po_no'))->first();
                    if (!$order) {

                        $order = new \App\Order;
                        $order->order_code = $this->autoGenerateOrderCode();
                        $order->consignee_id = $request->get('consignee_id') != 'other' ? $request->get('consignee_id') : $company->id;
                        $order->po_no = $request->get('po_no');
                        $order->name = $request->get('company_name') ?? 'Takipsan';
                        $order->item_count = $request->get('item_count');
                        $order->created_user_id = auth()->user()->id;
                        $order->save();

                    }

                    if ($order) {

                        $this->createLog('Order', 'portal.log_create_order', ['name' => $order->po_no], $order->id);

                        if ($request->get('company_name')) {

                            $consigneeFind = $company->id;

                        } else {

                            $consigneeFind = $request->get('consignee_id');

                        }

                        $consigneeName = \App\Consignee::find((int)$consigneeFind);
                        if ($consigneeName) {
                            $conName = $consigneeName->name;
                        } else {
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
                        if ($consignment->save()) {

                            $pox = explode('/', $consignment->name);
                            if (is_array($pox)) {
                                $order->po_no = $pox[0];
                                $order->save();
                            }

                        }

                        $this->createLog(
                            'Consignment', 'portal.log_create_consignment',
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

                    }

                    session()->flash(
                        'flash_message',
                        array(trans('station.successful'), trans('station.consignment_successfully'), 'success')
                    );

                    return response()->json([
                        'status' => 'ok',
                        'consignmentId' => $consignment->id,
                        'url' => route('station.index', ['consignment' => $consignment->id])
                    ]);

                } catch (\Exception $e) {

                    session()->flash(
                        'flash_message',
                        array(trans('station.failed'), trans('station.error_text'), 'error')
                    );
                    return redirect()->back()->withInput();

                }
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

                    if ($order) {

                        $this->createLog('Order', 'portal.log_create_order', ['name' => $order->po_no], $order->id);

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
                        if ($consignment->save()) {

                            if ($request->hasFile('db_list')) {

                                $path = $_FILES["db_list"]['tmp_name'];
                                //$request->file('db_list')->getRealPath();
                                $this->get_absolute_path($path);
                                $inx = 0;
                                $model = "";
                                foreach (file($path) as $key => $value) {

                                    $line = explode(';', $value);
                                    if (is_array($line)) {

                                        if ($inx != 0) {

                                            CompanyDb::where('gtin', $line[0])->delete();
                                            $gtin = $line[4];

                                            if ($gtin[0] == '0') {
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

                                // echo ('sevkiyat_adi => '.$consignment->name);
                                // exit();
                                $order->save();

                                $consigneeName = \App\Consignee::find((int)$request->get('sticker'));
                                if ($consigneeName) {
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
                            'Consignment', 'portal.log_create_consignment',
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
                        array(trans('station.successful'), trans('station.consignment_successfully'), 'success')
                    );

                    return response()->json([
                        'status' => 'ok',
                        'consignmentId' => $consignment->id,
                        'url' => route('station.index', ['consignment' => $consignment->id])
                    ]);

                } catch (\Exception $e) {

                    session()->flash(
                        'flash_message',
                        array(trans('station.failed'), trans('station.error_text'), 'error')
                    );
                    return redirect()->back()->withInput();

                }
                break;

            case 'consigmentMsStore':
                // excel okuma adımlarını helper üzerinden yürütmeye çalış..
                try {

                    $attribute = array(
                        'po_no' => trans('station.po_number'),
                        'hanging_product' => trans('station.hanging_product'),
                        //'country_code' => trans('station.country'),
                        //'item_count' => trans('station.product_quantity'),
                        //'consignee_id' => trans('station.consignee_name'),
                        //'delivery_date' => trans('station.delivery_date'),
                        //'plate_no' => trans('station.plate_no')
                        //'db_list' => trans('station.file_upload'),
                    );

                    $rules = array(
                        'po_no' => 'required',
                        'hanging_product' => 'required',
                        //'country_code' => 'required',
                        //'item_count' => 'required|numeric|min:1',
                        // 'consignee_id' => 'required',
                        //'delivery_date' => 'required|date|date_format:Y-m-d',
                        // 'plate_no' => 'nullable',
                        //'db_list' => 'required|mimes:pdf|max:2048'
                    );

                    $validator = Validator::make($request->all(), $rules);
                    $validator->setAttributeNames($attribute);
                    if ($validator->fails()) {
                        return response()->json([
                            'status' => false,
                            'errors' => $validator->getMessageBag()->toArray()
                        ]);
                    }

                    $conQuery = \App\Consignment::where('name', 'like', '%' . $request->get('po_no') . '%')
                        ->where('status', true)
                        ->whereNull('deleted_at')
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($conQuery) {

                        session()->flash(
                            'flash_message',
                            array(trans('station.similar'), trans('station.error_similar_text'), 'error')
                        );

                        return response()->json([
                            'status' => 'ok',
                            'url' => route('station.ms', ['consignment' => $conQuery->id])
                        ]);

                    }

                    $order = \App\Order::where('po_no', $request->get('po_no'))->first();
                    if (!$order) {

                        $order = new \App\Order;
                        $order->order_code = $this->autoGenerateOrderCode();
                        $order->consignee_id = $request->get('sticker');
                        $order->po_no = $request->get('po_no');
                        $order->name = $request->get('name') ?? "null";
                        $order->item_count = $request->get('item_count');
                        $order->created_user_id = auth()->user()->id;
                        $order->save();

                    }

                    if ($order) {

                        $this->createLog('Order', 'portal.log_create_order', ['name' => $order->po_no], $order->id);

                        $consigneeName = \App\Consignee::find((int)$request->get('sticker'));
                        if ($consigneeName) {
                            $conName = $consigneeName->name;
                        }

                        $consignment = new \App\Consignment;
                        $consignment->order_id = $order->id;
                        //$consignment->country_code = "RUW337";
                        $consignment->company_id = auth()->user()->company_id;
                        //$consignment->name = $this->autoGeneratePoNo($order->id, $consignment->country_code, $conName);
                        $consignment->name = $request->get('po_no') . '/' . $conName;
                        $consignment->delivery_date = date('Y-m-d');
                        $consignment->consignee_id = $request->get('sticker') != 'other' ? $request->get('sticker') : auth()->user()->company_id;
                        $consignment->item_count = $request->get('item_count') ? $request->get('item_count') : 0;
                        $consignment->hanging_product = $request->get('hanging_product') == 'Evet' ? 1 : 0;
                        $consignment->created_user_id = auth()->user()->id;

                        if ($consignment->save()) {

                            // dosya varsa dosyayı convert et. convert hali ile işleme gir.Ajax kullanabilirsin.
                            //consignment name dogru gelmeyebilir. işlem sonunda da tekrar bakabilirsin.
                            //order season ve po_no convert edilen dosya içerisinden alınıcak.

                            // echo 'ftm_sad2'.$request->get('po_no').'<br>';
                            // exit();
//                            if ($request->get('po_no') && $request->get('hanging_product') != 'Evet') {
                            if ($request->get('po_no')){

                                $xmlFileRepoQuery = DB::table('xml_file_repos')
                                    ->select('*')
                                    ->join('txt_file_repos', function ($join) {
                                        $join->on('txt_file_repos.xmlid', '=', 'xml_file_repos.id');
                                    })
                                    ->where('xml_file_repos.poNumber', $request->get('po_no'))
                                    ->orderBy('xml_file_repos.id', 'ASC')
                                    ->get();

                                if (isset($xmlFileRepoQuery) && count($xmlFileRepoQuery) > 0 ) {

                                    $upcList = array();
                                    $sizeArray = array();
                                    foreach ($xmlFileRepoQuery as $xmlFile) {

                                        $cartons = json_decode($xmlFile->cartons);
                                        $seasonId = $xmlFile->season;
                                        //$cartonSinglesTotal = 0;
                                        foreach ($cartons as $cKey => $carton) {

                                            foreach ($carton->upcs as $upcKey => $upcs) {

                                                $colorName = $xmlFile->colourCode . ' ' .
                                                    $xmlFile->colourDesc . ' ' .
                                                    $xmlFile->departmentDesc . ' ' .
                                                    $xmlFile->strokeDesc;

                                                $data = [
                                                    'consignment_id' => $consignment->id,
                                                    'order' => $request->get('po_no'),
                                                    'season' => $seasonId,
                                                    'description' => $colorName,
                                                    'sds_code' => $upcs->size,
                                                    'barcode' => $carton->barcode,
                                                    'upc' => $upcs->upc,
                                                    'price' => null,
                                                    'story_desc' => null,
                                                    'qty_req' => null,
                                                    'user_id' => auth()->user()->id,
                                                ];

                                                CompanyMsDb::create($data);

                                                $upc = (int)$upcs->upc;
                                                $size = (string)$upcs->size;
                                                $poUpcQuantity = (int)$upcs->poUpcQuantity;
                                                $cartonID = (int)$carton->cartonID;
                                                $cartonSeries = $carton->series;
                                                $cartonColour = $carton->colour;
                                                $cartonSingles = $carton->singles;
                                                //$cartonSinglesTotal = $cartonSinglesTotal + $carton->singles;
                                                $cartonBarcode = $carton->barcode;

                                                if (!array_key_exists($size, $sizeArray)) {
                                                    $sizeArray[$size] = $poUpcQuantity;
                                                } else {
                                                    $sizeArray[$size] = $sizeArray[$size] + $poUpcQuantity;
                                                }
                                                if (empty($upcArr) || !array_key_exists($upc, $upcArr)) {

                                                    $upcArr[$upc] = [
                                                        'upc' => $upc,
                                                        'size' => $size,
                                                        'qty' => $poUpcQuantity,
                                                        'descriptions' => $colorName
                                                    ];

                                                    $cartonArr = [
                                                        'upc' => $upc,
                                                        'cartonID' => $cartonID,
                                                        'series' => $cartonSeries,
                                                        'colour' => $cartonColour,
                                                        'singles' => $cartonSingles,
                                                        'barcode' => $cartonBarcode,
                                                        'consignment_id' => $consignment->id,
                                                    ];

                                                    //MsCarton::create($cartonArr);

                                                } else {

                                                    $cartonArr = [
                                                        'upc' => $upc,
                                                        'cartonID' => $cartonID,
                                                        'series' => $cartonSeries,
                                                        'colour' => $cartonColour,
                                                        'singles' => $cartonSingles,
                                                        'barcode' => $cartonBarcode,
                                                        'consignment_id' => $consignment->id,
                                                    ];

                                                }

                                                MsCarton::create($cartonArr);

                                            }

                                            //$upcArr[$upc]['allSize'] = $sizeArray;

                                        }

                                    }

                                    foreach ($upcArr as $key => $value) {

                                        $upcData = [
                                            'consignment_id' => $consignment->id,
                                            'user_id' => auth()->user()->id,
                                            'upc' => $key,
                                            'size' => $value['size'],
                                            'po_upc_quantity' => $value['qty'],
                                            'descriptions' => $value['descriptions']
                                            //'cartons' => json_encode($value['cartons']),

                                        ];

                                        MsUpcCarton::create($upcData);

                                    }

//                                    foreach ($cartonArr as $key => $value){
//
//                                        $barcodeData = [
//                                            'upc' => $value['upc'],
//                                            'cartonID' => $value['cartonID'],
//                                            'series' => $value['cartonSeries'],
//                                            'colour' => $value['cartonColour'],
//                                            'singles' => $value['cartonSingles'],
//                                            'barcode' => $value['cartonBarcode'],
//                                        ];
//
//                                        MsCarton::create($barcodeData);
//
//                                    }

                                } else {

                                    //$conDel = DB::delete("delete from consignments where consigment_id = ? ",[$consignment->id]);
                                    $conDel = \App\Consignment::where('id', $consignment->id)->delete();

                                    return response()->json([
                                        'status' => false,
                                        'url' => route('station.index', ['consignment' => $consignment->id])
                                    ]);

                                }

                                $pox = explode('/', $consignment->name);
                                if (is_array($pox)) {
                                    $order->po_no = $pox[0];
                                }
                                //echo $seasonId.'<br>';
                                $order->season = $seasonId;
                                $order->save();

                            }

//                            if ($request->get('po_no') && $request->get('hanging_product') == 'Evet'){
//
//                            }

                        }

                        $this->createLog(
                            'Consignment', 'portal.log_create_consignment',
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
                        array(trans('station.successful'), trans('station.consignment_successfully'), 'success')
                    );

                    return response()->json([
                        'status' => 'ok',
                        'hanging_product' => $consignment->hanging_product,
                        'consignmentId' => $consignment->id,
                        'url' => route('station.index', ['consignment' => $consignment->id])
                    ]);

                } catch (\Exception $e) {

                    session()->flash(
                        'flash_message',
                        array(trans('station.failed'), trans('station.error_text'), 'error')
                    );
                    return redirect()->back()->withInput();

                }
                break;

            case 'consigmentDcStore':

                try {

                } catch (\Exception $e) {

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

                    if ($order) {

                        $this->createLog('Order', 'portal.log_create_order', ['name' => $order->po_no], $order->id);

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
                        if ($consignment->save()) {

                            if ($request->hasFile('db_list')) {

                                $path = $_FILES["db_list"]['tmp_name'];
                                //$request->file('db_list')->getRealPath();
                                $path = $request->file('db_list')->getRealPath();
                                $inx = 0;
                                $model = "";
                                if ($xlsx = \SimpleXLSX::parse($path)) {

                                    foreach ($xlsx->rows() as $sKey => $sValue) {

                                        if ($sKey > 1) {

                                            $sku = $sValue[0];
                                            $name = $sValue[3];
                                            $part = $sValue[4];
                                            $referans = $sValue[5];
                                            $season = $sValue[6];
                                            $size = $sValue[7];
                                            $color = $sValue[8];
                                            $count = $sValue[9];

                                            //CompanyDb::where('gtin',$line[0])->delete();

                                            if (substr($sku, 0, 1) == 0) {
                                                $sku = substr($sku, 1, strlen($sku));
                                            }

                                            $order->po_no = $part;
                                            $order->season = $season;
                                            if (!empty($color)) {
                                                $model = $size . '/' . $color;
                                            } else {
                                                $model = $size;
                                            }

                                            $data = [
                                                'consignment_id' => $consignment->id,
                                                'order' => $part,
                                                'user_id' => auth()->user()->id,
                                                'season' => $season,
                                                'product' => $part,
                                                'variant' => '',
                                                'gtin' => $sku,
                                                'article_number' => '',
                                                'sds_code' => $model,
                                                'description' => $name,
                                            ];

                                            CompanyDb::create($data);

                                        }

                                    }

                                }
                                // echo ('sevkiyat_adi => '.$consignment->name);
                                // exit();
                                $order->save();

                                $consigneeName = \App\Consignee::find((int)$request->get('sticker'));
                                if ($consigneeName) {
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
                            'Consignment', 'portal.log_create_consignment',
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
                        array(trans('station.successful'), trans('station.consignment_successfully'), 'success')
                    );

                    return response()->json([
                        'status' => 'ok',
                        'consignmentId' => $consignment->id,
                        'url' => route('station.index', ['consignment' => $consignment->id])
                    ]);

                } catch (\Exception $e) {

                    session()->flash(
                        'flash_message',
                        array(trans('station.failed'), trans('station.error_text'), 'error')
                    );
                    return redirect()->back()->withInput();

                }
                break;

            case 'consigmentTargetStore':

                try {

                    $attribute = array(
                    //    'country_code' => trans('station.country'),
                        'item_count' => trans('station.product_quantity'),
                        'delivery_date' => trans('station.delivery_date'),
                        //'db_list' =>  trans('station.upload_list_xls'),
                    );

                    $rules = array(
                    //    'country_code' => 'required',
                        'item_count' => 'required|numeric|min:1',
                        'delivery_date' => 'required|date|date_format:Y-m-d',
                        //'db_list' => 'required|mimes:xlsx, xls',
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

                    if ($order) {

                        $this->createLog('Order', 'portal.log_create_order', ['name' => $order->po_no], $order->id);

                        $consignment = new \App\Consignment;
                        $consignment->order_id = $order->id;
                    //    $consignment->country_code = $request->get('country_code');
                        $consignment->company_id = auth()->user()->company_id;
                        $consignment->item_count = $request->get('item_count');
                        $consignment->delivery_date = $request->get('delivery_date');
                        $consignment->consignee_id = $request->get('sticker');
                        $consignment->created_user_id = auth()->user()->id;
                        if ($consignment->save()) {

                            if ($request->hasFile('db_list')) {

                                $pathTarget = $_FILES["db_list"]['tmp_name'];
                                //$request->file('db_list')->getRealPath();
                                $pathTarget = $request->file('db_list')->getRealPath();
                                $pathTargetEx = $request->file('db_list')->extension();

                                //echo $pathTargetEx;
                                if ($pathTargetEx == 'xlsx'){

                                    $xlsx = \SimpleXLSX::parse($pathTarget);

                                }elseif ($pathTargetEx == 'xls'){

                                    $xlsx = SimpleXLS::parse($pathTarget);

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
                                //echo '<pre>';
                                if ($xlsx) {

                                    foreach ($xlsx->rows() as $sKey => $sValue) {

                                        if ($sKey > 0) {

                                            //print_r($sValue);

                                            if ( !empty(array_filter($sValue))) {

                                                if (
                                                    $sValue[0] != "" &&
                                                    $sValue[1] != "" &&
                                                    $sValue[2] != "" &&
                                                    $sValue[3] != "" &&
                                                    $sValue[4] != ""
                                                ){

                                                    $po_number = $sValue[0];
                                                    $vendor_id = $sValue[1];
                                                    $department = $sValue[2];
                                                    $class = $sValue[3];
                                                    $item = $sValue[4];
                                                    $dpc_no = $department.'-'.$class.'-'.$item;
                                                    $item_description = $sValue[6];
                                                    $vendor_style = $sValue[7];
                                                    $color = $sValue[8];
                                                    $size = $sValue[9];
                                                    $item_barcode = $sValue[10];
                                                    $vcp_quantity = $sValue[11];
                                                    $ssp_quantity = $sValue[12];
                                                    $total_item_qty = $sValue[13];
                                                    $item_unit_cost = $sValue[14];
                                                    $item_unit_retail = $sValue[15];
                                                    $country_origin = $sValue[16];
                                                    $hs_tariff = $sValue[17];
                                                    $shipping_documents = $sValue[18];
                                                    $assortment_item = $sValue[19];
                                                    $component_department = $sValue[20];
                                                    $component_class = $sValue[21];
                                                    $component_item = $sValue[22];
                                                    $component_item_desciprtion = $sValue[23];
                                                    $component_style = $sValue[24];
                                                    $component_assort_qty = $sValue[25];
                                                    $component_item_total_qty = $sValue[26];
                                                    $item_changed_date = $sValue[27];

                                                    $order->po_no = $po_number;
                                                    $order->season = $vendor_id;

                                                    $data = [
                                                        'consignment_id' => $consignment->id,
                                                        'order' => $po_number,
                                                        'user_id' => auth()->user()->id,
                                                        'po_number' => $po_number,
                                                        'vendor_id' => $vendor_id,
                                                        'department' => $department,
                                                        'class' => $class,
                                                        'item' => $item,
                                                        'dpc_no' => $dpc_no,
                                                        'item_description' => $item_description,
                                                        'vendor_style' => $vendor_style,
                                                        'color' => $color,
                                                        'size' => $size,
                                                        'item_barcode' => $item_barcode,
                                                        'vcp_quantity' => $vcp_quantity,
                                                        'ssp_quantity' => $ssp_quantity,
                                                        'total_item_qty' => $total_item_qty,
                                                        'item_unit_cost' => $item_unit_cost,
                                                        'item_unit_retail' => $item_unit_retail,
                                                        'country_origin' => $country_origin,
                                                        'hs_tariff' => $hs_tariff,
                                                        'shipping_documents' => $shipping_documents,
                                                        'assortment_item' => $assortment_item,
                                                        'component_department' => $component_department,
                                                        'component_class' => $component_class,
                                                        'component_item' => $component_item,
                                                        'component_item_desciprtion' => $component_item_desciprtion,
                                                        'component_style' => $component_style,
                                                        'component_assort_qty' => $component_assort_qty,
                                                        'component_item_total_qty' => $component_item_total_qty,
                                                        'item_changed_date' => $item_changed_date,
                                                    ];

                                                    //print_r($data);

                                                    CompanyTargetDb::create($data);

                                                }

                                            }

                                        }

                                    }

                                }

                                //exit();

                                $order->save();

                                $consigneeName = \App\Consignee::find((int)$request->get('sticker'));
                                if ($consigneeName) {
                                    $conName = $consigneeName->name;
                                }
                                //$consignment->name = '_sad';
                                //$consignment->name = $this->autoGeneratePoNo($order->id, $request->get('country_code'), $conName);
                                $consignment->name = $order->po_no."/".$conName;  
                                $consignment->save();

                            }

                        }

                        $this->createLog(
                            'Consignment', 'portal.log_create_consignment',
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
                        array(trans('station.successful'), trans('station.consignment_successfully'), 'success')
                    );

                    return response()->json([
                        'status' => 'ok',
                        'url' => route('station.target', ['consignment' => $consignment->id])
                    ]);

                }catch (\Exception $e) {

                    session()->flash(
                        'flash_message',
                        array(trans('station.failed'), trans('station.error_text'), 'error')
                    );
                    return redirect()->back()->withInput();

                }
                break;

            case 'consigmentLevisStore':

                try {

                    $attribute = array(
                        'country_code' => trans('station.country'),
                        'item_count' => trans('station.product_quantity'),
                        'delivery_date' => trans('station.delivery_date'),
                        //'db_list' =>  trans('station.upload_list_xls'),
                    );

                    $rules = array(
                        'country_code' => 'required',
                        'item_count' => 'required|numeric|min:1',
                        'delivery_date' => 'required|date|date_format:Y-m-d',
                        //'db_list' => 'required|mimes:xlsx, xls',
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

                    if ($order) {

                        $this->createLog('Order', 'portal.log_create_order', ['name' => $order->po_no], $order->id);

                        $consignment = new \App\Consignment;
                        $consignment->order_id = $order->id;
                        $consignment->country_code = $request->get('country_code');
                        $consignment->company_id = auth()->user()->company_id;
                        $consignment->item_count = $request->get('item_count');
                        $consignment->delivery_date = $request->get('delivery_date');
                        $consignment->consignee_id = $request->get('sticker');
                        $consignment->created_user_id = auth()->user()->id;
                        if ($consignment->save()) {

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
                                    //echo '<pre>';
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
                            'Consignment', 'portal.log_create_consignment',
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
                        array(trans('station.successful'), trans('station.consignment_successfully'), 'success')
                    );

                    return response()->json([
                        'status' => 'ok',
                        'url' => route('station.levis', ['consignment' => $consignment->id])
                    ]);

                }catch (\Exception $e) {

                    session()->flash(
                        'flash_message',
                        array(trans('station.failed'), trans('station.error_text'), 'error')
                    );
                    return redirect()->back()->withInput();

                }
                break;

            case 'editConsignment':

                $data = $request->get('param');
                if (isset($data['id'])) {

                    $consignment = \App\Consignment::with(['order'])->findOrFail($data['id']);
                    if ($consignment) {
                        $this->data['consignment'] = $consignment;
                    }

                    $company = \App\Company::with(['consignees'])->find(auth()->user()->company_id);
                    if ($company) {
                        $this->data['company'] = $company;
                    }

                    $country_list = DB::table('country_list')->orderBy('country_list_name', 'ASC')->get();
                    if ($country_list) {
                        $this->data['country_list'] = $country_list;
                    }

                }

                return response()->json(
                    ['status' => 'ok', 'html' => view('station.consigments.consignmentEdit', $this->data)->render()]
                );
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
                    if ($request->get('country_code')) {

                        $ex = explode('/', $consignment->name);
                        $newName = $ex[0] . '/' . $request->get('country_code') . '/' . $ex[2];

                    } else {

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
                        'Consignment', 'portal.log_update_consignment',
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

                    //session()->flash('flash_message', array(trans('station.successful'),trans('station.consignment_updated'), 'success'));
                    return response()->json(['status' => 'ok']);

                } catch (\Exception $e) {

                    //session()->flash('flash_message', array(trans('station.failed'),trans('station.error_text'), 'error'));
                    return response()->json(['status' => false, 'message' => trans('station.error_text')]);

                }
                break;

            case 'closeConsignment':

                if ($request->has('consignmentId') && $request->post('CloseShipmentSt') == 1) {

                    $consignment = \App\Consignment::find($request->get('consignmentId'));
                    $consignment->status = false;
                    $consignment->updated_user_id = auth()->user()->id;
                    if ($consignment->save()) {

                        $this->createLog('Consignment', 'portal.log_statusclose_consignment', ['name' => $consignment->name], $consignment->id);
                        return response()->json('ok');

                    }

                    return response()->json(['message' => 'Sevkiyat Bulunamadı'], 400);

                } else {

                    return response()->json(['message' => 'Sevkiyat Bulunamadı'], 400);

                }
                break;

            case 'totalSizeQuantityMs' :

                try {

                    $getPoNumber = $request->post('poNo');
                    $poNumber = explode("/", $getPoNumber);
                    $poNumber = $poNumber[0];
                    //echo $poNumber;

                    $xmlFileRepoQuery = DB::table('xml_file_repos')
                        ->select('*')
                        ->join('txt_file_repos', function ($join) {
                            $join->on('txt_file_repos.xmlid', '=', 'xml_file_repos.id');
                        })
                        ->where('xml_file_repos.poNumber', $poNumber)
                        ->orderBy('xml_file_repos.id', 'ASC')
                        ->get();

                    // echo '<pre>';
                    // print_r($xmlFileRepoQuery);
                    // exit();

                    if ($xmlFileRepoQuery) {

                        $totalQuantityValue = 0;
                        $totalSinglesValue = 0;

                        foreach ($xmlFileRepoQuery as $xmlFile) {

                            $cartons = json_decode($xmlFile->cartons, true);
                            foreach ($cartons as $k => $cartonsSingles) {

                                foreach ($cartonsSingles as $key => $value) {

                                    if ($key == "singles") {

                                        $totalSinglesValue = $totalSinglesValue + $value;

                                    }

                                }

                            }

                            $totalSizeQuantity = json_decode($xmlFile->totalSizeQuantity, true);
                            foreach ($totalSizeQuantity as $k => $totalQuantity) {

                                //print_r ($value);
                                foreach ($totalQuantity as $key => $value) {
                                    if ($key == "totalQuantity") {

                                        $totalQuantityValue = $value;

                                    }
                                    //echo $k.'_sdk_ftm<br>';
                                }

                            }

                        }

                    }

                    $data = [
                        'totalSinglesValue' => $totalSinglesValue,
                        'totalQuantityValue' => $totalQuantityValue
                    ];

                    return $data;

                } catch (\Exception $e) {

                    session()->flash(
                        'flash_message',
                        array(trans('station.failed'), trans('station.error_text'), 'error')
                    );
                    return redirect()->back()->withInput();

                }
                break;

            case 'barcodeMs' :

                try {

                    $getPoNumber = $request->post('poNo');
                    $poNumber = explode("/", $getPoNumber);
                    $poNumber = $poNumber[0];
                    $barcode = $request->post('barcode');
                    $data = array();

                    $xmlFileRepoQuery = DB::table('xml_file_repos')
                        ->select('*')
                        ->join('txt_file_repos', function ($join) {
                            $join->on('txt_file_repos.xmlid', '=', 'xml_file_repos.id');
                        })
                        ->where('xml_file_repos.poNumber', $poNumber)
                        ->orderBy('xml_file_repos.id', 'ASC')
                        ->get();

                    if ($xmlFileRepoQuery) {

                        foreach ($xmlFileRepoQuery as $xmlFile) {

                            $cartons = json_decode($xmlFile->cartons);
                            foreach ($cartons as $carton) {

                                if ($carton->barcode == $barcode) {

                                    //echo $carton->barcode.'eslesti veri bas<br>';
                                    $data = [
                                        'cartonID' => $carton->cartonID,
                                        'barcode' => $carton->barcode,
                                        'singles' => $carton->singles,
                                        'colourCode' => $xmlFile->colourCode,
                                        'departmentDesc' => $xmlFile->departmentDesc,
                                        'strokeDesc' => $xmlFile->strokeDesc,
                                        'colourDesc' => $xmlFile->colourDesc,
                                    ];

                                    // $barcodeQuery = DB::table('xml_file_repos')
                                    //     ->join('txt_file_repos', function($join) {
                                    //         $join->on('xml_file_repos.id', "=", 'txt_file_repos.xmlid');
                                    //     })
                                    //     ->where('cartons', 'like' , '%'.$packages[0]['barcode'].'%')
                                    //     ->get();

                                }

                            }

                        }

                        // echo '<pre>';
                        // print_r($data);

                        return response()->json([
                            'status' => 'ok',
                            'res' => $data,
                        ]);

                    }

                } catch (\Exception $e) {

                    session()->flash(
                        'flash_message',
                        array(trans('station.failed'), trans('station.error_text'), 'error')
                    );
                    return redirect()->back()->withInput();

                }
                break;

            case 'barcodeCheck' :

                try {

                    $barcode = $request->post('barcode');
                    $consignmentId = $request->post('consignmentId');

                    $upcQuery = \App\MsUpcCarton::where('consignment_id', $consignmentId)->get();
                    if (count($upcQuery) > 0) {
                        //$upcQuery = \App\MsUpcCarton::where('consignment_id', $consignmentId)->get();
                        $upcQuery = DB::table('ms_upc_cartons')
                            ->select('*')
                            ->join('ms_cartons', function ($join) {
                                $join->on('ms_upc_cartons.upc', '=', 'ms_cartons.upc');
                            })
                            ->where('ms_upc_cartons.consignment_id', $request->get('consignmentId'))
                            ->where('ms_cartons.barcode', $barcode)
                            ->orderBy('ms_upc_cartons.id', 'ASC')
                            ->get();

//                         echo '<pre>';
//                         print_r($upcQuery);
//                         exit();

                        if ($upcQuery) {

                            foreach ($upcQuery as $upcVal) {

                                //echo $upcVal->barcode.'<br>';
                                if ($upcVal->barcode == $barcode) {

                                    $data = [
                                        'upc_cartons_id' => $upcVal->id,
                                        'upc' => $upcVal->upc,
                                        'cartonID' => $upcVal->cartonID,
                                        'barcode' => $upcVal->barcode,
                                        'singles' => $upcVal->singles,
                                        'colourCode' => $upcVal->colour,
                                        'descriptions' => $upcVal->descriptions,
                                    ];

                                    $request->session()->put('readBarcode', $data);

                                    return response()->json([
                                        'status' => 'ok',
                                        'res' => $data
                                    ]);

                                } else {

                                    return response()->json([
                                        'status' => 'nok',
                                        'res' => false,
                                    ]);

                                }

                            }

                        }

                        return response()->json([
                            'status' => 'nok',
                            'res' => false,
                        ]);

                    }

                } catch (\Exception $e) {

                    session()->flash(
                        'flash_message',
                        array(trans('station.failed'), trans('station.error_text sad'), 'error')
                    );
                    return redirect()->back()->withInput();

                }
                break;

            case 'packagesQuery':

                try {

                    $packageId = $request->post('packageId');

                    $packages = \App\Package::where('id', $packageId)
                        ->whereNull('deleted_at')
                        ->orderBy('id', 'desc')
                        ->get();

                    // echo '<pre>';
                    // echo $packages[0]['barcode'].'<br>'.$packages[0]['package_no'].'<br>';
                    // print_r($packages);
                    // exit();

                    if (count($packages) > 0) {

                        //echo '<pre>';
                        //echo $packages->package_no.'<br>';
                        //print_r($packages);
                        return response()->json([
                            'status' => 'ok',
                            'barcode' => $packages[0]['barcode'],
                            'packageNo' => $packages[0]['package_no'],
                        ]);

                    }

                } catch (\Exception $e) {

                }
                break;

            case 'notification':

                $notifications = \App\Notification::where('company_id', auth()->user()->company_id)
                    ->whereMonth('created_at', '>', \Carbon\Carbon::now()->subMonth()->month)->orderBy('id', 'desc')
                    ->get();

                if ($notifications) {
                    $this->data['notifications'] = $notifications;
                }

                return response()->json(
                    ['status' => 'ok', 'html' => view('station.notification', $this->data)->render()]
                );

                break;

            case 'notificationCheck' :

                $notifications = \App\Notification::where('company_id', auth()->user()->company_id)
                    ->where('read_web', false)
                    ->count();

                return response()->json($notifications);

                break;

            case 'notificationStatus' :

                if ($request->has('notificationId')) {

                    $notification = \App\Notification::find($request->get('notificationId'));
                    if ($notification) {
                        $notification->read_web = $notification->read_web == true ? false : true;
                        $notification->updated_user_id = auth()->user()->id;
                        if ($notification->save()) {
                            return response()->json('ok');
                        }
                    }

                }

                return response()->json(['message' => 'notification Bulunamadı'], 400);
                break;

            case 'findPackages' :

                if ($request->has('consignmentId') && $request->has('epc')) {

                    foreach ($request->get('epc') as $key => $value) {

                        $item = \App\Item::select('package_id')
                            ->where('consignment_id', $request->get('consignmentId'))
                            ->where('company_id', auth()->user()->company_id)
                            ->where('epc', $value)
                            ->first();

                        if ($item) {

                            $package = \App\Package::withCount('items')->find($item->package_id);
                            return response()->json(['package' => $package]);

                        }

                    }

                    return response()->json('nonpackage');

                } else {
                    return response()->json(['message' => 'Eksik parametre'], 400);
                }
                break;

            case 'sendPackage':
                try {

                    set_time_limit(5);
                    if ($request->has('consignmentId') && $request->has('package') && $request->has('data')) {

                        $consignmentId = $request->get('consignmentId');
                        $packageNo = $request->get('package');
                        $epc_list = $request->get('data');
                        //$package_quentity = $request->get('package_quentity');
                        $orderId = $request->get('orderId');
                        $model = $request->get('model');
                        $size = $request->get('size');
                        $box_type_id = $request->get('box_type_id');
                        $load_type = $request->get('load_type');

                        //paket kontrol
                        $packages = \App\Package::where('consignment_id', $request->get('consignmentId'))
                            ->where('package_no', $packageNo)
                            ->count();

                        if ($packages) {

                            session()->flash(
                                'flash_message',
                                array(trans('station.failed'), trans('station.error_text'), 'error')
                            );
                            return response()->json(false, 400);

                        }

                        //epc kontrol
                        $items = \App\Item::where('consignment_id', $request->get('consignmentId'))
                            ->whereIn('epc', collect($epc_list)->pluck('epc'))
                            ->whereNull('deleted_at')
                            ->count();

                        if ($items) {
                            session()->flash(
                                'flash_message',
                                array(trans('station.failed'), trans('station.error_text'), 'error')
                            );
                            return response()->json(false, 400);
                        }

                        $package = new \App\Package;
                        $package->company_id = auth()->user()->company_id;
                        $package->consignment_id = $consignmentId;
                        $package->order_id = $orderId;
                        $package->package_no = $packageNo;
                        $package->model = $model;
                        $package->box_type_id = $box_type_id;
                        $package->load_type = $load_type;
                        $package->size = $size;
                        $package->device_id = session('device.id');
                        $package->created_user_id = auth()->user()->id;
                        $package->created_at = date('Y-m-d H:i:s');
                        if ($package->save()) {
                            $item_data = [];
                            foreach ($epc_list as $key => $value) {
                                $item_data[] = [
                                    'company_id' => auth()->user()->company_id,
                                    'order_id' => $orderId,
                                    'consignment_id' => $consignmentId,
                                    'package_id' => $package->id,
                                    'epc' => $value['epc'],
                                    'gtin' => $this->GetGTINFromEPC($value['epc']),
                                    'device_id' => session('device.id'),
                                    'created_at' => $package->created_at,
                                    'created_user_id' => auth()->user()->id
                                ];
                            }

                            \App\Item::insert($item_data);
                            return response()->json($package->id);
                        }

                    }

                    session()->flash('flash_message', array(trans('station.failed'), trans('station.error_text'), 'error'));
                    return response()->json(false, 400);

                } catch (\Exception $exception) {

                    session()->flash('flash_message', array(trans('station.failed'), trans('station.error_text'), 'error'));
                    return response()->json(false, 400);

                }
                break;

            case 'sendCarton' :

                try {

                    set_time_limit(5);
                    if ($request->has('consignmentId') && $request->has('data')) {

                        $consignmentId = $request->get('consignmentId');
                        $packageNo = $request->get('package');
                        $epcList = $request->get('data');
                        $orderId = $request->get('orderId');

                        //epc kontrol
//                        $items = \App\MsCartonEpc::where('consigment_id', $request->get('consignmentId'))
//                            ->whereIn('epc', collect($epcList)->pluck('epc'))
//                            ->whereNull('deleted_at')
//                            ->count();
//
//                        if ($items) {
//
//                            session()->flash(
//                                'flash_message',
//                                array(trans('station.failed'), trans('station.error_text'), 'error')
//                            );
//
//                            return response()->json(false, 400);
//
//                        }

                        $itemData = [];
                        foreach ($epcList as $key => $value) {

                            $gtin = $this->GetGTINFromEPC($value['epc']);
                            $upc = session('readBarcode.upc');
                            $gtin = substr($gtin, -strlen($upc));

                            $itemData = [
                                'consigment_id' => $consignmentId,
                                'barcode' => session('readBarcode.barcode'),
                                'epc' => $value['epc'],
                                'upc' => session('readBarcode.upc'),
                                'created_at' => date('Y-m-d H:i:s'),
                                'gittinCheck' => $gtin == $upc ? 1 : 0
                            ];

                            $msCartonSave = \App\MsCartonEpc::insert($itemData);

                        }

                        $totalQuery = \App\MsCartonEpc::where('consigment_id', $request->get('consignmentId'))
                            ->whereNull('deleted_at')
                            ->count();

                        $request->session()->put('totalQuantity', $totalQuery);

                        return response()->json($totalQuery);

                    }

                    session()->flash('flash_message', array(trans('station.failed'), trans('station.error_text'), 'error'));
                    return response()->json(false, 400);

                } catch (\Exception $exception) {

                    session()->flash('flash_message', array(trans('station.failed'), trans('station.error_text'), 'error'));
                    return response()->json(false, 400);

                }
                break;

            case 'sendCartonHanging' :

                try {

                    set_time_limit(5);
                    if ($request->has('consignmentId') && $request->has('data')) {

                        $consignmentId = $request->get('consignmentId');
                        $packageNo = $request->get('package');
                        $epcList = $request->get('data');
                        $orderId = $request->get('orderId');
                        $upcListArr = [];

                        $upcListQuery = DB::table('ms_upc_cartons')
                            ->where(['consignment_id' => $consignmentId])
                            ->get();

                        if (count($upcListQuery) > 0 ){
                            foreach ($upcListQuery as $upcList){

                                $upcListArr[] = $upcList->upc;
                                //echo $upcList->upc.'<br>';

                            }
                        }

                        $itemData = [];
                        foreach ($epcList as $key => $value) {

                            $upc = 0;

                            $gtin = $this->GetGTINFromEPC($value['epc']);
                            $gtinEpcLenght = substr($gtin, -8);
                            if($gtinEpcLenght[0] == 0){
                                $gtinEpcLenght = substr($gtin,-7);
                            }

                            foreach ($upcListArr as $upcList){

                                if ($upcList == $gtinEpcLenght){

                                    $upc = $gtinEpcLenght;

                                }

                            }
//                            if (in_array($gtinEpcLenght, $upcListArr)){
//                                $upc = $gtinEpcLenght;
//                            }

                            //echo $gtinEpcLenght.'_gtin<br>';
                            //$upc = session('readBarcode.upc');
                            //$gtin = substr($gtin, -strlen($upc));

                            $itemData = [
                                'consigment_id' => $consignmentId,
                                'barcode' => 0,
                                'epc' => $value['epc'],
                                'upc' => $upc,
                                'created_at' => date('Y-m-d H:i:s'),
                                'gittinCheck' => $gtinEpcLenght == $upc ? 1 : 0
                            ];

                            //echo '<pre>';
                            //print_r($itemData);

                            $msCartonSaveHanging = \App\MsCartonEpc::insert($itemData);

                        }

                        $totalQuery = \App\MsCartonEpc::where('consigment_id', $consignmentId)
                            ->whereNull('deleted_at')
                            ->count();

                        $request->session()->put('totalQuantity', $totalQuery);

//                        echo '<pre>';
//                        print_r($totalQuery);
//                        exit();

                        return response()->json($totalQuery);

                    }

                    session()->flash('flash_message', array(trans('station.failed'), trans('station.error_text'), 'error'));
                    return response()->json(false, 400);

                }catch (\Exception $exception) {

                    session()->flash('flash_message', array(trans('station.failed'), trans('station.error_text'), 'error'));
                    return response()->json(false, 400);

                }
                break;

            case 'MsTotalQuantity':

                try {

                    if ($request->has('consignmentId')) {

                        $consignmentId = $request->get('consignmentId');
                        $totalQuery = \App\MsCartonEpc::where('consigment_id', $request->get('consignmentId'))
                            ->whereNull('deleted_at')
                            ->count();

                        $request->session()->put('totalQuantity', $totalQuery);

                        return response()->json($totalQuery);

                    }

                } catch (\Exception $exception) {

                    session()->flash('flash_message', array(trans('station.failed'), trans('station.error_text'), 'error'));
                    return response()->json(false, 400);

                }
                break;

            case 'sendPackageHb':

                try {

                    set_time_limit(5);
                    if ($request->has('consignmentId') && $request->has('package') && $request->has('data')) {

                        $consignmentId = $request->get('consignmentId');
                        $packageNo = $request->get('package');
                        $epc_list = $request->get('data');
                        //$package_quentity = $request->get('package_quentity');
                        $orderId = $request->get('orderId');
                        $model = $request->get('model');
                        $size = $request->get('size');
                        $box_type_id = $request->get('box_type_id');
                        $load_type = $request->get('load_type');

                        //paket kontrol
                        $packages = \App\Package::where('consignment_id', $request->get('consignmentId'))
                            ->where('package_no', $packageNo)
                            ->count();

                        if ($packages) {

                            session()->flash(
                                'flash_message',
                                array(trans('station.failed'), trans('station.error_text'), 'error')
                            );
                            return response()->json(false, 400);

                        }

                        //epc kontrol
                        $items = \App\Item::where('consignment_id', $request->get('consignmentId'))
                            ->whereIn('epc', collect($epc_list)->pluck('epc'))
                            ->whereNull('deleted_at')
                            ->count();

                        if ($items) {
                            session()->flash(
                                'flash_message',
                                array(trans('station.failed'), trans('station.error_text'), 'error')
                            );
                            return response()->json(false, 400);
                        }

                        $package = new \App\Package;
                        $package->company_id = auth()->user()->company_id;
                        $package->consignment_id = $consignmentId;
                        $package->order_id = $orderId;
                        $package->package_no = $packageNo;
                        $package->model = $model;
                        $package->box_type_id = $box_type_id;
                        $package->load_type = $load_type;
                        $package->size = $size;
                        $package->device_id = session('device.id');
                        $package->created_user_id = auth()->user()->id;
                        $package->created_at = date('Y-m-d H:i:s');
                        if ($package->save()) {
                            $item_data = [];
                            foreach ($epc_list as $key => $value) {
                                $item_data[] = [
                                    'company_id' => auth()->user()->company_id,
                                    'order_id' => $orderId,
                                    'consignment_id' => $consignmentId,
                                    'package_id' => $package->id,
                                    'epc' => $value['epc'],
                                    'gtin' => $this->GetGTINFromEPCHb($value['epc']),
                                    'device_id' => session('device.id'),
                                    'created_at' => $package->created_at,
                                    'created_user_id' => auth()->user()->id
                                ];
                            }
                            \App\Item::insert($item_data);
                            return response()->json($package->id);
                        }
                    }

                    session()->flash('flash_message', array(trans('station.failed'), trans('station.error_text'), 'error'));
                    return response()->json(false, 400);

                } catch (\Exception $exception) {

                    session()->flash('flash_message', array(trans('station.failed'), trans('station.error_text'), 'error'));
                    return response()->json(false, 400);

                }
                break;

            case 'sendPackageTarget':

                try {

                    set_time_limit(5);
                    if ($request->has('consignmentId') && $request->has('package') && $request->has('data')) {

                        $consignmentId = $request->get('consignmentId');
                        $packageNo = $request->get('package');
                        $epc_list = $request->get('data');
                        //$package_quentity = $request->get('package_quentity');
                        $orderId = $request->get('orderId');
                        $model = $request->get('model');
                        $size = $request->get('size');
                        $box_type_id = $request->get('box_type_id');
                        $load_type = $request->get('load_type');

                        //paket kontrol
                        $packages = \App\Package::where('consignment_id', $request->get('consignmentId'))
                            ->where('package_no', $packageNo)
                            ->count();

                        if ($packages) {

                            session()->flash(
                                'flash_message',
                                array(trans('station.failed'), trans('station.error_text'), 'error')
                            );
                            return response()->json(false, 400);

                        }

                        //epc kontrol
                        $items = \App\Item::where('consignment_id', $request->get('consignmentId'))
                            ->whereIn('epc', collect($epc_list)->pluck('epc'))
                            ->whereNull('deleted_at')
                            ->count();

                        if ($items) {
                            session()->flash(
                                'flash_message',
                                array(trans('station.failed'), trans('station.error_text'), 'error')
                            );
                            return response()->json(false, 400);
                        }

                        $package = new \App\Package;
                        $package->company_id = auth()->user()->company_id;
                        $package->consignment_id = $consignmentId;
                        $package->order_id = $orderId;
                        $package->package_no = $packageNo;
                        $package->model = $model;
                        $package->box_type_id = $box_type_id;
                        $package->load_type = $load_type;
                        $package->size = $size;
                        $package->device_id = session('device.id');
                        $package->created_user_id = auth()->user()->id;
                        $package->created_at = date('Y-m-d H:i:s');
                        if ($package->save()) {
                            $item_data = [];
                            foreach ($epc_list as $key => $value) {
                                $item_data[] = [
                                    'company_id' => auth()->user()->company_id,
                                    'order_id' => $orderId,
                                    'consignment_id' => $consignmentId,
                                    'package_id' => $package->id,
                                    'epc' => $value['epc'],
                                    'gtin' => $this->GetGTINFromEPCTarget($value['epc']),
                                    'device_id' => session('device.id'),
                                    'created_at' => $package->created_at,
                                    'created_user_id' => auth()->user()->id
                                ];
                            }
                            \App\Item::insert($item_data);
                            return response()->json($package->id);
                        }
                    }

                    session()->flash('flash_message', array(trans('station.failed'), trans('station.error_text'), 'error'));
                    return response()->json(false, 400);

                } catch (\Exception $exception) {

                    session()->flash('flash_message', array(trans('station.failed'), trans('station.error_text'), 'error'));
                    return response()->json(false, 400);

                }
                break;

            case 'sendPackageLevis':

                try {

                    set_time_limit(5);
                    if ($request->has('consignmentId') && $request->has('package') && $request->has('data')) {

                        $consignmentId = $request->get('consignmentId');
                        $packageNo = $request->get('package');
                        $epc_list = $request->get('data');
                        //$package_quentity = $request->get('package_quentity');
                        $orderId = $request->get('orderId');
                        $model = $request->get('model');
                        $size = $request->get('size');
                        $box_type_id = $request->get('box_type_id');
                        $load_type = $request->get('load_type');

                        //paket kontrol
                        $packages = \App\Package::where('consignment_id', $request->get('consignmentId'))
                            ->where('package_no', $packageNo)
                            ->count();

                        if ($packages) {

                            session()->flash(
                                'flash_message',
                                array(trans('station.failed'), trans('station.error_text'), 'error')
                            );
                            return response()->json(false, 400);

                        }

                        //epc kontrol
                        $items = \App\Item::where('consignment_id', $request->get('consignmentId'))
                            ->whereIn('epc', collect($epc_list)->pluck('epc'))
                            ->whereNull('deleted_at')
                            ->count();

                        if ($items) {
                            session()->flash(
                                'flash_message',
                                array(trans('station.failed'), trans('station.error_text'), 'error')
                            );
                            return response()->json(false, 400);
                        }

                        $package = new \App\Package;
                        $package->company_id = auth()->user()->company_id;
                        $package->consignment_id = $consignmentId;
                        $package->order_id = $orderId;
                        $package->package_no = $packageNo;
                        $package->model = $model;
                        $package->box_type_id = $box_type_id;
                        $package->load_type = $load_type;
                        $package->size = $size;
                        $package->device_id = session('device.id');
                        $package->created_user_id = auth()->user()->id;
                        $package->created_at = date('Y-m-d H:i:s');
                        if ($package->save()) {
                            $item_data = [];
                            foreach ($epc_list as $key => $value) {
                                $item_data[] = [
                                    'company_id' => auth()->user()->company_id,
                                    'order_id' => $orderId,
                                    'consignment_id' => $consignmentId,
                                    'package_id' => $package->id,
                                    'epc' => $value['epc'],
                                    // 7 digit hesaplaması hb de yapılmıstı
                                    'gtin' => $this->GetGTINFromEPCHb($value['epc']),
                                    'device_id' => session('device.id'),
                                    'created_at' => $package->created_at,
                                    'created_user_id' => auth()->user()->id
                                ];
                            }
                            \App\Item::insert($item_data);
                            return response()->json($package->id);
                        }
                    }

                    session()->flash('flash_message', array(trans('station.failed'), trans('station.error_text'), 'error'));
                    return response()->json(false, 400);

                } catch (\Exception $exception) {

                    session()->flash('flash_message', array(trans('station.failed'), trans('station.error_text'), 'error'));
                    return response()->json(false, 400);

                }
                break;

            case 'getPackages':

                if ($request->has("consignmentId")) {

                    $packages = \App\Package::select('id', 'package_no', 'model', 'size', 'load_type', 'box_type_id')
                        ->where('consignment_id', $request->get('consignmentId'))->with(['items'])
                        ->orderBy('id', 'desc')
                        ->get();

                    if ($packages) {
                        return response()->json(['list' => $packages, 'ids' => $packages->pluck('id')]);
                    } else {
                        return response()->json('empty list');
                    }

                } else {

                    return response()->json(['message' => 'Sevkiyat Bulunamadı'], 400);

                }
                break;

            case 'getItems':

                if ($request->has('consignmentId')) {

                    $items = DB::table('items')
                        ->select('items.package_id', 'items.epc', 'items.created_at', 'packages.package_no')
                        ->join('packages', function ($join) {
                            $join->on('items.package_id', '=', 'packages.id');
                        })
                        ->whereIn('items.package_id', $request->get('ids'))
                        ->where('items.consignment_id', $request->get('consignmentId'))
                        ->whereNull('items.deleted_at')
                        ->get();

                    if ($items) {
                        return response()->json(['list' => $items]);
                    } else {
                        return response()->json('empty list');
                    }

                } else {

                    return response()->json(['message' => 'Sevkiyat Bulunamadı'], 400);

                }
                break;

            case 'deletePackages':

                if ($request->has('consignmentId') && $request->has('packages')) {

                    $consignmentId = $request->get('consignmentId');
                    $packageIds = $request->get('packages');

                    if (is_array($packageIds)) {

                        DB::beginTransaction();

                        try {

                            foreach ($packageIds as &$pi) {

                                $ids = explode('_', $pi);
                                $d = $ids[0];
                                $items_delete = \App\Item::where('package_id', $ids[0])->delete();
                                DB::commit();

                            }

                            foreach ($packageIds as &$pi) {

                                $ids = explode('_', $pi);
                                $d = $ids[0];
                                $itemCount = \App\Item::where('package_id', $ids[0])->count();
                                // if($itemCount == 0){
                                $packages_delete = \App\Package::where('id', $ids[0])->delete();
                                // }
                                DB::commit();

                            }
                            //
                            // $items_delete       = \App\Item::whereIn('package_id', $packageIds)->delete();

                            //paket no güncelleme başla
                            $packageList = \App\Package::select('id')->where('consignment_id', $consignmentId)->get();
                            $cases = [];
                            $ids = [];
                            $params = [];
                            foreach ($packageList as $key => $value) {

                                $cases[] = "WHEN {$value->id} then ?";
                                $params[] = $key + 1;
                                $ids[] = $value->id;

                            }

                            $ids = implode(',', $ids);
                            $cases = implode(' ', $cases);

                            if (!empty($ids)) {

                                DB::update("UPDATE packages SET `package_no` = CASE `id` {$cases} END WHERE `id` in ({$ids})", $params);

                            }
                            //paket no güncelleme bitir

                            DB::commit();

                        } catch (\Exception $ex) {

                            DB::rollback();
                            return response()->json(['error' => $ex->getMessage()], 500);

                        }

                    }

                    $packages = \App\Package::select('id', 'package_no', 'model', 'size')
                        ->where('consignment_id', $request->get('consignmentId'))
                        ->with(['items'])
                        ->orderBy('id', 'desc')
                        ->get();

                    if ($packages) {
                        return response()->json(['list' => $packages, 'ids' => $packages->pluck('id')]);
                    } else {
                        return response()->json('empty list');
                    }

                } else {
                    return response()->json(['message' => 'Sevkiyat Bulunamadı'], 400);
                }
                break;

            case 'combinePackages':

                if ($request->has('consignmentId') && $request->has('packages')) {

                    $consignmentId = $request->get('consignmentId');
                    $packageIds = $request->get('packages');

                    if (is_array($packageIds)) {

                        $firstPackageId = array_shift($packageIds);
                        $main_package = \App\Package::find($firstPackageId);

                        if ($main_package) {

                            DB::beginTransaction();
                            try {

                                $items_update = \App\Item::whereIn('package_id', $packageIds)->update(['package_id' => $firstPackageId]);
                                $packages_delete = \App\Package::whereIn('id', $packageIds)->delete();

                                //paket no güncelleme başla
                                $packageList = \App\Package::select('id')->where('consignment_id', $consignmentId)->get();
                                $cases = [];
                                $ids = [];
                                $params = [];

                                foreach ($packageList as $key => $value) {

                                    $cases[] = "WHEN {$value->id} then ?";
                                    $params[] = $key + 1;
                                    $ids[] = $value->id;

                                }

                                $ids = implode(',', $ids);
                                $cases = implode(' ', $cases);

                                if (!empty($ids)) {

                                    DB::update("UPDATE packages SET `package_no` = CASE `id` {$cases} END WHERE `id` in ({$ids})", $params);

                                }
                                //paket no güncelleme bitir
                                DB::commit();

                            } catch (\Exception $ex) {

                                DB::rollback();
                                return response()->json(['error' => $ex->getMessage()], 500);

                            }

                        }

                    }

                    $packages = \App\Package::select('id', 'package_no', 'model', 'size')
                        ->where('consignment_id', $request->get('consignmentId'))
                        ->withCount(['items'])
                        ->orderBy('id', 'desc')
                        ->get();

                    if ($packages) {
                        return response()->json(['list' => $packages, 'ids' => $packages->pluck('id')]);
                    } else {
                        return response()->json(['message' => 'Sevkiyat Bulunamadı'], 400);
                    }

                } else {

                    return response()->json(['message' => 'Sevkiyat Bulunamadı'], 400);

                }
                break;

            case 'typesEdit' :

                if ($request->has('packageId') && $request->has('box_type_id') && $request->has('load_type')) {

                    $boxType = $request->get('box_type_id');
                    $loadType = $request->get('load_type');
                    $package = \App\Package::where('id', $request->get('packageId'))->update([
                        'box_type_id' => $boxType,
                        'load_type' => $loadType
                    ]);

                    if ($package) {
                        return response()->json(['status' => 'ok', 'message' => strtoupper($boxType)]);
                    }

                    return response()->json(['message' => 'Başarısız'], 400);

                } else {

                    return response()->json(['message' => 'Eksik Veri'], 400);

                }
                break;

            case 'modelEdit' :

                if ($request->has('consignmentId') && $request->has('packageNo') && $request->has('model')) {

                    $model = $request->get('model') == '' ? '-' : $request->get('model');
                    $package = \App\Package::where('consignment_id', $request->get('consignmentId'))
                        ->where('package_no', $request->get('packageNo'))
                        ->update(['model' => $model]);

                    if ($package) {

                        return response()->json(['status' => 'ok', 'message' => $model]);

                    }

                    return response()->json(['message' => 'Başarısız'], 400);

                } else {

                    return response()->json(['message' => 'Eksik Veri'], 400);

                }
                break;

            case 'modelAllEdit' :

                if ($request->has('consignmentId') && $request->has('packages') && $request->has('model')) {

                    $model = $request->get('model') == '' ? '-' : $request->get('model');
                    $package = \App\Package::where('consignment_id', $request->get('consignmentId'))
                        ->whereIn('package_no', $request->get('packages'))
                        ->update(['model' => strtoupper($model)]);

                    if ($package) {
                        return response()->json(['status' => 'ok', 'message' => strtoupper($model)]);
                    }

                    return response()->json(['message' => 'Başarısız'], 400);

                } else {

                    return response()->json(['message' => 'Eksik Veri'], 400);

                }
                break;

            case 'sizeEdit' :

                if ($request->has('consignmentId') && $request->has('packageNo') && $request->has('size')) {

                    $size = $request->get('size') == '' ? '-' : $request->get('size');
                    $package = \App\Package::where('consignment_id', $request->get('consignmentId'))
                        ->where('package_no', $request->get('packageNo'))
                        ->update(['size' => strtoupper($size)]);

                    if ($package) {
                        return response()->json(['status' => 'ok', 'message' => strtoupper($size)]);
                    }

                    return response()->json(['message' => 'Başarısız'], 400);

                } else {

                    return response()->json(['message' => 'Eksik Veri'], 400);

                }
                break;

            case 'sizeAllEdit' :

                if ($request->has('consignmentId') && $request->has('packages') && $request->has('size')) {

                    $size = $request->get('size') == '' ? '-' : $request->get('size');
                    $package = \App\Package::where('consignment_id', $request->get('consignmentId'))
                        ->whereIn('package_no', $request->get('packages'))
                        ->update(['size' => strtoupper($size)]);

                    if ($package) {
                        return response()->json(['status' => 'ok', 'message' => strtoupper($size)]);
                    }

                    return response()->json(['message' => 'Başarısız'], 400);

                } else {

                    return response()->json(['message' => 'Eksik Veri'], 400);

                }
                break;

            case 'deviceChangeAddress' :

                if ($request->has('ip') && $request->has('id')) {

                    $device = \App\Device::find($request->get('id'));
                    $device->ip_address = $request->get('ip');

                    if ($device->save()) {

                        session()->put('device', $device);
                        return response()->json(['status' => 'ok', 'message' => $request->get('ip')]);

                    }

                    return response()->json(['message' => 'Başarısız'], 400);

                } else {

                    return response()->json(['message' => 'Eksik Veri'], 400);

                }
                break;

            case 'settings':

                $device = \App\Device::with('readType')->find(session('device.id'));
                if ($device) {
                    $this->data['device'] = $device;
                } else {
                    session()->flash(
                        'flash_message',
                        array(trans('station.device_not_found'), trans('station.device_not_found'), 'error')
                    );
                    return redirect()->back();
                }

                $this->data['read_types'] = \App\ReadType::all()->where('status', 1);
                // echo '<pre>';
                // print_r($this->data);
                // exit();
                return response()->json(['status' => 'ok', 'html' => view('station.setting', $this->data)->render()]);
                break;

            case 'setSetting' :

                try {

                    $attribute = array(
                        'read_type_id' => trans('station.read_mode'),
                        'reader' => trans('station.rfid_reader'),
                        'device_ip' => trans('station.device_address'),
                        'package_timeout' => trans('station.package_timeout'),
                        'common_power' => trans('station.common_power'),
                        'reader_mode' => 'Reader Mode',
                        'estimated_population' => 'Est. Population',
                        'search_mode' => 'Search Mode',
                        'session' => 'Session',
                        'barcode_ip_address' => 'barcode_ip_address',
                        'barcode_status' => 'barcode_status',
                        'bridgeCloseTime' => 'bridgeCloseTime',
                    );

                    $rules = array(
                        'read_type_id' => 'required|numeric',
                        'reader' => 'required',
                        'device_ip' => 'required',
                        'package_timeout' => 'required',
                        'common_power' => 'nullable',
                        'reader_mode' => 'nullable',
                        'estimated_population' => 'nullable|numeric',
                        'search_mode' => 'nullable',
                        'session' => 'nullable|numeric',
                        'barcode_ip_address' => 'nullable',
                        'barcode_status' => 'required',
                        'bridgeCloseTime' => 'nullable',
                    );

                    $validator = Validator::make($request->all(), $rules);
                    $validator->setAttributeNames($attribute);
                    if ($validator->fails()) {
                        return response()->json([
                            'status' => false,
                            'errors' => $validator->getMessageBag()->toArray()
                        ]);
                    }

                    $device = \App\Device::find(session('device.id'));
                    $device->reader = $request->get('reader');
                    $device->reader_mode = $request->get('reader_mode');
                    $device->estimated_population = $request->get('estimated_population');
                    $device->search_mode = $request->get('search_mode');
                    $device->session = $request->get('session');
                    $device->string_set = $request->get('string_set');
                    $device->ip_address = $request->get('device_ip');
                    $device->package_timeout = $request->get('package_timeout');
                    $device->common_power = $request->has('common_power') ? true : false;
                    if ($request->has('common_power')) {

                        $device->antennas = json_encode(
                            ['read' => $request->get('antenna'), 'write' => $request->get('antenna')]
                        );

                    } else {

                        $antennas = [];
                        if ($request->has('antennasStatus')) {

                            foreach ($request->get('antennasStatus') as $key => $value) {
                                if (isset($request->get('antennas')[$key])) {
                                    $antennas[$key] = [
                                        'read' => $request->get('antennas')[$key],
                                        'write' => $request->get('antennas')[$key],
                                    ];
                                }
                            }

                        }

                        $device->antennas = json_encode($antennas);

                    }

                    $device->read_type_id = $request->get('read_type_id');
                    $device->auto_print = $request->has('auto_print') ? true : false;
                    $device->auto_model_name = $request->has('auto_model_name') ? true : false;
                    $device->auto_size_name = $request->has('auto_size_name') ? true : false;
                    $device->auto_size_name = $request->has('auto_size_name') ? true : false;
                    $device->barcode_ip_address = $request->get('barcode_ip_address');
                    $device->barcode_status = $request->get('barcode_status');
                    $device->bridgeCloseTime = $request->get('bridgeCloseTime');
                    $device->updated_user_id = auth()->user()->id;
                    $device->save();

                    $device = \App\Device::with('readType')->find($device->id);
                    session()->put('device', $device);
                    //session()->flash('flash_message', array(trans('station.successful'),trans('station.settings_update'), 'success'));
                    return response()->json(['status' => 'ok']);

                } catch (\Exception $e) {
                    //session()->flash('flash_message', array(trans('station.failed'),trans('station.error_text'), 'error'));
                    return response()->json(['status' => false, 'message' => trans('station.error_text')]);

                }
                break;

            case 'getTotalCountBar':

                $consignments = \App\Consignment::where('status', true)
                    ->where('company_id', auth()->user()->company_id)
                    ->where('id', $request->get('consigment_id'))
                    ->with(['consignee', 'order'])
                    ->orderBy('id', 'desc')
                    ->get();

                $totalBoxCount = DB::select(
                    "select count(*) as countCarton from
                                   ms_cartons
                               where consignment_id = :consignment_id and
                                     upc in (
                                            select upc from
                                                           ms_upc_cartons
                                                       where
                                                           consignment_id = :consignment_id2
                                     )",
                    array(
                        'consignment_id' => $request->get('consigment_id'),
                        'consignment_id2' => $request->get('consigment_id')
                    )
                );

                if ($consignments[0]['hanging_product'] == 1 ){

                    $totalCount = DB::select(
                        "select count(*) as count from
                             ms_carton_epcs
                               where consigment_id = :consignment_id
                                     and gittinCheck = 0",
                        array(
                            'consignment_id' => $request->get('consigment_id')
                        )
                    );

                }else{

                    $totalCount = DB::select(
                        "select count(*) as count from
                             ms_carton_epcs
                               where consigment_id = :consignment_id and
                                    barcode in (
                                                select barcode from
                                                                ms_cartons
                                                            where
                                                                consignment_id = :consignment_id2 and
                                        upc in (
                                                    select upc from
                                                                    ms_upc_cartons
                                                                where
                                                                    consignment_id = :consignment_id3
                                        )
                                    ) and gittinCheck = 0",
                        array(
                            'consignment_id' => $request->get('consigment_id'),
                            'consignment_id2' => $request->get('consigment_id'),
                            'consignment_id3' => $request->get('consigment_id')
                        )
                    );

                }

                $totalReadBoxCountQuery = DB::select(
                    "select count(*) as readCartonCount, upc from
                                            ms_cartons msc
                                        where
                                            msc.barcode in
                                                (select barcode from ms_carton_epcs) and
                                                consignment_id = :consignment_id
                                        GROUP BY upc",
                    array(
                        'consignment_id' => $request->get('consigment_id')
                    )
                );

                $totalReadBoxCount = 0;
                foreach ($totalReadBoxCountQuery as $value){
                    $totalReadBoxCount = $totalReadBoxCount + $value->readCartonCount;
                }

                return response()->json(
                    [
                        'status' => true,
                        'carton' => $totalBoxCount[0]->countCarton,
                        'invalidCount' => $totalCount[0]->count,
                        'totalReadBoxCount' => $totalReadBoxCount
                    ]
                );

        }

    }

    function get_absolute_path($path)
    {

        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();

        foreach ($parts as $part) {
            if ('.' == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }

        return implode(DIRECTORY_SEPARATOR, $absolutes);

    }

    public function getUpcListForDatatableFromMs(Request $request)
    {
        $method = $request->method();

        if ($request->isMethod('post')) {

            $model = DB::table('ms_upc_cartons')->where(['consignment_id' => $request->get('consignmentId')])->get();

            $consignments = \App\Consignment::where('status', true)
                ->where('company_id', auth()->user()->company_id)
                ->where('id', $request->get('consignmentId'))
                ->with(['consignee', 'order'])
                ->orderBy('id', 'desc')
                ->get();
//            echo '<pre>';
//            print_r($consignments);
//            exit();
            $sessTotal = 0;
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
                if ($consignments[0]['hanging_product'] == 1 ){

                    $results = DB::select(DB::raw(
                        "select upc,cartonID,consignment_id,series,colour,singles,barcode,
                            (
                                select count(*) from
                                                    ms_carton_epcs mce
                                                where mce.upc = mc.upc and
                                                      gittinCheck <> 0 and
                                                      consigment_id = $item->consignment_id
                            ) as counted,
                            (
                                select count(*) from
                                                    ms_carton_epcs mce
                                                where mce.barcode = mc.barcode and
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
                                            where mc.consignment_id = :consignment_id and
                                                  mc.upc = :upc"
                    ),
                        array('upc' => $item->upc,'consignment_id' => $item->consignment_id)
                    );

                }else{

                    $results = DB::select(DB::raw(
                        "select upc,cartonID,consignment_id,series,colour,singles,barcode,
                            (
                                select count(*) from
                                                    ms_carton_epcs mce
                                                where mce.barcode = mc.barcode and
                                                      gittinCheck <> 0 and
                                                      consigment_id = $item->consignment_id
                            ) as counted,
                            (
                                select count(*) from
                                                    ms_carton_epcs mce
                                                where mce.barcode = mc.barcode and
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
                                            where mc.consignment_id = :consignment_id and
                                                  mc.upc = :upc"
                    ),
                        array('upc' => $item->upc,'consignment_id' => $item->consignment_id)
                    );

                }

//                echo '<pre>';
//                print_r($results);
//                exit();

                $cssBolean = false;
                foreach ($results as $cssItem) {
                    if ($cssItem->CssClass == 'bg-danger text-white')
                        $cssBolean = true;
                }

                $sessTotal = $sessTotal + $totalCounted;

                $data['data'][] = [
                    'UPC' => $item->upc,
                    'SIZE' => $item->size,
                    'targetCount' => $totalCount->sum,
                    'counted' => $totalCounted - $UndCounted,
                    'undcounted' => $UndCounted,
                    'boxes' => json_encode($results),
                    'description' => $item->descriptions,
                    'baseRowClass' => $cssBolean ? 'bg-danger' : '',
                    'baseTextClass' => $cssBolean ? 'text-white' : '',
                    'hanging' => $consignments[0]['hanging_product'],
                ];

                /*if ($consignments[0]['hanging_product'] == 1 ){

                    $data['data'][] = [
                        'UPC' => $item->upc,
                        'SIZE' => $item->size,
                        //'targetCount' => $totalCount->sum,
                        'counted' => $totalCounted - $UndCounted,
                        'undcounted' => $UndCounted,
                        'boxes' => json_encode($results),
                        'description' => $item->descriptions,
                        'baseRowClass' => $cssBolean ? 'bg-danger' : '',
                        'baseTextClass' => $cssBolean ? 'text-white' : '',
                        'ahmet' => $consignments[0]['hanging_product']
                    ];

                }else{

                    $data['data'][] = [
                        'UPC' => $item->upc,
                        'SIZE' => $item->size,
                        'targetCount' => $totalCount->sum,
                        'counted' => $totalCounted - $UndCounted,
                        'undcounted' => $UndCounted,
                        'boxes' => json_encode($results),
                        'description' => $item->descriptions,
                        'baseRowClass' => $cssBolean ? 'bg-danger' : '',
                        'baseTextClass' => $cssBolean ? 'text-white' : '',
                        'ahmet' => true
                    ];

                }*/
            }

//            echo '<pre>';
//            print_r($data);
//            exit();

            return json_encode($data);
        }
    }

    public function setEpcMs(Request $request)
    {
        $method = $request->method();
        $resault = false;
        if ($request->isMethod('post')) {
            $postEPC = json_decode($request->get('data'), true);
            foreach ($postEPC as $post) {
                $model = new CartoonIntoEpcs();
                $model->consigment_id = $post['consigment'];
                $model->barcode = $post['barcode'];
                $model->barcode = $post['epc'];
                $model->save() == true ? $resault = true : $resault = false;
            }
        }

        if ($resault) {
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }

    }

    /**
     * MS için Daha önce okunan epc tablodan kaldırır,
     * Böylece karton tekrar okutmaya hazırlanır
     * @param Request $request
     * @return void
     */
    public function reReadCarton(Request $request)
    {
        $method = $request->method();
        if ($request->isMethod('post')) {

            try {

                $consignments = \App\Consignment::where('status', true)
                    ->where('company_id', auth()->user()->company_id)
                    ->where('id', $request->get('consigment_id'))
                    ->with(['consignee', 'order'])
                    ->orderBy('id', 'desc')
                    ->get();

                if ($consignments[0]['hanging_product'] == 1 ){

                    $model = DB::delete("delete from ms_carton_epcs
                            where consigment_id = ? and
                                  upc = ?",
                    [
                        $request->get('consigment_id'),
                        $request->get('upc')
                    ]);

                }else{

                    $model = DB::delete("delete from ms_carton_epcs
                                where consigment_id = ? and
                                      upc = ? and
                                      barcode = ?",
                    [
                        $request->get('consigment_id'),
                        $request->get('upc'),
                        $request->get('barcode'),
                    ]);

                }

                if ($model) {

                    $totalQuery = \App\MsCartonEpc::where('consigment_id', $request->get('consigment_id'))
                        ->whereNull('deleted_at')
                        ->count();

                    $request->session()->put('totalQuantity', $totalQuery);

                    return json_encode(['status' => 200, 'msg' => trans('station.successful')]);

                } else {

                    return json_encode(['status' => 404, 'msg' => trans('error_text.successful').' - ' . $request->get('consigment_id')]);

                }

            } catch (\Illuminate\Database\QueryException $ex) {

                return json_encode(['status' => 404, 'msg' => $ex->getMessage()]);// Note any method of class PDOException can be called on $ex.

            }


        }

    }

}
