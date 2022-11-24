<?php

namespace App\Http\Controllers;

use App\Company;

use App\Consignment;
use App\Exports\exportMSreport;
use App\Helpers\OptionTrait;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


//use Barryvdh\DomPDF\Facade as PDF;

class ReportsController extends Controller
{
    use OptionTrait;
    /**
     * Kaynaktan bir liste görüntüler.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company_id = auth()->user()->company_id;


        //üretici
        if($this->roleCheck(config('settings.roles.uretici'))){

            $consignments = Consignment::orderBy('created_at')
            ->where("company_id", '=', $company_id)
            ->where("created_at", ">", \Carbon\Carbon::now()->subYear())
            ->get()
            ->groupBy(function ($val) {
                return \Carbon\Carbon::parse($val->created_at)->translatedFormat('F');
            });
            if ($consignments) {
                $this->data['consignments'] = $consignments;
            }
            $maxConsignee = Consignment::with(['consignee'])
            ->where("company_id", '=', $company_id)
            ->where('consignee_id', '!=', 0)
            ->select('consignee_id', \Illuminate\Support\Facades\DB::raw('count(consignee_id) as total'))
            ->groupBy('consignee_id')
            ->orderBy('total', 'desc')
            ->limit(3)
            ->get();

            if ($maxConsignee) {
                $this->data['maxConsignee'] = $maxConsignee;
            }

            $companies = \App\Company::withCount('consignments as consignment_total')
                ->where("id", '=', $company_id)
                ->orderBy('consignment_total', 'desc')
                ->get();

            if ($companies) {
                $this->data['companies'] = $companies;
            }
        }
        //anaUretici
        elseif($this->roleCheck(config('settings.roles.anaUretici')))
        {
            $consignments = Consignment::join('companies', function ($join) {
                $join->on('consignments.company_id', '=', 'companies.id');
            })->select('consignments.*')
            ->where(function ($query) {
                $query->where("consignments.created_at", ">", \Carbon\Carbon::now()->subYear());
            })
            ->where(function ($query) {
                $query->where("companies.status", "=", 1);
            })
            ->where(function ($query) use ($company_id) {
                $query->where('companies.main_company_id','=',$company_id);
            })->get()
            ->groupBy(function ($val) {
                return \Carbon\Carbon::parse($val->created_at)->translatedFormat('F');
            });

            if ($consignments) {
                $this->data['consignments'] = $consignments;
            }

            $maxConsignee = Consignment::join('companies', function ($join) {
                $join->on('consignments.company_id', '=', 'companies.id');
            })->select('consignments.*')
            ->where(function ($query) {
                $query->where("consignments.consignee_id", "!=", 0);
            })
            ->where(function ($query) {
                $query->where("companies.status", "=", 1);
            })
            ->where(function ($query) use ($company_id) {
                $query->where('companies.main_company_id','=',$company_id);
            })
            ->select('consignee_id', \Illuminate\Support\Facades\DB::raw('count(consignee_id) as total'))
            ->groupBy('consignee_id')
            ->orderBy('total', 'desc')
            ->limit(3)
            ->get();

            if ($maxConsignee) {
                $this->data['maxConsignee'] = $maxConsignee;
            }

            $companies = \App\Company::withCount('consignments as consignment_total')
            ->where(function ($query) {
                $query->where("status", "=", 1);
            })
            ->where(function ($query) use ($company_id) {
                $query->where('main_company_id','=',$company_id);
            })
                ->orderBy('consignment_total', 'desc')
                ->get();

            if ($companies) {
                $this->data['companies'] = $companies;
            }
        }
        //partner
        elseif($this->roleCheck(config('settings.roles.partner')))
        {   
            $consignments = Consignment::orderBy('created_at')->join('companies', function ($join) {
                $join->on('consignments.company_id', '=', 'companies.id');
            })->select('consignments.*')
            ->where(function ($query) {
                $query->where("consignments.created_at", ">", \Carbon\Carbon::now()->subYear());
            })
            ->where(function ($query) {
                $query->where("companies.created_user_id", "=", auth()->user()->id);
            })
            ->where(function ($query) {
                $query->where("companies.status", "=", 1);
            })
            ->get()
            ->groupBy(function ($val) {
                return \Carbon\Carbon::parse($val->created_at)->translatedFormat('F');
            });

            if ($consignments) {
                $this->data['consignments'] = $consignments;
            }

            $maxConsignee = Consignment::join('companies', function ($join) {
                $join->on('consignments.company_id', '=', 'companies.id');
            })->select('consignments.*')
            ->where(function ($query) {
                $query->where("consignee_id", "!=", 0);
            })
            ->where(function ($query) {
                $query->where("companies.status", "=", 1);
            })
            ->where(function ($query) {
                $query->where("companies.created_user_id", "=", auth()->user()->id);
            })
            ->select('consignee_id', \Illuminate\Support\Facades\DB::raw('count(consignee_id) as total'))
            ->groupBy('consignee_id')
            ->orderBy('total', 'desc')
            ->limit(3)
            ->get();

            if ($maxConsignee) {
                $this->data['maxConsignee'] = $maxConsignee;
            }

            $companies = \App\Company::withCount('consignments as consignment_total')
            ->where(function ($query) {
                $query->where("status", "=", 1);
            })
            ->where(function ($query) {
                $query->where("created_user_id", "=", auth()->user()->id);
            })
            ->where(function ($query) {
                $query->where("main_company_id", ">", 0);
            })
                ->orderBy('consignment_total', 'desc')
                ->get();

            if ($companies) {
                $this->data['companies'] = $companies;
            }
        }
        //admin
        if($this->roleCheck(config('settings.roles.admin'))){

            $consignments = Consignment::orderBy('created_at')
            ->where("created_at", ">", \Carbon\Carbon::now()->subYear())
            ->get()
            ->groupBy(function ($val) {
                return \Carbon\Carbon::parse($val->created_at)->translatedFormat('F');
            });
            if ($consignments) {
                $this->data['consignments'] = $consignments;
            }
            $maxConsignee = Consignment::with(['consignee'])
            ->where('consignee_id', '!=', 0)
            ->select('consignee_id', \Illuminate\Support\Facades\DB::raw('count(consignee_id) as total'))
            ->groupBy('consignee_id')
            ->orderBy('total', 'desc')
            ->limit(3)
            ->get();

            if ($maxConsignee) {
                $this->data['maxConsignee'] = $maxConsignee;
            }

            //sadece fason firmalar sevkiyat yapabilir. O yüzden buraya ->where("main_company_id", '>', 0) koşulu eklendi.
            $companies = \App\Company::withCount('consignments as consignment_total')
                ->where("main_company_id", '>', 0)
                ->orderBy('consignment_total', 'desc')
                ->get();

            if ($companies) {
                $this->data['companies'] = $companies;
            }
        }
        $companyInfo = Company::where('id',  auth()->user()->company_id)->first();
        $this->data['company_name'] = $companyInfo->name;
        return view('reports.index', $this->data);
    }
    //default package pdf
    public function exportPackagePdf($consignmentId)
    {

        header('Content-Type: text/html; charset=utf-8');
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');

        $consignment = \App\Consignment::with(['company', 'packages', 'packages.items'])
            ->withCount(['items', 'packages'])
            ->find($consignmentId);

        $data = [
            'title' => $consignment->name . '-' . trans('portal.package_list') . '-' . date('YmdHis'),
            'consignment' => $consignment
        ];

        //return view('reports.pdf.package', $data);
        $pdf = \PDF::loadView('reports.pdf.package', $data);

        return $pdf->download($data['title'] . '.pdf');

    }
    //ms package
    public function exportPackageMsPdf($consignmentId)
    {

        header('Content-Type: text/html; charset=utf-8');
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');

        $poNumber = DB::table('company_ms_dbs')->where(['consignment_id' => $consignmentId])->first();
        $fixVal = DB::table('xml_file_repos')
            ->select('*')
            ->join('txt_file_repos', function ($join) {
                $join->on('txt_file_repos.xmlid', '=', 'xml_file_repos.id');
            })
            ->where('xml_file_repos.poNumber', $poNumber->order)
            ->orderBy('xml_file_repos.id', 'ASC')
            ->get();

        $colourName = $fixVal[0]->colourCode . ' - ' . $fixVal[0]->colourDesc;

        $columnName = DB::table('ms_upc_cartons')->where(['consignment_id' => $consignmentId])
            ->select("upc", DB::raw("CONCAT(upc,'<br>',size) AS columName"))->get();
        $arrayTable = [];
        $arrayTh = ["portal.cartons"];
        foreach ($columnName as $column) {
            array_push($arrayTh, $column->columName . '<br>' . $colourName);
        }
        array_push($arrayTh, 'station.target_qty', 'station.invalidQuantity', 'portal.cartons', 'portal.total');
        array_push($arrayTh,);
        $arrayTable ['th'] = $arrayTh;

        foreach ($columnName as $column) {
            $row = [];
            $model = DB::table('ms_cartons')
                ->select(DB::raw("group_concat(DISTINCT cartonID) as carton "))
                ->where(['consignment_id' => $consignmentId])
                ->where(['upc' => $column->upc])
                ->groupBy('upc')
                ->first();
            $row['carton'] = $model->carton;

            $sumofTargetQty = DB::table('ms_cartons')
                ->select(DB::raw("case when sum(singles)>0 then sum(singles) else '0'  end as sumofCount"))
                ->where(['consignment_id' => $consignmentId])
                ->where(['upc' => $column->upc])
                ->whereIn('cartonID', explode(',', $model->carton))
                ->first();
            $row['picies'] = (int)$sumofTargetQty->sumofCount;

            $countofCartons = DB::table('ms_cartons')
                ->select(DB::raw("count(*) as count"))
                ->where(['consignment_id' => $consignmentId])
                ->where(['upc' => $column->upc])
                ->whereIn('cartonID', explode(',', $model->carton))
                ->first();
            $row['countofCartons'] = (int)$countofCartons->count;

            $rowTotal = 0;
            $rowInvalid = 0;
            foreach ($columnName as $column) {
                $countModel = DB::table('ms_carton_epcs')
                    ->select(DB::raw('count(*) as count'))
                    ->where([
                        'consignment_id' => $consignmentId,
                        'upc' => $column->columName,
                        'gittinCheck' => 1
                    ])->whereIn('barcode',
                        DB::table('ms_cartons')->select('barcode')
                            ->where(['consignment_id' => $consignmentId])
                            ->where(['upc' => $column->upc])
                            ->whereIn('cartonID', explode(',', $model->carton))
                    )->first();
                $countInvalidModel = DB::table('ms_carton_epcs')
                    ->select(DB::raw('count(*) as count'))
                    ->where([
                        'consignment_id' => $consignmentId,
                        'upc' => $column->columName,
                        'gittinCheck' => 0
                    ])->whereIn('barcode',
                        DB::table('ms_cartons')->select('barcode')
                            ->where(['consignment_id' => $consignmentId])
                            ->where(['upc' => $column->upc])
                            ->whereIn('cartonID', explode(',', $model->carton))
                    )->first();

                $rowTotal += (int)$countModel->count;
                $rowInvalid += (int)$countInvalidModel->count;
                $row['epcs'][] = (int)$countModel->count;
            }

//            echo '<pre>';
//            print_r($rowInvalid);
//            exit();

            $row['countofCartons'] = (int)$countofCartons->count;
            $row['rowTotal'] = $rowTotal + $rowInvalid;
            $row['inValidTotal'] = $rowInvalid;

            $arrayTable[] = $row;
        }
//        echo "<pre>";
//        print_r($arrayTable);
//        echo "</pre>";
//        exit();


        $data = [
            'title' => '-' . trans('portal.package_list') . '-' . date('YmdHis'),
            'consignment' => null,
            'consignmentExtra' => null,
            'tableData' => $arrayTable,
            'fixVal' => $fixVal
        ];

//         return view('reports.pdf.packageMs', $data);
//         exit();

        $pdf = \PDF::loadView('reports.pdf.packageMs', $data);

       return $pdf->download($data['title'] . '.pdf');

    }

    public function exportPackageMsExcel($consignmentId){

        return Excel::download(new exportMSreport($consignmentId), $consignmentId.'-PackageReport.xlsx');

    }

    public function exportHNMPdf($consignmentId)
    {

        header('Content-Type: text/html; charset=utf-8');
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');

        $consignment = \App\Consignment::with(['company', 'packages', 'packages.items'])
            ->withCount(['items', 'packages'])
            ->find($consignmentId);

        // dd($consignment);
        // exit();
        // $prods = \App\CompanyDb::where('consignment_id',  $consignment->id)->get();
        $artNos = 0;
        $sdsCodes = 0;
        $sizes = [];
        $assrSizes = [];
        $renderedGtins = [];
        $loadTypes = [];
        $packageLoadSizes = [];
        $packageTypeSizes = [];
        $packageTypes = [];
        $boxTypes = [];
        $boxTypes = \App\BoxType::get();
        $totalCount = 0;
        $packageCount = count($consignment->packages);
        foreach ($consignment->packages as $key => $package) {
            //echo $key.' = '.$package.'<br>';
            $totalCount = $totalCount + count($package->items);
            if (isset($loadTypes[$package->load_type]) == false) {
                $loadTypes[$package->load_type] = count($package->items);
            } else {
                $loadTypes[$package->load_type] = $loadTypes[$package->load_type] + count($package->items);
            }

            if ($key === 0)
                $packingMode = $package->load_type;

            if (isset($packageTypes[$package->box_type_id]) == false) {
                $packageTypes[$package->box_type_id] = 1;
            } else {
                $packageTypes[$package->box_type_id] = $packageTypes[$package->box_type_id] + 1;
            }

            foreach ($package->items as $key => $item) {

                $d = \App\CompanyDb::where('gtin', $item->gtin)->where('consignment_id', $consignmentId)->first();

                if (is_null($d) == false) {
                    array_push($item->itemDetails, $d);
                }
                // echo $d->product;
                // exit();
                if ($key === 0) {
                    if ($d) {
                        $productId = $d->product;
                    } else {
                        $productId = 0;
                    }
                }

                if ($artNos == 0 && isset($item->itemDetails[0])) {
                    $artNos = $item->itemDetails[0]->article_number;
                    $sdsCodes = $item->itemDetails[0]->sds_code;
                }

                if (in_array($item->gtin, $renderedGtins) == false) {

                    $size = "";

                    if (isset($item->itemDetails[0])) {

                        $size = preg_replace('/\s+/', '', $item->itemDetails[0]->sds_code);

                        if ($package->load_type == "Assortment") {
                            if (isset($assrSizes[$size])) {
                                $assrSizes[$size] = $assrSizes[$size] + getItemCount($consignment->packages, $item->gtin);
                            } else {
                                $assrSizes[$size] = getItemCount($consignment->packages, $item->gtin);
                            }
                        }

                        if (isset($sizes[$size])) {
                            $sizes[$size] = $sizes[$size] + getItemCount($consignment->packages, $item->gtin);
                        } else {
                            $sizes[$size] = getItemCount($consignment->packages, $item->gtin);
                        }

                        if (isset($packageLoadSizes[$package->load_type . "-" . $size]) == false) {
                            $packageLoadSizes[$package->load_type . "-" . $size] =
                                getItemCount($consignment->packages, $item->gtin);
                        } else {
                            $packageLoadSizes[$package->load_type . "-" . $size] =
                                $packageLoadSizes[$package->load_type . "-" . $size] +
                                getItemCount($consignment->packages, $item->gtin);
                        }
                        // $packageTypeSizes
                        if (isset($packageTypeSizes[$package->box_type_id . "-" . $size]) == false) {
                            $packageTypeSizes[$package->box_type_id . "-" . $size] =
                                getPackageItemCount($package, $item->gtin);
                        } else {
                            $packageTypeSizes[$package->box_type_id . "-" . $size] =
                                $packageTypeSizes[$package->box_type_id . "-" . $size] +
                                getPackageItemCount($package, $item->gtin);
                        }

                    } else {

                        if ($package->load_type == "Assortment") {
                            if (isset($assrSizes["UND"])) {
                                $assrSizes["UND"] =
                                    $assrSizes["UND"] + getItemCount($consignment->packages, $item->gtin);
                            } else {
                                $assrSizes["UND"] = getItemCount($consignment->packages, $item->gtin);
                            }
                        }

                        if (isset($sizes["UND"])) {
                            $sizes["UND"] = $sizes["UND"] + getItemCount($consignment->packages, $item->gtin);
                        } else {
                            $sizes["UND"] = getItemCount($consignment->packages, $item->gtin);
                        }

                        if (isset($packageLoadSizes[$package->load_type . "-UND"]) == false) {
                            $packageLoadSizes[$package->load_type . "-UND"] =
                                getItemCount($consignment->packages, $item->gtin);
                        } else {
                            $packageLoadSizes[$package->load_type . "-UND"] =
                                $packageLoadSizes[$package->load_type . "-UND"] +
                                getItemCount($consignment->packages, $item->gtin);
                        }

                        if (isset($packageTypeSizes[$package->box_type_id . "-UND"]) == false) {
                            $packageTypeSizes[$package->box_type_id . "-UND"] = getPackageItemCount($package, $item->gtin);
                        } else {
                            $packageTypeSizes[$package->box_type_id . "-UND"] =
                                $packageTypeSizes[$package->box_type_id . "-UND"] +
                                getPackageItemCount($package, $item->gtin);
                        }

                    }

                    array_push($renderedGtins, $item->gtin);

                }
            }
        }

        // $loadingTypes = ["A"]
        $data = [
            'title' => $consignment->name . '-' . trans('portal.package_list') . '-' . date('YmdHis'),
            'consignment' => $consignment,
            'sizes' => $sizes,
            'artNo' => $artNos,
            'sdsCode' => $sdsCodes,
            'loadTypes' => $loadTypes,
            'productId' => $productId,
            'packageTypes' => $packageTypes,
            'packageCount' => $packageCount,
            'packingMode' => $packingMode,
            'packageLoadSizes' => $packageLoadSizes,
            'packageTypeSizes' => $packageTypeSizes,
            'boxTypes' => $boxTypes,
            'totalCount' => $totalCount,
            'assrSizes' => $assrSizes
        ];

        // echo '<pre>';
        // print_r($data);
        // exit();
        $pdf = \PDF::loadView('reports.pdf.hm', $data);
        return $pdf->download($data['title'] . '.pdf');

    }

    public function exportHNMNewPdf($consignmentId)
    {

        header('Content-Type: text/html; charset=utf-8');
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');

        $consignment = \App\Consignment::with(['company', 'packages', 'packages.items'])
            ->withCount(['items', 'packages'])
            ->find($consignmentId);
        // $prods = \App\CompanyDb::where('consignment_id',  $consignment->id)->get();
        $artNos = 0;
        $sdsCodes = 0;
        $sizes = [];
        $assrSizes = [];
        $renderedGtins = [];
        $loadTypes = [];
        $packageLoadSizes = [];
        $packageTypeSizes = [];
        $packageTypes = [];
        $boxTypes = [];
        $boxTypes = \App\BoxType::get();
        $totalCount = 0;
        $packageCount = count($consignment->packages);
        foreach ($consignment->packages as $key => $package) {

            $totalCount = $totalCount + count($package->items);
            if (isset($loadTypes[$package->load_type]) == false) {
                $loadTypes[$package->load_type] = count($package->items);
            } else {
                $loadTypes[$package->load_type] = $loadTypes[$package->load_type] + count($package->items);
            }

            if ($key === 0)
                $packingMode = $package->load_type;

            if (isset($packageTypes[$package->box_type_id]) == false) {
                $packageTypes[$package->box_type_id] = 1;
            } else {
                $packageTypes[$package->box_type_id] = $packageTypes[$package->box_type_id] + 1;
            }

            foreach ($package->items as $key => $item) {

                $d = \App\CompanyDb::where('gtin', $item->gtin)->where('consignment_id', $consignmentId)->first();

                if (is_null($d) == false) {
                    array_push($item->itemDetails, $d);
                }

                if ($key === 0)
                    $productId = $d->product;

                if ($artNos == 0 && isset($item->itemDetails[0])) {
                    $artNos = $item->itemDetails[0]->article_number;
                    $sdsCodes = $item->itemDetails[0]->sds_code;
                }

                if (in_array($item->gtin, $renderedGtins) == false) {

                    $size = "";

                    if (isset($item->itemDetails[0])) {

                        $size = preg_replace('/\s+/', '', $item->itemDetails[0]->sds_code);

                        if ($package->load_type == "Assortment") {
                            if (isset($assrSizes[$size])) {
                                $assrSizes[$size] = $assrSizes[$size] +
                                    getItemCount($consignment->packages, $item->gtin);
                            } else {
                                $assrSizes[$size] = getItemCount($consignment->packages, $item->gtin);
                            }
                        }

                        if (isset($sizes[$size])) {
                            $sizes[$size] = $sizes[$size] + getItemCount($consignment->packages, $item->gtin);
                        } else {
                            $sizes[$size] = getItemCount($consignment->packages, $item->gtin);
                        }

                        if (isset($packageLoadSizes[$package->load_type . "-" . $size]) == false) {
                            $packageLoadSizes[$package->load_type . "-" . $size] =
                                getItemCount($consignment->packages, $item->gtin);
                        } else {
                            $packageLoadSizes[$package->load_type . "-" . $size] =
                                $packageLoadSizes[$package->load_type . "-" . $size] +
                                getItemCount($consignment->packages, $item->gtin);
                        }

                        // $packageTypeSizes
                        if (isset($packageTypeSizes[$package->box_type_id . "-" . $size]) == false) {
                            $packageTypeSizes[$package->box_type_id . "-" . $size] =
                                getPackageItemCount($package, $item->gtin);
                        } else {
                            $packageTypeSizes[$package->box_type_id . "-" . $size] =
                                $packageTypeSizes[$package->box_type_id . "-" . $size] +
                                getPackageItemCount($package, $item->gtin);
                        }

                    } else {

                        if ($package->load_type == "Assortment") {
                            if (isset($assrSizes["UND"])) {
                                $assrSizes["UND"] = $assrSizes["UND"] +
                                    getItemCount($consignment->packages, $item->gtin);
                            } else {
                                $assrSizes["UND"] = getItemCount($consignment->packages, $item->gtin);
                            }
                        }

                        if (isset($sizes["UND"])) {
                            $sizes["UND"] = $sizes["UND"] + getItemCount($consignment->packages, $item->gtin);
                        } else {
                            $sizes["UND"] = getItemCount($consignment->packages, $item->gtin);
                        }

                        if (isset($packageLoadSizes[$package->load_type . "-UND"]) == false) {
                            $packageLoadSizes[$package->load_type . "-UND"] =
                                getItemCount($consignment->packages, $item->gtin);
                        } else {
                            $packageLoadSizes[$package->load_type . "-UND"] =
                                $packageLoadSizes[$package->load_type . "-UND"] +
                                getItemCount($consignment->packages, $item->gtin);
                        }

                        if (isset($packageTypeSizes[$package->box_type_id . "-UND"]) == false) {
                            $packageTypeSizes[$package->box_type_id . "-UND"] = getPackageItemCount($package, $item->gtin);
                        } else {
                            $packageTypeSizes[$package->box_type_id . "-UND"] =
                                $packageTypeSizes[$package->box_type_id . "-UND"] +
                                getPackageItemCount($package, $item->gtin);
                        }

                    }

                    array_push($renderedGtins, $item->gtin);

                }
            }
        }

        // dd("packageTypes", $packageTypes, "loadTypes", $loadTypes);
        $data = [
            'title' => $consignment->name . '-' . trans('portal.package_list') . '-' . date('YmdHis'),
            'consignment' => $consignment,
            'sizes' => $sizes,
            'artNo' => $artNos,
            'sdsCode' => $sdsCodes,
            'loadTypes' => $loadTypes,
            'productId' => $productId,
            'packageTypes' => $packageTypes,
            'packageCount' => $packageCount,
            'packingMode' => $packingMode,
            'packageLoadSizes' => $packageLoadSizes,
            'packageTypeSizes' => $packageTypeSizes,
            'boxTypes' => $boxTypes,
            'totalCount' => $totalCount,
            'assrSizes' => $assrSizes
        ];

        $pdf = \PDF::loadView('reports.pdf.hm-new', $data);

        return $pdf->download($data['title'] . '.pdf');

    }
    // zara
    public function exportModelPdf($consignmentId)
    {

        header('Content-Type: text/html; charset=utf-8');
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');

        $consignment = \App\Consignment::with(['company', 'packages', 'packages.items', 'order'])
            ->withCount(['items', 'packages'])
            ->find($consignmentId);

        $data = [
            'title' => $consignment->name . '-' . trans('portal.model_list') . '-' . date('YmdHis'),
            'consignment' => $consignment
        ];

        // echo '<pre>';
        // print_r($data['consignment']);
        // exit();

        //return view('reports.pdf.model', $data);
        $pdf = \PDF::loadView('reports.pdf.model', $data);

        return $pdf->download($data['title'] . '.pdf');

    }
    // hm
    public function exportModelHmPdf($consignmentId)
    {

        header('Content-Type: text/html; charset=utf-8');
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');

        $consignment = \App\Consignment::with(['company', 'packages', 'packages.items', 'order'])
            ->withCount(['items', 'packages'])
            ->find($consignmentId);

        $data = [
            'title' => $consignment->name . '-' . trans('portal.model_list') . '-' . date('YmdHis'),
            'consignment' => $consignment
        ];

        foreach ($consignment->packages as $package) {
            foreach ($package->items as $item) {

                $d = \App\CompanyDb::where('gtin', $item->gtin)->first();

                if (is_null($d) == false) {
                    array_push($item->itemDetails, $d);
                }

            }
        }

        // echo '<pre>';
        // print_r($data['consignment']);
        // exit();

        // return view('reports.pdf.modelHm', $data);
        // exit();
        $pdf = \PDF::loadView('reports.pdf.modelHm', $data);

        return $pdf->download($data['title'] . '.pdf');

    }
    // ms
    public function exportModelMsPdf($consignmentId)
    {

        header('Content-Type: text/html; charset=utf-8');
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');

        $consignment = \App\Consignment::with(['company', 'packages', 'packages.items', 'order'])
            ->withCount(['items', 'packages'])
            ->find($consignmentId);

        //echo $consignment->name.'_ftm<br>';
        if (!empty($consignment->name)) {

            $poNumberEx = explode("/", $consignment->name);
            $poNumber = $poNumberEx[0];
            //echo $poNumber.'<br>';
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

                    $dep = $xmlFile->dep;
                    //B = Boxed,H = Hanging,C = Converted,D = Boxed to Tote,P = Converted when picked for despatch
                    $packType = $xmlFile->packType;
                    if ($packType == "B") {
                        $packType = "Boxed";
                    } elseif ($packType == "H") {
                        $packType = "Hanging";
                    } elseif ($packType == "C") {
                        $packType = "Converted";
                    } elseif ($packType == "D") {
                        $packType = "Boxed to Tote";
                    }
                    $packType;
                    $supplierDesc = $xmlFile->supplierDesc;
                    // //O – Original, A – amended, C –cancelled.
                    $poStatusType = $xmlFile->poStatusType;
                    if ($poStatusType == 'O') {
                        $poStatusType = "Original";
                    } elseif ($poStatusType == 'A') {
                        $poStatusType = "Amended";
                    } elseif ($poStatusType == 'C') {
                        $poStatusType = "Cancelled";
                    }
                    $poStatusType;
                    $manufacturerCode = $xmlFile->manufacturerCode;
                    $factoryDescription = $xmlFile->factoryDescription;
                    $incotermType = $xmlFile->incotermType;
                    $portLoadingCode = $xmlFile->portLoadingCode;
                    $freightDesc = $xmlFile->freightDesc;
                    $paymentCurrency = $xmlFile->paymentCurrency;
                    $shipmentMethod = $xmlFile->shipmentMethod;
                    $orderNotes = $xmlFile->orderNotes;
                    $finalWarehouseDesc = $xmlFile->finalWarehouseDesc;
                    $destination = $xmlFile->destination;
                    $colourCode = $xmlFile->colourCode;
                    $departmentDesc = $xmlFile->departmentDesc;
                    $strokeDesc = $xmlFile->strokeDesc;
                    $colourDesc = $xmlFile->colourDesc;
                    $season = $xmlFile->season;

                    $consignmentExtra = array(
                        'dep' => $dep,
                        'packType' => $packType,
                        'supplierDesc' => $supplierDesc,
                        'poStatusType' => $poStatusType,
                        'manufacturerCode' => $manufacturerCode,
                        'factoryDescription' => $factoryDescription,
                        'incotermType' => $incotermType,
                        'portLoadingCode' => $portLoadingCode,
                        'freightDesc' => $freightDesc,
                        'paymentCurrency' => $paymentCurrency,
                        'shipmentMethod' => $shipmentMethod,
                        'orderNotes' => $orderNotes,
                        'finalWarehouseDesc' => $finalWarehouseDesc,
                        'destination' => $destination,
                        'colourCode' => $colourCode,
                        'departmentDesc' => $departmentDesc,
                        'strokeDesc' => $strokeDesc,
                        'colourDesc' => $colourDesc,
                        'season' => $season,
                    );

                }

            }

        }
        /*foreach ($consignment->packages as $package) {
            foreach ($package->items as $item) {
                // gtin son 8 i upc ye eşit olanlar
                $gtinEpcUzunluk = substr($item->gtin, -8);
                if (substr($gtinEpcUzunluk, 0, 1) == 0) {
                    $gtinEpcUzunluk = substr($item->gtin, -7);
                }
                $d = \App\CompanyMsDb::where('upc', $gtinEpcUzunluk)->first();

                if (is_null($d) == false) {
                    array_push($item->itemDetails, $d);
                }

            }
        }*/

        $epcCartonsQuery = DB::table('ms_upc_cartons')->where(['consignment_id' => $consignmentId])->get();
        foreach ($epcCartonsQuery as $epcCartons) {

            $totalCounted = DB::table('ms_carton_epcs')
                ->where(['consigment_id' => $consignmentId, 'upc' => $epcCartons->upc])
                ->count('*');

            $UndCounted = DB::table('ms_carton_epcs')
                ->where(['consigment_id' => $consignmentId, 'upc' => $epcCartons->upc, ['gittinCheck', '=', 0]])
                ->count('*');

            $results = DB::select(DB::raw("select upc,
                           cartonID,
                           series,
                           colour,
                           singles,
                           barcode,
                           (select count(*) from ms_carton_epcs mce where mce.barcode = mc.barcode and gittinCheck <>0) as counted,
                           (select count(*) from ms_carton_epcs mce where mce.barcode = mc.barcode and gittinCheck = 0) as Undefinecounted
                    from ms_cartons mc
                    where  mc.upc = :upc"), array(
                'upc' => $epcCartons->upc,
            ));

            $resultData[] = [
                'upc' => $epcCartons->upc,
                'size' => $epcCartons->size,
                'counted' => $totalCounted - $UndCounted,
                'undcounted' => $UndCounted,
                'boxes' => json_encode($results),
                'description' => $epcCartons->descriptions,
            ];

        }

        $data = [
            'title' => $consignment->name . '-' . trans('portal.model_list') . '-' . date('YmdHis'),
            'consignment' => $consignment,
            'consignmentExtra' => $consignmentExtra,
            'resultData' => $resultData
        ];

//        return view('reports.pdf.modelMs', $data);
//        exit();
        $pdf = \PDF::loadView('reports.pdf.modelMs', $data);

        return $pdf->download($data['title'] . '.pdf');

    }
    //hb
    public function exportModelHbPdf($consignmentId)
    {

        header('Content-Type: text/html; charset=utf-8');
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');

        $consignment = \App\Consignment::with(['company', 'packages', 'packages.items', 'order'])
            ->withCount(['items', 'packages'])
            ->find($consignmentId);

        $bedenModel = array();
        foreach ($consignment->packages as $package) {
            foreach ($package->items as $item) {

                $d = \App\CompanyDb::where('gtin', $item->gtin)->first();

                if (is_null($d) == false) {
                    array_push($item->itemDetails, $d);
                }

                if (count($item->itemDetails) > 0) {
                    //$bedenModelToplam = $bedenModelToplam + count( $item->itemDetails);
                    $bedenModel[$item->itemDetails[0]->description][$item->itemDetails[0]->sds_code][] = $item->itemDetails[0]->sds_code;
                } else {
                    $bedenModel['UND']['UND'][] = 'UND';
                }

            }
        }

        $data = [
            'title' => $consignment->name . '-' . trans('portal.model_list') . '-' . date('YmdHis'),
            'consignment' => $consignment,
            'bedenModel' => $bedenModel
        ];

        // echo '<pre>';
        // print_r($data['bedenModel']);
        // //print_r($data['bedenModelToplam']);
        // exit();

        // return view('reports.pdf.modelHb', $data);
        // exit();
        $pdf = \PDF::loadView('reports.pdf.modelHb', $data);

        return $pdf->download($data['title'] . '.pdf');

    }
    //levis
    public function exportModelLevisPdf($consignmentId)
    {

        header('Content-Type: text/html; charset=utf-8');
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');

        $consignment = \App\Consignment::with(['company', 'packages', 'packages.items', 'order'])
            ->withCount(['items', 'packages'])
            ->find($consignmentId);

        $bedenModel = array();
        foreach ($consignment->packages as $package) {
            foreach ($package->items as $item) {

                $d = \App\CompanyLevisDb::where('gtin', $item->gtin)->first();

                if (is_null($d) == false) {
                    array_push($item->itemDetails, $d);
                }

                if (count($item->itemDetails) > 0) {
                    //$bedenModelToplam = $bedenModelToplam + count( $item->itemDetails);
                    $bedenModel[$item->itemDetails[0]->po][$item->itemDetails[0]->sds_code][] = $item->itemDetails[0]->sds_code;
                } else {
                    $bedenModel['UND']['UND'][] = 'UND';
                }

            }
        }

        $data = [
            'title' => $consignment->name . '-' . trans('portal.model_list') . '-' . date('YmdHis'),
            'consignment' => $consignment,
            'bedenModel' => $bedenModel
        ];

        // echo '<pre>';
        // print_r($data['bedenModel']);
        // //print_r($data['bedenModelToplam']);
        // exit();

        // return view('reports.pdf.modelHb', $data);
        // exit();
        $pdf = \PDF::loadView('reports.pdf.modelLevis', $data);

        return $pdf->download($data['title'] . '.pdf');

    }
    //default epc pdf
    public function exportEpcPdf($consignmentId)
    {

        header('Content-Type: text/html; charset=utf-8');
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');

        $consignment = \App\Consignment::with(['company', 'packages', 'packages.items'])
            ->withCount(['items', 'packages'])
            ->find($consignmentId);

        $data = [
            'title' => $consignment->name . '-' . trans('portal.epc_list') . '-' . date('YmdHis'),
            'consignment' => $consignment
        ];

        $pdf = \PDF::loadView('reports.pdf.epc', $data);

        return $pdf->download($data['title'] . '.pdf');

    }
    // ms epc pdf
    public function exportEpcMsPdf($consignmentId)
    {

        header('Content-Type: text/html; charset=utf-8');
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');

        $consignment = \App\Consignment::with(['company', 'packages', 'packages.items'])
             ->withCount(['items', 'packages'])
             ->find($consignmentId);

        $cartons = DB::table('ms_cartons')
            ->select(['barcode','upc',DB::raw("concat('CartonID: ',cartonID,' - UPC:',upc,' - Barcode:',barcode) as name")])
            ->whereIn('upc',DB::table('ms_upc_cartons')
                ->select('upc')
                ->where(['consignment_id' => $consignmentId]))->get();



        foreach ($cartons as $carton){
            $epclist[$carton->barcode]['name'] = $carton->name;
            $epcslistDB = DB::table('ms_carton_epcs')->select('epc')->where([
                'barcode' => $carton->barcode,
                'upc' => $carton->upc
            ])->get();
            $epcslistd = [];
            foreach ($epcslistDB as $e){$epcslistd[] = $e->epc; }
            $epclist[$carton->barcode]['epcs'] = $epcslistd;
            $epclist[$carton->barcode]['count'] = DB::table('ms_carton_epcs')->select('epc')->where([
                'barcode' => $carton->barcode,
                'upc' => $carton->upc
            ])->count();

        }

        if (!empty($consignment->name)) {

            $poNumberEx = explode("/", $consignment->name);
            $poNumber = $poNumberEx[0];
            //echo $poNumber.'<br>';
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

                    $dep = $xmlFile->dep;
                    //B = Boxed,H = Hanging,C = Converted,D = Boxed to Tote,P = Converted when picked for despatch
                    $packType = $xmlFile->packType;
                    if ($packType == "B") {
                        $packType = "Boxed";
                    } elseif ($packType == "H") {
                        $packType = "Hanging";
                    } elseif ($packType == "C") {
                        $packType = "Converted";
                    } elseif ($packType == "D") {
                        $packType = "Boxed to Tote";
                    }
                    $packType;
                    $supplierDesc = $xmlFile->supplierDesc;
                    // //O – Original, A – amended, C –cancelled.
                    $poStatusType = $xmlFile->poStatusType;
                    if ($poStatusType == 'O') {
                        $poStatusType = "Original";
                    } elseif ($poStatusType == 'A') {
                        $poStatusType = "Amended";
                    } elseif ($poStatusType == 'C') {
                        $poStatusType = "Cancelled";
                    }
                    $poStatusType;
                    $manufacturerCode = $xmlFile->manufacturerCode;
                    $factoryDescription = $xmlFile->factoryDescription;
                    $incotermType = $xmlFile->incotermType;
                    $portLoadingCode = $xmlFile->portLoadingCode;
                    $freightDesc = $xmlFile->freightDesc;
                    $paymentCurrency = $xmlFile->paymentCurrency;
                    $shipmentMethod = $xmlFile->shipmentMethod;
                    $orderNotes = $xmlFile->orderNotes;
                    $finalWarehouseDesc = $xmlFile->finalWarehouseDesc;
                    $destination = $xmlFile->destination;
                    $colourCode = $xmlFile->colourCode;
                    $departmentDesc = $xmlFile->departmentDesc;
                    $strokeDesc = $xmlFile->strokeDesc;
                    $colourDesc = $xmlFile->colourDesc;
                    $season = $xmlFile->season;

                    $consignmentExtra = array(
                        'dep' => $dep,
                        'packType' => $packType,
                        'supplierDesc' => $supplierDesc,
                        'poStatusType' => $poStatusType,
                        'manufacturerCode' => $manufacturerCode,
                        'factoryDescription' => $factoryDescription,
                        'incotermType' => $incotermType,
                        'portLoadingCode' => $portLoadingCode,
                        'freightDesc' => $freightDesc,
                        'paymentCurrency' => $paymentCurrency,
                        'shipmentMethod' => $shipmentMethod,
                        'orderNotes' => $orderNotes,
                        'finalWarehouseDesc' => $finalWarehouseDesc,
                        'destination' => $destination,
                        'colourCode' => $colourCode,
                        'departmentDesc' => $departmentDesc,
                        'strokeDesc' => $strokeDesc,
                        'colourDesc' => $colourDesc,
                        'season' => $season,
                    );

                }

            }

        }
        $data = [
            'title' => $consignment->name . '-' . trans('portal.epc_list') . '-' . date('YmdHis'),
            'consignment' => $consignment,
            'consignmentExtra' => $consignmentExtra,
            'epc' => $epclist
        ];

        return view('reports.pdf.epcMs', $data);
        // exit();
        // $pdf = \PDF::loadView('reports.pdf.epcMs', $data);

        //   return $pdf->download($data['title'] . '.pdf');

    }

    public function exportEpcCsv($consignmentId)
    {

        // return Excel::download(new EpcExport, 'EPC-Report.csv');
        // header('Content-Type: text/html; charset=utf-8');
        // ini_set('memory_limit','-1');
        // ini_set('max_execution_time', '-1');

        $consignment = \App\Consignment::with(['company', 'packages', 'packages.items'])
            ->withCount(['items', 'packages'])
            ->find($consignmentId);

        $data = [
            'title' => $consignment->name . '-' . trans('portal.epc_list') . '-' . date('YmdHis'),
            'consignment' => $consignment
        ];

        // $filename = "members_" . date('Y-m-d') . ".csv";
        // $delimiter = ",";
        // //create a file pointer
        // $f = fopen('php://memory', 'w');
        // //set column headers
        // $fields = array('EPC');
        // fputcsv($f, $fields, $delimiter);
        // $array = array("epc" => array());

        $load = "";
        $file = "";
        foreach ($consignment->packages as &$package) {
            foreach ($package->items as &$item) {
                switch ($package->load_type) {
                    case "Solid":
                        $load = "S";
                        break;
                    case "Assortment":
                        $load = "A";
                        break;
                    case "SolidLast":
                        $load = "SL";
                        break;
                    case "AssortmentLast":
                        $load = "AL";
                        break;
                    case "SolidMix":
                        $load = "SM";
                        break;
                    default:
                        $load = "UND";
                }

                $file .= $package->box_type_id . ',' . $load . ',' . $package->package_no . ',' . '0.00' . ',' . $item->epc . "\n";

            }
        }

        $this->download_send_headers($consignment->name . "-EPC-Report.csv");
        echo $this->array2csv($file);
        die();
    }

    public function exportEpcPdfAsc($consignmentId)
    {

        $consignment = \App\Consignment::with(['company', 'packages', 'packages.items'])
            ->withCount(['items', 'packages'])
            ->find($consignmentId);

        $data = [
            'title' => $consignment->name . '-' . trans('portal.epc_list') . '-' . date('YmdHis'),
            'consignment' => $consignment
        ];

        $load = "";
        $file = "";
        $strData = [];
        $sepData = [];
        $fData = [];
        $sizeData = [];
        $sizeStrData = [];
        $sayi = 0;
        foreach ($consignment->packages as $k => &$package) {
            // $sayi++;
            //echo $sayi.' ==> '.$package->package_no.' - '.$package->load_type.'-'.$package->size.'<br>';
            //$bedenData = [];
            $size = "UND";
            foreach ($package->items as $v => &$item) {
                // Pakete ait badenler belirleniyor. solid ve assortmentLast paketleri için benzersiz bedenler oluşturuluyor.
                $d = \App\CompanyDb::where('gtin', $item->gtin)->where('consignment_id', $consignmentId)->first();

                if (is_null($d) == false) {
                    array_push($item->itemDetails, $d);
                }

                /*if (isset($item->itemDetails[0])) {*/
                if (
                    isset($item->itemDetails[0]) &&
                    ($package->load_type == "Solid" || $package->load_type == "AssortmentLast")
                ) {
                    //echo $item->itemDetails[0]->sds_code.'_beden<br>';
                    //$bedenData[] = $item->itemDetails[0]->sds_code;
                    $size = preg_replace('/\s+/', '', $item->itemDetails[0]->sds_code);

                }
                //echo getEpcSize($item->epc).'_<br>';
                switch ($package->load_type) {
                    case "Solid":
                        $load = "S";
                        break;
                    case "Assortment":
                        $load = "A";
                        break;
                    case "SolidLast":
                        $load = "SL";
                        break;
                    case "AssortmentLast":
                        $load = "AL";
                        break;
                    case "SolidMix":
                        $load = "SM";
                        break;
                    default:
                        $load = "UND";
                        break;
                }

                $strData[$package->load_type][$package->package_no][$size][] =
                    $package->box_type_id . ',' . $load . ',' . $package->package_no . ',' . '0.00' . ',' . $item->epc;
            }

            //echo $size.'<br>';
            //array_unique($bedenData);
            // echo '<pre>';
            // print_r(array_unique($bedenData));
        }

        //echo '<pre>';
        //print_r($strData);
        //exit();

        foreach ($strData as $v => $strPackage) {
            //echo $v.'__<br>';
            foreach ($strPackage as $p => $strValue) {
                //$sayi++;
                //echo $p.'_paket_'.$sayi.'<br>';
                foreach ($strValue as $b => $beden) {
                    if ($b != "") {
                        switch ($b) {
                            case "XXS":
                                $sizeStrData['XXS'][$v][$p] = $strPackage[$p];
                                break;
                            case "XS":
                                $sizeStrData['XS'][$v][$p] = $strPackage[$p];
                                break;
                            case "S":
                                $sizeStrData['S'][$v][$p] = $strPackage[$p];
                                break;
                            case "M":
                                $sizeStrData['M'][$v][$p] = $strPackage[$p];
                                break;
                            case "L":
                                $sizeStrData['L'][$v][$p] = $strPackage[$p];
                                break;
                            case "XL":
                                $sizeStrData['XL'][$v][$p] = $strPackage[$p];
                                break;
                            case "XXL":
                                $sizeStrData['XXL'][$v][$p] = $strPackage[$p];
                                break;
                            case "XXXL":
                                $sizeStrData['XXXL'][$v][$p] = $strPackage[$p];
                                break;
                            case "134/140":
                                $sizeStrData['134/140'][$v][$p] = $strPackage[$p];
                                break;
                            case "146/152":
                                $sizeStrData['146/152'][$v][$p] = $strPackage[$p];
                                break;
                            case "158/164":
                                $sizeStrData['158/164'][$v][$p] = $strPackage[$p];
                                break;
                            case "170":
                                $sizeStrData['170'][$v][$p] = $strPackage[$p];
                                break;
                            case "UND":
                                $sizeStrData['UND'][$v][$p] = $strPackage[$p];
                                break;
                        }
                    }
                }
            }
        }

        //echo '<pre>';
        //print_r($sizeStrData);
        //exit();
        //beden sıralaması helper dan düzenleniyor.
        $siralaHelper = getOrderedBySize($sizeStrData);
        //print_r($siralaHelper);
        //exit();
        //print_r(getOrderedBySize($sizeStrData));

        //ana diziden solid ve assortmentlast olan veriler siliniyor.
        foreach ($strData as $v => $strPackage) {
            if ($v == 'Solid' || $v == 'AssortmentLast') {
                foreach ($strPackage as $strKey => $strValue) {
                    //echo $strKey.'<br>';
                    unset($strData[$v][$strKey]);
                }
            }
        }

        //beden sıralaması yapılan diziden ana diziye veriler gönderiliyor.
        foreach ($siralaHelper as $s => $sirala) {
            foreach ($sirala as $sk => $siralaKoli) {
                if (array_key_exists($sk, $strData)) {
                    $strData[$sk] += $siralaKoli;
                }
            }
        }

        //beden sıralamasından sonra koli sıralaması gerçekleştiriliyor.
        ksort($strData);

        //echo '<pre>';
        //print_r($strData);
        //exit();

        foreach ($strData as $v => $strPackage) {
            //echo $v.'__<br>';
            foreach ($strPackage as $p => $strValue) {
                $sayi++;
                //echo $p.'_paket_'.$sayi.'<br>';
                foreach ($strValue as $strBeden) {
                    foreach ($strBeden as $epcValue) {
                        //print_r($epcValue);
                        $sepData[$sayi][] = $epcValue;
                    }
                }
            }
        }

        //echo '<pre>';
        //print_r($strData);
        //echo '__________';
        //print_r($sepData);
        //exit();

        foreach ($sepData as $sKey => $finalValue) {
            //echo $sKey.'<br>';
            foreach ($finalValue as $val) {
                //echo $val.'<br>';
                $bol = explode(',', $val, 4);
                $bol[2] = $sKey;
                //echo $bol[2].'__degisti<br>';
                $fData[] = implode(',', $bol);
            }
            //print_r($bol);
            //echo '<br>';
        }

        foreach ($fData as $fileValue) {
            //echo $fileValue.'<br>';
            $file .= $fileValue . "\n";
        }

        //echo '<pre>';
        //print_r($strData);
        //print_r($fData);
        //print_r($file);
        //exit();

        $this->download_send_headers($consignment->name . "-EPC-Sort-Report.csv");
        echo $this->array2csv($file);
        die();

    }

    public function exportEpcPdfCheck($consignmentId)
    {

        header('Content-Type: text/html; charset=utf-8');
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');

        $consignment = \App\Consignment::with(['company', 'packages', 'packages.items'])
            ->withCount(['items', 'packages'])
            ->find($consignmentId);

        $data = [
            'title' => $consignment->name . '-' . trans('portal.epc_check') . '-' . date('YmdHis'),
            'consignment' => $consignment
        ];

        $load = "";
        $file = "";
        $strData = [];
        $sepData = [];
        $fData = [];
        $sizeData = [];
        $sizeStrData = [];
        $sayi = 0;

        foreach ($consignment->packages as $k => &$package) {
            // $sayi++;
            // echo $sayi.' ==> '.$package->package_no.' - '.$package->load_type.'-'.$package->size.'<br>';
            //$bedenData = [];
            $size = "UND";
            foreach ($package->items as $v => &$item) {
                // Pakete ait badenler belirleniyor. solid ve assortmentLast paketleri için benzersiz bedenler oluşturuluyor.
                $d = \App\CompanyDb::where('gtin', $item->gtin)->where('consignment_id', $consignmentId)->first();

                if (is_null($d) == false) {
                    array_push($item->itemDetails, $d);
                }

                /*if (isset($item->itemDetails[0])) {*/
                if (
                    isset($item->itemDetails[0]) &&
                    ($package->load_type == "Solid" || $package->load_type == "AssortmentLast")
                ) {
                    //echo $item->itemDetails[0]->sds_code.'_beden<br>';
                    //$bedenData[] = $item->itemDetails[0]->sds_code;
                    $size = preg_replace('/\s+/', '', $item->itemDetails[0]->sds_code);
                }

                //echo getEpcSize($item->epc).'_<br>';
                switch ($package->load_type) {
                    case "Solid":
                        $load = "S";
                        break;
                    case "Assortment":
                        $load = "A";
                        break;
                    case "SolidLast":
                        $load = "SL";
                        break;
                    case "AssortmentLast":
                        $load = "AL";
                        break;
                    case "SolidMix":
                        $load = "SM";
                        break;
                    default:
                        $load = "UND";
                        break;
                }

                $strData[$package->load_type][$package->package_no][$size][] =
                    $package->box_type_id . ',' . $load . ',' . $package->package_no . ',' . '0.00' . ',' . $item->epc;

            }

            //echo $size.'<br>';
            //array_unique($bedenData);
            // echo '<pre>';
            // print_r(array_unique($bedenData));
        }

        //echo '<pre>';
        //print_r($strData['Solid']);
        //exit();
        foreach ($strData as $v => $strPackage) {
            //echo $v.'__<br>';
            foreach ($strPackage as $p => $strValue) {
                //$sayi++;
                //echo $p.'_paket_'.$sayi.'<br>';
                foreach ($strValue as $b => $beden) {

                    if ($b != "") {

                        switch ($b) {
                            case "XXS":
                                $sizeStrData['XXS'][$v][$p] = $strPackage[$p];
                                break;
                            case "XS":
                                $sizeStrData['XS'][$v][$p] = $strPackage[$p];
                                break;
                            case "S":
                                $sizeStrData['S'][$v][$p] = $strPackage[$p];
                                break;
                            case "M":
                                $sizeStrData['M'][$v][$p] = $strPackage[$p];
                                break;
                            case "L":
                                $sizeStrData['L'][$v][$p] = $strPackage[$p];
                                break;
                            case "XL":
                                $sizeStrData['XL'][$v][$p] = $strPackage[$p];
                                break;
                            case "XXL":
                                $sizeStrData['XXL'][$v][$p] = $strPackage[$p];
                                break;
                            case "XXXL":
                                $sizeStrData['XXXL'][$v][$p] = $strPackage[$p];
                                break;
                            case "134/140":
                                $sizeStrData['134/140'][$v][$p] = $strPackage[$p];
                                break;
                            case "146/152":
                                $sizeStrData['146/152'][$v][$p] = $strPackage[$p];
                                break;
                            case "158/164":
                                $sizeStrData['158/164'][$v][$p] = $strPackage[$p];
                                break;
                            case "170":
                                $sizeStrData['170'][$v][$p] = $strPackage[$p];
                                break;
                            case "UND":
                                $sizeStrData['UND'][$v][$p] = $strPackage[$p];
                                break;
                        }

                    }

                }
            }
        }

        //echo '<pre>';
        //print_r($sizeStrData);
        //exit();
        //beden sıralaması helper dan düzenleniyor.
        $siralaHelper = getOrderedBySize($sizeStrData);
        //print_r($siralaHelper);
        //exit();
        //print_r(getOrderedBySize($sizeStrData));

        //ana diziden solid ve assortmentlast olan veriler siliniyor.
        foreach ($strData as $v => $strPackage) {
            if ($v == 'Solid' || $v == 'AssortmentLast') {
                foreach ($strPackage as $strKey => $strValue) {
                    //echo $strKey.'<br>';
                    unset($strData[$v][$strKey]);
                }
            }
        }

        //beden sıralaması yapılan diziden ana diziye veriler gönderiliyor.
        //echo '<pre>';
        foreach ($siralaHelper as $s => $sirala) {
            foreach ($sirala as $sk => $siralaKoli) {
                //echo $sk.'<br>';
                if (array_key_exists($sk, $strData)) {
                    $strData[$sk] += $siralaKoli;
                    //echo 'sdk_ftm<br>';
                    //print_r($strData);
                }
            }
        }

        ksort($strData);

        //echo '<pre>';
        //print_r($strData);
        //exit();

        foreach ($strData as $v => $strPackage) {
            //echo $v.'__<br>';
            foreach ($strPackage as $p => $strValue) {
                $sayi++;
                //echo $p.'_paket_'.$sayi.'<br>';
                $checkData[] = [
                    'okutulanSira' => $p,
                    'yeniSira' => $sayi
                ];
            }
        }

        $postData = [
            'data' => $data,
            'checkData' => $checkData
        ];

        //echo '<pre>';
        //print_r($strData);
        //print_r($sepData);
        //print_r($fData);
        //print_r($postData['checkData']);
        //return view('reports.pdf.packageCheck', $postData);
        //exit();

        $pdf = \PDF::loadView('reports.pdf.packageCheck', $postData);
        return $pdf->download($data['title'] . '.pdf');

    }


    function array2csv($file)
    {
        // if (!isset($file)) {
        //     return null;
        // }
        // ob_start();
        // $file = "\n".$file;
        // $df = fopen("php://output", 'w');
        // // $len = strlen($file);
        //
        // fputcsv($df, [$file], ',', ' ');
        //
        // // preg_replace("/#.*?\n/", "\n", $file));
        // // preg_replace("/[^A-Za-z0-9]/", '', $file));
        // // str_replace(['"',"'"], "", $array));
        // // preg_replace(array('/^\[/','/\]$/'), '', [$file]), ',', '[');
        // // str_replace(array('-',']'), '',[$file]), ',', '-');
        // fclose($df);
        // return ob_get_clean();
        print_r($file);
        //   die();
        //
        //   $fp = fopen('file.csv', 'w');
        //
        //   foreach ($list as $fields) {
        //       fputcsv($fp, $fields);
        //   }
        //
        //   fclose($fp);
        //   // return ob_get_clean();
        //
        //
        //
        // // if (!isset($file)) {
        // //     return null;
        // // }
        // // ob_start();
        // $file = "\n".$file;
        // $df = fopen("php://output", 'w');
        // // $len = strlen($file);
        //
        // fputcsv($df, [$file], ',', ' ');
        //
        // // preg_replace("/#.*?\n/", "\n", $file));
        // // preg_replace("/[^A-Za-z0-9]/", '', $file));
        // // str_replace(['"',"'"], "", $array));
        // // preg_replace(array('/^\[/','/\]$/'), '', [$file]), ',', '[');
        // // str_replace(array('-',']'), '',[$file]), ',', '-');
        // fclose($df);
        // return ob_get_clean();
    }

    // function string_sanitize($s) {
    //     $result = preg_replace("/[^a-zA-Z0-9]+/", "", html_entity_decode($s, ENT_QUOTES));
    //     return $result;
    // }

    function download_send_headers($filename)
    {
        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
    }

    public function exportGtinPdf($consignmentId)
    {

        header('Content-Type: text/html; charset=utf-8');
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');

        $consignment = \App\Consignment::with(['company', 'packages', 'packages.items'])
            ->withCount(['items', 'packages'])
            ->find($consignmentId);

        $data = [
            'title' => $consignment->name . '-' . trans('portal.gtin_list') . '-' . date('YmdHis'),
            'consignment' => $consignment
        ];
        //return view('reports.pdf.epc', $data);
        $pdf = \PDF::loadView('reports.pdf.gtin', $data);
        return $pdf->download($data['title'] . '.pdf');

    }
    //default delete package
    public function exportDeletedPackagePdf($consignmentId)
    {

        header('Content-Type: text/html; charset=utf-8');
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');

        $consignment = \App\Consignment::with(['company', 'deleted_packages'])
            ->withCount(['items', 'packages'])
            ->find($consignmentId);

        $deleted_packages = [];
        foreach ($consignment->deleted_packages as $key => $value) {
            if ($value->deleted_items()->count() > 0) {
                $deleted_packages[] = $value;
            }
        }

        $data = [
            'title' => $consignment->name . '-' . trans('portal.deleted_package_list') . '-' . date('YmdHis'),
            'deleted_packages' => $deleted_packages,
            'consignment' => $consignment
        ];

        //return view('reports.pdf.deleted-package', $data);
        $pdf = \PDF::loadView('reports.pdf.deleted-package', $data);
        return $pdf->download($data['title'] . '.pdf');

    }
    // ms delete package
    public function exportDeletedPackageMsPdf($consignmentId)
    {

        header('Content-Type: text/html; charset=utf-8');
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');

        $consignment = \App\Consignment::with(['company', 'deleted_packages'])
            ->withCount(['items', 'packages'])
            ->find($consignmentId);

        if (!empty($consignment->name)) {

            $poNumberEx = explode("/", $consignment->name);
            $poNumber = $poNumberEx[0];
            //echo $poNumber.'<br>';
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

                    $dep = $xmlFile->dep;
                    //B = Boxed,H = Hanging,C = Converted,D = Boxed to Tote,P = Converted when picked for despatch
                    $packType = $xmlFile->packType;
                    if ($packType == "B") {
                        $packType = "Boxed";
                    } elseif ($packType == "H") {
                        $packType = "Hanging";
                    } elseif ($packType == "C") {
                        $packType = "Converted";
                    } elseif ($packType == "D") {
                        $packType = "Boxed to Tote";
                    }
                    $supplierDesc = $xmlFile->supplierDesc;
                    // //O – Original, A – amended, C –cancelled.
                    $poStatusType = $xmlFile->poStatusType;
                    if ($poStatusType == 'O') {
                        $poStatusType = "Original";
                    } elseif ($poStatusType == 'A') {
                        $poStatusType = "Amended";
                    } elseif ($poStatusType == 'C') {
                        $poStatusType = "Cancelled";
                    }
                    $manufacturerCode = $xmlFile->manufacturerCode;
                    $factoryDescription = $xmlFile->factoryDescription;
                    $incotermType = $xmlFile->incotermType;
                    $portLoadingCode = $xmlFile->portLoadingCode;
                    $freightDesc = $xmlFile->freightDesc;
                    $paymentCurrency = $xmlFile->paymentCurrency;
                    $shipmentMethod = $xmlFile->shipmentMethod;
                    $orderNotes = $xmlFile->orderNotes;
                    $finalWarehouseDesc = $xmlFile->finalWarehouseDesc;
                    $destination = $xmlFile->destination;
                    $colourCode = $xmlFile->colourCode;
                    $departmentDesc = $xmlFile->departmentDesc;
                    $strokeDesc = $xmlFile->strokeDesc;
                    $colourDesc = $xmlFile->colourDesc;
                    $season = $xmlFile->season;

                    $consignmentExtra = array(
                        'dep' => $dep,
                        'packType' => $packType,
                        'supplierDesc' => $supplierDesc,
                        'poStatusType' => $poStatusType,
                        'manufacturerCode' => $manufacturerCode,
                        'factoryDescription' => $factoryDescription,
                        'incotermType' => $incotermType,
                        'portLoadingCode' => $portLoadingCode,
                        'freightDesc' => $freightDesc,
                        'paymentCurrency' => $paymentCurrency,
                        'shipmentMethod' => $shipmentMethod,
                        'orderNotes' => $orderNotes,
                        'finalWarehouseDesc' => $finalWarehouseDesc,
                        'destination' => $destination,
                        'colourCode' => $colourCode,
                        'departmentDesc' => $departmentDesc,
                        'strokeDesc' => $strokeDesc,
                        'colourDesc' => $colourDesc,
                        'season' => $season,
                    );

                }

            }

        }

        $deleted_packages = [];
//        foreach ($consignment->deleted_packages as $key => $value) {
//            if ($value->deleted_items()->count() > 0) {
//                $deleted_packages[] = $value;
//            }
//        }
        $deletedCountQuery = DB::table('ms_upc_cartons')->select('upc')->where(['consignment_id' => $consignmentId])->get();
        foreach ($deletedCountQuery as $deleteCount){

            $carton = DB::table('ms_cartons')
                ->select(['barcode', 'cartonID',DB::raw("(select count(*) from ms_carton_epcs_deleted where upc = ms_cartons.upc and barcode = ms_cartons.barcode) as count")])
                ->where(['upc' => $deleteCount->upc])
                ->whereIn('barcode',DB::table('ms_carton_epcs_deleted')->select('barcode'))
                ->get();

            foreach ($carton as $val){

                $deleteData[] = [
                    'cartonID' => $val->cartonID,
                    'count' => $val->count
                ];

            }
        }

//        echo '<pre>';
//        print_r($deleteData);
//        exit();

        $data = [
            'title' => $consignment->name . '-' . trans('portal.deleted_package_list') . '-' . date('YmdHis'),
            'deleted_packages' => $deleteData,
            'consignment' => $consignment,
            'consignmentExtra' => $consignmentExtra,

        ];

//         return view('reports.pdf.deleted-package-ms', $data);
//         exit();
        $pdf = \PDF::loadView('reports.pdf.deleted-package-ms', $data);
        return $pdf->download($data['title'] . '.pdf');

    }

}
