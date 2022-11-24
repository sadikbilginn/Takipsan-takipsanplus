<?php

namespace App\Http\Controllers;

use App\Company;
use App\Consignment;
use App\LogActivity;
use App\Package;
use App\Helpers\OptionTrait;
use App\Item;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Cache;

class HomeController extends Controller
{
    use OptionTrait;

    public function index()
    {
        if(roleCheck(config('settings.roles.admin'))){
          $this->adminIndex();

          return view('home', $this->data);
        } 

        if($this->roleCheck(config('settings.roles.partner')) ){
          $this->partnerIndex();

          return view('home_partner', $this->data);
        } 
    
        if($this->roleCheck(config('settings.roles.anaUretici'))){
          $this->anaUreticiIndex();

            return view('home_main_company', $this->data);

        }else{

            $this->companyIndex();

            return view('home_company', $this->data);
        }

    }

    public function indexProduction()
    {
        $this->productionIndex();
        return view('home_production', $this->data);

    }

    public function adminIndex()
    {
        $companies = Company::where('status', 1)->where('main_company_id',0)->get();
        if ($companies) {
            $this->data['companies'] = $companies;
        }

        $consignments = Consignment::orderBy('created_at')
        ->where("created_at", ">", Carbon::now()->subMonths(6))
        ->get()
        ->groupBy(function ($val) {
            return Carbon::parse($val->created_at)->translatedFormat('F');
        });
      if ($consignments) {
          $this->data['consignments'] = $consignments;
      }

      $maxConsignments = Consignment::with(['company'])->where('company_id', '!=', 0)->select('company_id', \Illuminate\Support\Facades\DB::raw('count(company_id) as total'))->groupBy('company_id')->orderBy('total', 'desc')->limit(3)->get();
      if ($maxConsignments) {
          $this->data['maxConsignments'] = $maxConsignments;
      }

      $maxConsignee = Consignment::with(['consignee'])->where('consignee_id', '!=', 0)->select('consignee_id', \Illuminate\Support\Facades\DB::raw('count(consignee_id) as total'))->groupBy('consignee_id')->orderBy('total', 'desc')->limit(3)->get();
      if ($maxConsignee) {
          $this->data['maxConsignee'] = $maxConsignee;
      }
        
        $this->data['consignment']  = Consignment::count();
        $this->data['packages']     = Package::count();
        $this->data['items']        = Item::count();


        $companyInfo = Company::where('id',  auth()->user()->company_id)->first();
        $this->data['company_name'] = $companyInfo->name;

        $log_activity = LogActivity::with(['created_user', 'created_user.company'])->limit(6)->orderBy('id', 'desc')->get()->groupBy(function ($val) {
            return Carbon::parse($val->created_at)->format('d.m.Y');
        });
        if ($log_activity) {
            $this->data['log_activity'] = $log_activity;
        }
    }

    public function partnerIndex()
    {
        $companies = Company::where('status', 1)->where('created_user_id', auth()->user()->id)->where('main_company_id', 0)->get();
        
        if ($companies) {
            $this->data['companies'] = $companies;
        }

        $consignments = Consignment::orderBy('created_at')->join('companies', function ($join) {
          $join->on('consignments.company_id', '=', 'companies.id');
            })->select('consignments.*')
            ->where(function ($query) {
                $query->where("consignments.created_at", ">", Carbon::now()->subMonths(6));
            })
            ->where(function ($query) {
                $query->where("companies.created_user_id", "=", auth()->user()->id);
            })
            ->get()
            ->groupBy(function ($val) {
                return \Carbon\Carbon::parse($val->created_at)->translatedFormat('F');
            });
        if ($consignments) {
            $this->data['consignments'] = $consignments;
        }

        $maxConsignments = Consignment::join('companies', function ($join) {
          $join->on('consignments.company_id', '=', 'companies.id');
        })->select('consignments.company_id', \Illuminate\Support\Facades\DB::raw('count(consignments.company_id) as total'))
        ->where(function ($query) {
          $query->where("companies.created_user_id", "=", auth()->user()->id);
        })
        ->groupBy('consignments.company_id')
        ->orderBy('total', 'desc')->limit(3)->get();
        if ($maxConsignments) {
            $this->data['maxConsignments'] = $maxConsignments;
        }

        $maxConsignee = Consignment::join('companies', function ($join) {
          $join->on('consignments.company_id', '=', 'companies.id');
        })->select('consignments.consignee_id', \Illuminate\Support\Facades\DB::raw('count(consignments.consignee_id) as total'))
        ->where(function ($query) {
          $query->where("companies.created_user_id", "=", auth()->user()->id);
        })
        ->groupBy('consignments.consignee_id')
        ->orderBy('total', 'desc')->limit(3)->get();
        if ($maxConsignee) {
            $this->data['maxConsignee'] = $maxConsignee;
        }

        $this->data['consignment'] = Consignment::join('companies', function ($join) {
          $join->on('consignments.company_id', '=', 'companies.id');
      })->select('consignment.id')
      ->where(function ($query) {
          $query->where('companies.created_user_id','=', auth()->user()->id);
      })->count();

        $this->data['packages'] = Package::join('companies', function ($join) {
          $join->on('packages.company_id', '=', 'companies.id');
      })->select('packages.id')
      ->where(function ($query) {
          $query->where('companies.created_user_id','=',auth()->user()->id);
      })->count();
        
        $this->data['items'] = Item::join('companies', function ($join) {
          $join->on('items.company_id', '=', 'companies.id');
      })->select('items.id')
      ->where(function ($query) {
          $query->where('companies.created_user_id','=',auth()->user()->id);
      })->count();

        $companyInfo = Company::where('id',  auth()->user()->company_id)->first();
        $this->data['company_name'] = $companyInfo->name;

        $log_activity = LogActivity::with(['created_user', 'created_user.company'])->limit(6)->orderBy('id', 'desc')->get()->groupBy(function ($val) {
            return Carbon::parse($val->created_at)->format('d.m.Y');
        });
        if ($log_activity) {
            $this->data['log_activity'] = $log_activity;
        }
        $companiesOfPartner = Company::where('created_user_id', '=', auth()->user()->id)->orWhere('id', '=', auth()->user()->company_id)
            ->where(function ($query) {
                $query->where("companies.status", "=", 1);
            })->pluck('id');

          $this->data['companiesOfPartner'] = $companiesOfPartner->toArray();
    }


    public function productionIndex()
    {
          $companies = Company::where('status', 1)
          ->where('id',auth()->user()->company_id)
          ->orWhere('main_company_id',auth()->user()->company_id)
          ->with(['consignments', 'devices'])->get();
        
        if ($companies) {
            $this->data['companies'] = $companies;
        }

        //burasının düzeltilmesi lazım. orWhere main_company_id nin de eklenmesi lazım
        $consignments = Consignment::orderBy('created_at')
            ->where('company_id',auth()->user()->company_id)
            ->where("created_at", ">", Carbon::now()->subMonths(6))
            ->get()
            ->groupBy(function ($val) {
                return Carbon::parse($val->created_at)->translatedFormat('F');
            });
        if ($consignments) {
            $this->data['consignments'] = $consignments;
        }

        //$maxConsignments = Consignment::with(['company'])->where('company_id', '!=', 0)->where('company_id',auth()->user()->company_id)->select('company_id', \Illuminate\Support\Facades\DB::raw('count(company_id) as total'))->groupBy('company_id')->orderBy('total', 'desc')->limit(3)->get();
        $maxConsignments = Consignment::join('companies', function ($join) {
          $join->on('consignments.company_id', '=', 'companies.id');
      })->select('consignments.*')
      ->where(function ($query) {
          $query->where('companies.id','=',[auth()->user()->company_id])
              ->orWhere('companies.main_company_id','=',[auth()->user()->company_id]);
      })->select('company_id', \Illuminate\Support\Facades\DB::raw('count(company_id) as total'))->groupBy('company_id')->orderBy('total', 'desc')->limit(3)->get();
        
        if ($maxConsignments) {
            $this->data['maxConsignments'] = $maxConsignments;
        }

        //$maxConsignee = Consignment::with(['consignee'])->where('consignee_id', '!=', 0)->where('company_id',auth()->user()->company_id)->select('consignee_id', \Illuminate\Support\Facades\DB::raw('count(consignee_id) as total'))->groupBy('consignee_id')->orderBy('total', 'desc')->limit(3)->get();
        $maxConsignee = Consignment::join('companies', function ($join) {
          $join->on('consignments.company_id', '=', 'companies.id');
      })->select('consignments.*')
      ->where(function ($query) {
          $query->where('companies.id','=',[auth()->user()->company_id])
              ->orWhere('companies.main_company_id','=',[auth()->user()->company_id]);
      })->select('consignee_id', \Illuminate\Support\Facades\DB::raw('count(consignee_id) as total'))->groupBy('consignee_id')->orderBy('total', 'desc')->limit(3)->get();
        

        if ($maxConsignee) {
            $this->data['maxConsignee'] = $maxConsignee;
        }

        /*$this->data['consignment']  = Consignment::count();
        $this->data['packages']     = Package::count();
        $this->data['items']        = Item::count();*/

        //$this->data['consignment']  = Consignment::where('company_id',auth()->user()->company_id)->count();
        $this->data['consignment'] = Consignment::join('companies', function ($join) {
          $join->on('consignments.company_id', '=', 'companies.id');
      })->select('consignment.*')
      ->where(function ($query) {
          $query->where('companies.id','=',[auth()->user()->company_id])
              ->orWhere('companies.main_company_id','=',[auth()->user()->company_id]);
      })->count();

        //$this->data['packages']     = Package::where('company_id',auth()->user()->company_id)->count();
        $this->data['packages'] = Package::join('companies', function ($join) {
          $join->on('packages.company_id', '=', 'companies.id');
      })->select('packages.*')
      ->where(function ($query) {
          $query->where('companies.id','=',[auth()->user()->company_id])
              ->orWhere('companies.main_company_id','=',[auth()->user()->company_id]);
      })->count();
        
        //$this->data['items']  = Item::where('company_id',auth()->user()->company_id)->count();
        $this->data['items'] = Item::join('companies', function ($join) {
          $join->on('items.company_id', '=', 'companies.id');
      })->select('items.*')
      ->where(function ($query) {
          $query->where('companies.id','=',[auth()->user()->company_id])
              ->orWhere('companies.main_company_id','=',[auth()->user()->company_id]);
      })->count();


        $companyInfo = Company::where('id',  auth()->user()->company_id)->first();
        $this->data['company_name'] = $companyInfo->name;

        $log_activity = LogActivity::with(['created_user', 'created_user.company'])->limit(6)->orderBy('id', 'desc')->get()->groupBy(function ($val) {
            return Carbon::parse($val->created_at)->format('d.m.Y');
        });
        if ($log_activity) {
            $this->data['log_activity'] = $log_activity;
        }
    }

    public function anaUreticiIndex()
    {
          $companies = Company::where('status', 1)
          ->where('main_company_id',auth()->user()->company_id)
          ->get();
        
        if ($companies) {
            $this->data['companies'] = $companies;
        }

        $consignments = Consignment::orderBy('created_at')->join('companies', function ($join) {
          $join->on('consignments.company_id', '=', 'companies.id');
            })->select('consignments.*')
            ->where(function ($query) {
                $query->where("consignments.created_at", ">", Carbon::now()->subMonths(6));
            })
            ->where(function ($query) {
                $query->where("companies.main_company_id", "=", auth()->user()->company_id);
            })
            ->get()
            ->groupBy(function ($val) {
                return \Carbon\Carbon::parse($val->created_at)->translatedFormat('F');
            });
        if ($consignments) {
            $this->data['consignments'] = $consignments;
        }

        $maxConsignments = Consignment::join('companies', function ($join) {
          $join->on('consignments.company_id', '=', 'companies.id');
        })->select('consignments.company_id', \Illuminate\Support\Facades\DB::raw('count(consignments.company_id) as total'))
        ->where(function ($query) {
          $query->where("companies.main_company_id", "=", auth()->user()->company_id);
        })
        ->groupBy('consignments.company_id')
        ->orderBy('total', 'desc')->limit(3)->get();
        if ($maxConsignments) {
            $this->data['maxConsignments'] = $maxConsignments;
        }

        $maxConsignee = Consignment::join('companies', function ($join) {
          $join->on('consignments.company_id', '=', 'companies.id');
        })->select('consignments.consignee_id', \Illuminate\Support\Facades\DB::raw('count(consignments.consignee_id) as total'))
        ->where(function ($query) {
          $query->where("companies.main_company_id", "=", auth()->user()->company_id);
        })
        ->groupBy('consignments.consignee_id')
        ->orderBy('total', 'desc')->limit(3)->get();
        if ($maxConsignee) {
            $this->data['maxConsignee'] = $maxConsignee;
        }

        $this->data['consignment'] = Consignment::join('companies', function ($join) {
          $join->on('consignments.company_id', '=', 'companies.id');
      })->select('consignment.id')
      ->where(function ($query) {
          $query->where('companies.main_company_id','=', auth()->user()->company_id);
      })->count();

        $this->data['packages'] = Package::join('companies', function ($join) {
          $join->on('packages.company_id', '=', 'companies.id');
      })->select('packages.id')
      ->where(function ($query) {
          $query->where('companies.main_company_id','=',auth()->user()->company_id);
      })->count();
        
        $this->data['items'] = Item::join('companies', function ($join) {
          $join->on('items.company_id', '=', 'companies.id');
      })->select('items.id')
      ->where(function ($query) {
          $query->where('companies.main_company_id','=',auth()->user()->company_id);
      })->count();

        $companyInfo = Company::where('id',  auth()->user()->company_id)->first();
        $this->data['company_name'] = $companyInfo->name;

        $log_activity = LogActivity::with(['created_user', 'created_user.company'])->limit(6)->orderBy('id', 'desc')->get()->groupBy(function ($val) {
            return Carbon::parse($val->created_at)->format('d.m.Y');
        });
        if ($log_activity) {
            $this->data['log_activity'] = $log_activity;
        }
        $companiesOfAnaUretici = Company::where('main_company_id', '=', auth()->user()->company_id)
            ->where(function ($query) {
                $query->where("companies.status", "=", 1);
            })->pluck('id');

          $this->data['companiesOfAnaUretici'] = $companiesOfAnaUretici->toArray();
    }

    public function companyIndex()
    {

      $companyInfo = Company::where('id',  auth()->user()->company_id)->first();
        $this->data['company_name'] = $companyInfo->name;

        $company_id = auth()->user()->company_id;

        $open_consignments = Consignment::where('company_id', $company_id)
            // ->where('status', true)
            ->orderBy('updated_at', 'desc')
            ->with(['consignee'])
            //->withCount('items')
            ->limit(20)
            ->get();

        if($open_consignments){
            $this->data['open_consignments'] = $open_consignments;
        }

        $consignments = Consignment::where('company_id', $company_id)
            ->where("created_at", ">", Carbon::now()->subMonths(12))
            ->orderBy('created_at')
            ->limit(20)
            ->get()
            ->groupBy(function ($val) {
                return Carbon::parse($val->created_at)->translatedFormat('F');
            });
        if ($consignments) {
            $this->data['consignments'] = $consignments;
        }

        //$company = Company::withCount(['consignments', 'packages', 'items', 'consignees', 'users', 'devices'])->find($company_id);
        $company = Company::withCount(['consignments', 'packages', 'consignees', 'users', 'devices'])->find($company_id);
        if ($company) {
            $this->data['company'] = $company;
        }

    }


  public function tableDataAjax()
  {
    $company_id = auth()->user()->company_id;

    $open_consignments = Consignment::where('company_id', $company_id)
    // ->where('status', true)
    ->orderBy('updated_at', 'desc')
    ->with(['consignee'])
    //->withCount('items')
    ->limit(20)
    ->get();

    $consignments = Consignment::where('company_id', $company_id)
    ->where("created_at", ">", Carbon::now()->subMonths(12))
    ->orderBy('created_at')
    ->limit(20)
    ->get()
    ->groupBy(function ($val) {
      return Carbon::parse($val->created_at)->translatedFormat('F');
    });
    if ($consignments) {
      $this->data['consignments'] = $consignments;
    }

    //$company = Company::withCount(['consignments', 'packages', 'items', 'consignees', 'users', 'devices'])->find($company_id);
    $company = Company::withCount(['consignments', 'packages', 'consignees', 'users', 'devices'])->find($company_id);
    if ($company) {
      $this->data['company'] = $company;
    }

    if($open_consignments){
      $this->data['open_consignments'] = $open_consignments;
    }
    foreach($open_consignments as $key => $value)
    {
      $comStatus = consignmentStatusPercent($value->items_count, $value->item_count);
      echo '<tr id="'.$value->id.'">
      <td><a href="'.route('consignment.show', $value->id).'">'.$value->name.'</a></td>';
      echo $value->consignee ? '<td>'.$value->consignee->name.'</td>' : '<td>-</td>';
      echo '<td>'.date('d-m-Y', strtotime($value->delivery_date)).'</td>';
      echo  $value->status == 1 ? '<td align="center"><span class="badge badge-success">Açık</span></td>' : '<td align="center"><span class="badge badge-danger">Kapalı</span></td>';
      echo '<td><div class="kt-widget__progress d-flex align-items-center"><div class="progress" style="height: 5px;width: 100%;"><div class="progress-bar '.$this->consignmentProgressBg($comStatus).'" role="progressbar" style="width: '.$comStatus. '%;" aria-valuenow="'.$comStatus.'" aria-valuemin="0" aria-valuemax="100"></div></div><span class="kt-widget__stats">%'.$comStatus.'</span></div></td>';
      if(roleCheck(config('settings.roles.anaUretici')))
      {
        echo '<td width="100" align="center"><span class="dropdown"><a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true"><i class="la la-cogs"></i></a><div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="'.route('consignment.show', $value->id).'"><i class="la la-search"></i>'.trans('portal.show').'</a><a class="dropdown-item" href="'.trans('consignment.status', $value->id).'"><i class="la la-truck"></i>'.trans('portal.close_consignment').'</a><a class="dropdown-item" href="'.route('consignment.edit', $value->id).'"><i class="la la-edit"></i>'.trans('portal.edit').'</a><a class="dropdown-item" href="'.route('consignment.destroy', $value->id).'" data-method="delete" data-token="'.csrf_token().'" data-confirm="'.trans('portal.delete_text').'"><i class="la la-trash"></i>'.trans('portal.delete').'</a></div></span></td>';
      }
    }
  }



  function consignmentProgressBg($percent)
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

  function consignmentStatusPercent($a = 0, $b = 0)
  {
    $x = 0;

    if($a != 0 && $b != 0){
      $x = ($a / $b) * 100;
    }else{
      if($a == 0){
        $x = 0;
      }else{
        $x = 100;
      }
    }

    return round($x);
  }

}
