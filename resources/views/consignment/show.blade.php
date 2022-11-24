@extends('layouts.main')

@section('content')


    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

        <!-- begin:: Subheader -->
        <div class="kt-subheader kt-grid__item" id="kt_subheader">
            <div class="kt-container  kt-container--fluid ">
                <div class="kt-subheader__main">
                    <h3 class="kt-subheader__title">
                        @lang('portal.consignments')
                    </h3>
                    <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                    <div class="kt-subheader__group" id="kt_subheader_search">
                        <span class="kt-subheader__desc" id="kt_subheader_total">{{ $consignment->name }}  </span>
                    </div>
                </div>
                <div class="kt-subheader__toolbar"></div>
            </div>
        </div>


        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

            <div class="row">
                <div class="col-xl-12">
                    <!--begin:: Portlet-->
                    <div class="kt-portlet kt-portlet--height-fluid">
                        <div class="kt-portlet__body kt-portlet__body--fit">
                            <!--begin::Widget -->
                            <div class="kt-widget kt-widget--project-1">
                                <div class="kt-widget__head">
                                    <div class="kt-widget__label">
                                        <div class="kt-widget__media">
                                            <span class="kt-media kt-media--lg kt-media--circle">
                                                <img
                                                    src="{{ config('settings.media.companies.full_path') .
                                                            $consignment->company->logo }}"
                                                    alt="image"
                                                >
                                            </span>
                                        </div>
                                        <div class="kt-widget__info kt-margin-t-5">
                                            <a href="#" class="kt-widget__title">
                                                {{ $consignment->company->name }}
                                                {!!
                                                    $consignment->company->status == true ?
                                                        '<i class="flaticon2-correct kt-font-success"></i>' :
                                                        '<i class="flaticon2-correct kt-font-danger"></i>'
                                                !!}
                                            </a>
                                            <span class="kt-widget__desc">
                                                {{ $consignment->name }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="kt-portlet__head-toolbar">
                                        <button
                                            type="button"
                                            class="btn btn-label-success btn-sm btn-upper"
                                            onclick="javascript:history.go(-1);"
                                        >
                                            <i class="la la-arrow-left"></i> @lang('portal.back')
                                        </button>
                                        <a
                                            href="#"
                                            class="btn btn-clean btn-sm btn-icon btn-icon-md"
                                            data-toggle="dropdown"
                                        >
                                            <i class="flaticon-more-1"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right">
                                            <ul class="kt-nav">
                                            @if(!(roleCheck(config('settings.roles.uretici')) && $consignment->status==false))
                                                <li class="kt-nav__item">
                                                    <a
                                                        href="{{ route('consignment.status', $consignment->id) }}"
                                                        class="kt-nav__link"
                                                    >
                                                        <i class="kt-nav__link-icon flaticon2-delivery-truck"></i>
                                                        <span class="kt-nav__link-text">
                                                            {{
                                                                $consignment->status == false ?
                                                                    trans('portal.open_consignmet') :
                                                                    trans('portal.close_consignmet')
                                                            }}
                                                        </span>
                                                    </a>
                                                </li>
                                                @endif
                                                <li class="kt-nav__item">
                                                    <a
                                                        href="{{ route('consignment.edit', $consignment->id) }}"
                                                        class="kt-nav__link"
                                                    >
                                                        <i class="kt-nav__link-icon flaticon2-contract"></i>
                                                        <span class="kt-nav__link-text">@lang('portal.edit')</span>
                                                    </a>
                                                </li>
                                                <li class="kt-nav__item">
                                                    <a
                                                        href="{{ route('consignment.destroy', $consignment->id) }}"
                                                        class="kt-nav__link"
                                                        data-method="delete"
                                                        data-token="{{csrf_token()}}"
                                                        data-confirm="@lang('portal.delete_text')"
                                                    >
                                                        <i class="kt-nav__link-icon flaticon2-trash"></i>
                                                        <span class="kt-nav__link-text">@lang('portal.delete')</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="kt-widget__body">
                                    <div class="kt-widget__stats">
                                        <div class="kt-widget__item">
                                            <span class="kt-widget__date">
                                                @lang('portal.create_date')
                                            </span>
                                            <div class="kt-widget__label">
                                                <span class="btn btn-label-brand btn-sm btn-bold btn-upper">
                                                    {{ date('d-m-Y', strtotime($consignment->created_at)) }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="kt-widget__item">
                                            <span class="kt-widget__date">
                                                @lang('portal.delivery_date')
                                            </span>
                                            <div class="kt-widget__label">
                                                <span class="btn btn-label-danger btn-sm btn-bold btn-upper">
                                                    {{ empty($consignment->delivery_date) ? '-' : date('d-m-Y', strtotime($consignment->delivery_date)) }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="kt-widget__item flex-fill" id="consingnmentStatus">
                                            <span class="kt-widget__subtitel">@lang('portal.consignmet_status')</span>
                                            <div class="kt-widget__progress d-flex  align-items-center">
                                                @php
                                                    $conStatus = consignmentStatusPercent(
                                                        $consignment->items_count, $consignment->item_count
                                                    );
                                                @endphp
                                                <div class="progress" style="height: 5px;width: 100%;">
                                                    <div
                                                        class="progress-bar {{ consignmentProgressBg($conStatus) }}"
                                                        role="progressbar"
                                                        style="width: {{ $conStatus }}%;"
                                                        aria-valuenow="100"
                                                        aria-valuemin="0"
                                                        aria-valuemax="100"
                                                    ></div>
                                                </div>
                                                <span class="kt-widget__stat">
                                                    {{ $conStatus }}%
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <span class="kt-widget__text">
                                        @lang('portal.consignment_text')
                                    </span>

                                    <div class="kt-widget__content">
                                        <div class="kt-widget__details">
                                            <span class="kt-widget__subtitle">@lang('portal.order_code')</span>
                                            <span class="kt-widget__value">
                                                <span class="fa fa-truck-moving"></span>
                                                {{ $consignment->order->order_code }}
                                            </span>
                                        </div>
                                        {{--
                                            <div class="kt-widget__details">
                                            <span class="kt-widget__subtitle">@lang('portal.plate')</span>
                                            <span class="kt-widget__value">
                                                <span class="fa fa-truck-moving"></span>
                                                {{ $consignment->plate_no != '' ? $consignment->plate_no : '-' }}
                                            </span>
                                        </div>
                                         --}}
                                        <div class="kt-widget__details">
                                            <span class="kt-widget__subtitle">@lang('portal.number_orders')</span>
                                            <span class="kt-widget__value">
                                                <span class="fa fa-tags"></span>
                                                {{ number_format($consignment->item_count) }} @lang('portal.piece')
                                            </span>
                                        </div>

                                        <div class="kt-widget__details">
                                            <span class="kt-widget__subtitle">@lang('portal.number_read')</span>
                                            <span class="kt-widget__value" id="items_count">
                                                <span class="fa fa-tags"></span>
                                                {{ number_format($consignment->items_count) }} @lang('portal.piece')
                                            </span>
                                        </div>
                                        <div class="kt-widget__details">
                                            <span class="kt-widget__subtitle">@lang('portal.consignee_name')</span>
                                            <span class="kt-widget__value">
                                                <span class="fa fa-building"> </span>
                                                {{  $consignment->consignee ? $consignment->consignee->name : '-' }}
                                            </span>
                                        </div>
                                        <div class="kt-widget__details">
                                            <span class="kt-widget__subtitle">@lang('portal.delivery_date')</span>
                                            <span class="kt-widget__value">
                                                <span class="fa fa-calendar-alt"> </span>
                                               {{ empty($consignment->delivery_date) ? '-' : date('d-m-Y', strtotime($consignment->delivery_date)) }}
                                            </span>
                                        </div>
                                        <div class="kt-widget__details">
                                            <span class="kt-widget__subtitle">@lang('portal.creator_user')</span>
                                            <span class="kt-widget__value">
                                                <span class="fa fa-user"> </span>
                                                {{  $consignment->created_user ? $consignment->created_user->username : '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div id="loader" class="center"></div>
                                <div class="kt-widget__footer p-3">

                                    <div class="row">
                                        {{-- consignee HM ise raporları göster viewid 2 hm --}}
                                        @if ($consignment->consignee->viewid == 2)
                                        <div class="col-12 col-md-6 col-lg-3">
                                            <div class="kt-portlet kt-iconbox kt-iconbox--brand kt-iconbox--animate-slow">
                                                <div class="kt-portlet__body" style="padding: 25px 10px;">
                                                    <div class="kt-iconbox__body">
                                                        <div class="kt-iconbox__icon">
                                                            <svg
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                width="24px"
                                                                height="24px"
                                                                viewBox="0 0 24 24"
                                                                version="1.1"
                                                                class="kt-svg-icon"
                                                            >
                                                                <g
                                                                    stroke="none"
                                                                    stroke-width="1"
                                                                    fill="none"
                                                                    fill-rule="evenodd"
                                                                >
                                                                    <rect id="bound" x="0" y="0" width="24" height="24"/>
                                                                    <path
                                                                        d="
                                                                            M7.83498136,4
                                                                            C8.22876115,5.21244017
                                                                            9.94385174,6.125
                                                                            11.999966,6.125
                                                                            C14.0560802,6.125
                                                                            15.7711708,5.21244017
                                                                            16.1649506,4
                                                                            L17.2723671,4
                                                                            C17.3446978,3.99203791
                                                                            17.4181234,3.99191839
                                                                            17.4913059,4
                                                                            L17.5,4
                                                                            C17.8012164,4
                                                                            18.0713275,4.1331782
                                                                            18.2546625,4.34386406
                                                                            L22.5900048,6.8468751
                                                                            C23.0682974,7.12301748
                                                                            23.2321726,7.73460788
                                                                            22.9560302,8.21290051
                                                                            L21.2997802,11.0816097
                                                                            C21.0236378,11.5599023
                                                                            20.4120474,11.7237774
                                                                            19.9337548,11.4476351
                                                                            L18.5,10.6198563
                                                                            L18.5,20
                                                                            C18.5,20.5522847
                                                                            18.0522847,21
                                                                            17.5,21 L6.5,21
                                                                            C5.94771525,21
                                                                            5.5,20.5522847
                                                                            5.5,20
                                                                            L5.5,10.6204852
                                                                            L4.0673344,11.4476351
                                                                            C3.58904177,11.7237774
                                                                            2.97745137,11.5599023
                                                                            2.70130899,11.0816097
                                                                            L1.04505899,8.21290051
                                                                            C0.768916618,7.73460788
                                                                            0.932791773,7.12301748
                                                                            1.4110844,6.8468751
                                                                            L5.74424153,4.34512566
                                                                            C5.92759515,4.13371
                                                                            6.19818276,4
                                                                            6.5,4
                                                                            L6.50978325,4
                                                                            C6.58296578,3.99191839
                                                                            6.65639143,3.99203791
                                                                            6.72872211,4
                                                                            L7.83498136,4
                                                                            Z
                                                                        "
                                                                        id="Combined-Shape"
                                                                        fill="#000000"
                                                                    />
                                                                </g>
                                                            </svg>
                                                        </div>
                                                        <div class="kt-iconbox__desc">
                                                            <h3 class="kt-iconbox__title">
                                                                <span
                                                                    class="kt-link"
                                                                    onclick="
                                                                        GetPDF(
                                                                            '{{ route('reports.hnm.pdf', $consignment->id) }}',
                                                                            '@lang('portal.package_list')')
                                                                    "
                                                                >
                                                                    @lang('portal.hm_report')
                                                                </span>
                                                            </h3>
                                                            <div class="kt-iconbox__content">
                                                                @lang('portal.hm_report_text')
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        {{--
                                            MS için model raporu yönlendirilmesi yapılıyor. viewid 3 ms
                                            decathlon icin yeni model eklenicek
                                        --}}
                                        @php
                                            if ($consignment->consignee->viewid == 7){
                                                $packageLink = 'reports.package.pdf';
                                                $modelLink = 'reports.modelLevis.pdf';
                                                $epcLink = 'reports.epc.pdf';
                                                $deleteLink = 'reports.deleted-package.pdf';
                                            }elseif ($consignment->consignee->viewid == 5){
                                                $packageLink = 'reports.package.pdf';
                                                $modelLink = 'reports.modelHb.pdf';
                                                $epcLink = 'reports.epc.pdf';
                                                $deleteLink = 'reports.deleted-package.pdf';
                                            }elseif ($consignment->consignee->viewid == 4){
                                                $packageLink = 'reports.package.pdf';
                                                $modelLink = 'reports.model.pdf';
                                                $epcLink = 'reports.epc.pdf';
                                                $deleteLink = 'reports.deleted-package.pdf';
                                            }elseif ($consignment->consignee->viewid == 3){
                                                $packageLink = 'reports.packageMs.pdf';
                                                $modelLink = 'reports.modelMs.pdf';
                                                $epcLink = 'reports.epcMs.pdf';
                                                $deleteLink = 'reports.deleted-package-ms.pdf';
                                            }elseif ($consignment->consignee->viewid == 2){
                                                $packageLink = 'reports.package.pdf';
                                                $modelLink = 'reports.modelHm.pdf';
                                                $epcLink = 'reports.epc.csv';
                                                $deleteLink = 'reports.deleted-package.pdf';
                                            }else{
                                                $packageLink = 'reports.package.pdf';
                                                $modelLink = 'reports.model.pdf';
                                                $epcLink = 'reports.epc.pdf';
                                                $deleteLink = 'reports.deleted-package.pdf';
                                            }
                                        @endphp

                                        <div class="col-12 col-md-6 col-lg-3">
                                            <div class="kt-portlet kt-iconbox kt-iconbox--success kt-iconbox--animate-slow">
                                                <div class="kt-portlet__body" style="padding: 25px 10px;">
                                                    <div class="kt-iconbox__body">
                                                        <div class="kt-iconbox__icon">
                                                            <svg
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                width="24px"
                                                                height="24px"
                                                                viewBox="0 0 24 24"
                                                                version="1.1"
                                                                class="kt-svg-icon"
                                                            >
                                                                <g
                                                                    stroke="none"
                                                                    stroke-width="1"
                                                                    fill="none"
                                                                    fill-rule="evenodd"
                                                                >
                                                                    <rect id="bound" x="0" y="0" width="24" height="24"/>
                                                                    <path
                                                                        d="
                                                                            M4,9.67471899
                                                                            L10.880262,13.6470401
                                                                            C10.9543486,13.689814
                                                                            11.0320333,13.7207107
                                                                            11.1111111,13.740321
                                                                            L11.1111111,21.4444444
                                                                            L4.49070127,17.526473
                                                                            C4.18655139,17.3464765
                                                                            4,17.0193034 4,16.6658832
                                                                            L4,9.67471899
                                                                            Z
                                                                            M20,9.56911707
                                                                            L20,16.6658832
                                                                            C20,17.0193034
                                                                            19.8134486,17.3464765
                                                                            19.5092987,17.526473
                                                                            L12.8888889,21.4444444
                                                                            L12.8888889,13.6728275
                                                                            C12.9050191,13.6647696
                                                                            12.9210067,13.6561758
                                                                            12.9368301,13.6470401
                                                                            L20,9.56911707
                                                                            Z
                                                                        "
                                                                        id="Combined-Shape"
                                                                        fill="#000000"
                                                                    />
                                                                    <path
                                                                        d="
                                                                            M4.21611835,7.74669402
                                                                            C4.30015839,7.64056877
                                                                            4.40623188,7.55087574
                                                                            4.5299008,7.48500698
                                                                            L11.5299008,3.75665466
                                                                            C11.8237589,3.60013944
                                                                            12.1762411,3.60013944
                                                                            12.4700992,3.75665466
                                                                            L19.4700992,7.48500698
                                                                            C19.5654307,7.53578262
                                                                            19.6503066,7.60071528
                                                                            19.7226939,7.67641889
                                                                            L12.0479413,12.1074394
                                                                            C11.9974761,12.1365754
                                                                            11.9509488,12.1699127
                                                                            11.9085461,12.2067543
                                                                            C11.8661433,12.1699127
                                                                            11.819616,12.1365754
                                                                            11.7691509,12.1074394
                                                                            L4.21611835,7.74669402
                                                                            Z
                                                                        "
                                                                        id="Path"
                                                                        fill="#000000"
                                                                        opacity="0.3"
                                                                    />
                                                                </g>
                                                            </svg>
                                                        </div>
                                                        <div class="kt-iconbox__desc">
                                                            <h3 class="kt-iconbox__title">
                                                                <span
                                                                    class="kt-link"
                                                                    onclick="
                                                                        GetPDF(
                                                                            '{{ route($packageLink, $consignment->id) }}',
                                                                            '@lang('portal.package_list')'
                                                                        )
                                                                    "
                                                                >
                                                                    @lang('portal.package_report')
                                                                </span><br>
                                                            </h3>
                                                            <div class="kt-iconbox__content">
                                                                @lang('portal.package_report_text')
                                                                <br>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-3">
                                            <div class="kt-portlet kt-iconbox kt-iconbox--brand kt-iconbox--animate-slow">
                                                <div class="kt-portlet__body" style="padding: 25px 10px;">
                                                    <div class="kt-iconbox__body">
                                                        <div class="kt-iconbox__icon">
                                                            <svg
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                width="24px"
                                                                height="24px"
                                                                viewBox="0 0 24 24"
                                                                version="1.1"
                                                                class="kt-svg-icon"
                                                            >
                                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                    <rect id="bound" x="0" y="0" width="24" height="24"/>
                                                                    <path
                                                                        d="
                                                                            M7.83498136,4
                                                                            C8.22876115,5.21244017
                                                                            9.94385174,6.125
                                                                            11.999966,6.125
                                                                            C14.0560802,6.125
                                                                            15.7711708,5.21244017
                                                                            16.1649506,4
                                                                            L17.2723671,4
                                                                            C17.3446978,3.99203791
                                                                            17.4181234,3.99191839
                                                                            17.4913059,4
                                                                            L17.5,4
                                                                            C17.8012164,4
                                                                            18.0713275,4.1331782
                                                                            18.2546625,4.34386406
                                                                            L22.5900048,6.8468751
                                                                            C23.0682974,7.12301748
                                                                            23.2321726,7.73460788
                                                                            22.9560302,8.21290051
                                                                            L21.2997802,11.0816097
                                                                            C21.0236378,11.5599023
                                                                            20.4120474,11.7237774
                                                                            19.9337548,11.4476351
                                                                            L18.5,10.6198563
                                                                            L18.5,20 C18.5,20.5522847
                                                                            18.0522847,21
                                                                            17.5,21
                                                                            L6.5,21
                                                                            C5.94771525,21
                                                                            5.5,20.5522847
                                                                            5.5,20
                                                                            L5.5,10.6204852
                                                                            L4.0673344,11.4476351
                                                                            C3.58904177,11.7237774
                                                                            2.97745137,11.5599023
                                                                            2.70130899,11.0816097
                                                                            L1.04505899,8.21290051
                                                                            C0.768916618,7.73460788
                                                                            0.932791773,7.12301748
                                                                            1.4110844,6.8468751
                                                                            L5.74424153,4.34512566
                                                                            C5.92759515,4.13371
                                                                            6.19818276,4 6.5,4
                                                                            L6.50978325,4
                                                                            C6.58296578,3.99191839
                                                                            6.65639143,3.99203791
                                                                            6.72872211,4
                                                                            L7.83498136,4
                                                                            Z
                                                                        "
                                                                        id="Combined-Shape"
                                                                        fill="#000000"
                                                                    />
                                                                </g>
                                                            </svg>
                                                        </div>
                                                        <div class="kt-iconbox__desc">
                                                            <h3 class="kt-iconbox__title">
                                                                <span
                                                                    class="kt-link"
                                                                    onclick="
                                                                        GetPDF(
                                                                            '{{ route($modelLink, $consignment->id) }}',
                                                                            '@lang('portal.model_list')'
                                                                        )
                                                                    "
                                                                >
                                                                    @lang('portal.model_report')
                                                                </span>
                                                            </h3>
                                                            <div class="kt-iconbox__content">
                                                                @lang('portal.model_report_text')
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- HM için epc raporları csv olarak indiriliyor. --}}
                                        <div class="col-12 col-md-6 col-lg-3">
                                            <div class="kt-portlet kt-iconbox kt-iconbox--warning kt-iconbox--animate-slow">
                                                <div class="kt-portlet__body" style="padding: 25px 10px;">
                                                    <div class="kt-iconbox__body">
                                                        <div class="kt-iconbox__icon">
                                                            <svg
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                width="24px"
                                                                height="24px"
                                                                viewBox="0 0 24 24"
                                                                version="1.1"
                                                                class="kt-svg-icon"
                                                            >
                                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                    <rect id="bound" x="0" y="0" width="24" height="24"/>
                                                                    <rect
                                                                        id="Rectangle-151"
                                                                        fill="#000000"
                                                                        opacity="0.3"
                                                                        x="4"
                                                                        y="4"
                                                                        width="8"
                                                                        height="16"
                                                                    />
                                                                    <path
                                                                        d="
                                                                            M6,18
                                                                            L9,18
                                                                            C9.66666667,18.1143819
                                                                            10,18.4477153
                                                                            10,19
                                                                            C10,19.5522847
                                                                            9.66666667,19.8856181
                                                                            9,20
                                                                            L4,20
                                                                            L4,15
                                                                            C4,14.3333333
                                                                            4.33333333,14
                                                                            5,14
                                                                            C5.66666667,14
                                                                            6,14.3333333
                                                                            6,15
                                                                            L6,18
                                                                            Z
                                                                            M18,18
                                                                            L18,15
                                                                            C18.1143819,14.3333333
                                                                            18.4477153,14
                                                                            19,14
                                                                            C19.5522847,14
                                                                            19.8856181,14.3333333
                                                                            20,15
                                                                            L20,20
                                                                            L15,20
                                                                            C14.3333333,20
                                                                            14,19.6666667
                                                                            14,19
                                                                            C14,18.3333333
                                                                            14.3333333,18
                                                                            15,18
                                                                            L18,18
                                                                            Z
                                                                            M18,6
                                                                            L15,6
                                                                            C14.3333333,5.88561808
                                                                            14,5.55228475
                                                                            14,5
                                                                            C14,4.44771525
                                                                            14.3333333,4.11438192
                                                                            15,4
                                                                            L20,4
                                                                            L20,9
                                                                            C20,9.66666667
                                                                            19.6666667,10
                                                                            19,10
                                                                            C18.3333333,10
                                                                            18,9.66666667
                                                                            18,9
                                                                            L18,6
                                                                            Z M6,6
                                                                            L6,9
                                                                            C5.88561808,9.66666667
                                                                            5.55228475,10
                                                                            5,10
                                                                            C4.44771525,10
                                                                            4.11438192,9.66666667
                                                                            4,9
                                                                            L4,4
                                                                            L9,4
                                                                            C9.66666667,4
                                                                            10,4.33333333
                                                                            10,5
                                                                            C10,5.66666667
                                                                            9.66666667,6
                                                                            9,6
                                                                            L6,6
                                                                            Z
                                                                        "
                                                                        id="Combined-Shape"
                                                                        fill="#000000"
                                                                        fill-rule="nonzero"
                                                                    />
                                                                </g>
                                                            </svg>
                                                        </div>
                                                        <div class="kt-iconbox__desc">
                                                            <h3 class="kt-iconbox__title">
                                                                <a href="{{ route($epcLink, $consignment->id) }}">
                                                                    <span class="kt-link">
                                                                        @lang('portal.epc_report')
                                                                    </span>
                                                                </a>
                                                            </h3>
                                                            <div class="kt-iconbox__content">
                                                                @lang('portal.epc_report_text')
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Sevkiyat kapalıysa ve consignee HM ise raporları göster viewid 2 hm--}}
                                        @if ($consignment->status == false && $consignment->consignee->viewid == 2)
                                        <div class="col-12 col-md-6 col-lg-3">
                                            <div class="kt-portlet kt-iconbox kt-iconbox--warning kt-iconbox--animate-slow">
                                                <div class="kt-portlet__body" style="padding: 25px 10px;">
                                                    <div class="kt-iconbox__body">
                                                        <div class="kt-iconbox__icon">
                                                            <svg
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                width="24px"
                                                                height="24px"
                                                                viewBox="0 0 24 24"
                                                                version="1.1"
                                                                class="kt-svg-icon"
                                                            >
                                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                    <rect id="bound" x="0" y="0" width="24" height="24"/>
                                                                    <rect
                                                                        id="Rectangle-151"
                                                                        fill="#000000"
                                                                        opacity="0.3"
                                                                        x="4"
                                                                        y="4"
                                                                        width="8"
                                                                        height="16"
                                                                    />
                                                                    <path
                                                                        d="
                                                                            M6,18
                                                                            L9,18
                                                                            C9.66666667,18.1143819
                                                                            10,18.4477153
                                                                            10,19
                                                                            C10,19.5522847
                                                                            9.66666667,19.8856181
                                                                            9,20
                                                                            L4,20
                                                                            L4,15
                                                                            C4,14.3333333
                                                                            4.33333333,14
                                                                            5,14
                                                                            C5.66666667,14
                                                                            6,14.3333333
                                                                            6,15
                                                                            L6,18
                                                                            Z
                                                                            M18,18
                                                                            L18,15
                                                                            C18.1143819,14.3333333
                                                                            18.4477153,14
                                                                            19,14
                                                                            C19.5522847,14
                                                                            19.8856181,14.3333333
                                                                            20,15
                                                                            L20,20
                                                                            L15,20
                                                                            C14.3333333,20
                                                                            14,19.6666667
                                                                            14,19
                                                                            C14,18.3333333
                                                                            14.3333333,18
                                                                            15,18
                                                                            L18,18
                                                                            Z
                                                                            M18,6
                                                                            L15,6
                                                                            C14.3333333,5.88561808
                                                                            14,5.55228475
                                                                            14,5
                                                                            C14,4.44771525
                                                                            14.3333333,4.11438192
                                                                            15,4
                                                                            L20,4
                                                                            L20,9
                                                                            C20,9.66666667
                                                                            19.6666667,10
                                                                            19,10
                                                                            C18.3333333,10
                                                                            18,9.66666667
                                                                            18,9
                                                                            L18,6
                                                                            Z M6,6
                                                                            L6,9
                                                                            C5.88561808,9.66666667
                                                                            5.55228475,10
                                                                            5,10
                                                                            C4.44771525,10
                                                                            4.11438192,9.66666667
                                                                            4,9
                                                                            L4,4
                                                                            L9,4
                                                                            C9.66666667,4
                                                                            10,4.33333333
                                                                            10,5
                                                                            C10,5.66666667
                                                                            9.66666667,6
                                                                            9,6
                                                                            L6,6
                                                                            Z
                                                                        "
                                                                        id="Combined-Shape"
                                                                        fill="#000000"
                                                                        fill-rule="nonzero"
                                                                    />
                                                                </g>
                                                            </svg>
                                                        </div>
                                                        <div class="kt-iconbox__desc">
                                                            <h3 class="kt-iconbox__title">
                                                                <a href="{{ route('reports.epc.pdfAsc', $consignment->id) }}">
                                                                    <span class="kt-link">
                                                                        @lang('portal.epc_karsilastirilmis')
                                                                    </span>
                                                                </a>
                                                            </h3>
                                                            <div class="kt-iconbox__content">
                                                                @lang('portal.epc_karsilastirilmis_text')
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-3">
                                            <div class="kt-portlet kt-iconbox kt-iconbox--brand kt-iconbox--animate-slow">
                                                <div class="kt-portlet__body" style="padding: 25px 10px;">
                                                    <div class="kt-iconbox__body">
                                                        <div class="kt-iconbox__icon">
                                                            <svg
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                width="24px"
                                                                height="24px"
                                                                viewBox="0 0 24 24"
                                                                version="1.1"
                                                                class="kt-svg-icon"
                                                            >
                                                                <g
                                                                    stroke="none"
                                                                    stroke-width="1"
                                                                    fill="none"
                                                                    fill-rule="evenodd"
                                                                >
                                                                    <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                                                                    <path
                                                                        d="
                                                                            M2.56066017,10.6819805
                                                                            L4.68198052,8.56066017
                                                                            C5.26776695,7.97487373
                                                                            6.21751442,7.97487373
                                                                            6.80330086,8.56066017
                                                                            L8.9246212,10.6819805
                                                                            C9.51040764,11.267767
                                                                            9.51040764,12.2175144
                                                                            8.9246212,12.8033009
                                                                            L6.80330086,14.9246212
                                                                            C6.21751442,15.5104076
                                                                            5.26776695,15.5104076
                                                                            4.68198052,14.9246212
                                                                            L2.56066017,12.8033009
                                                                            C1.97487373,12.2175144
                                                                            1.97487373,11.267767
                                                                            2.56066017,10.6819805
                                                                            Z
                                                                            M14.5606602,10.6819805
                                                                            L16.6819805,8.56066017
                                                                            C17.267767,7.97487373
                                                                            18.2175144,7.97487373
                                                                            18.8033009,8.56066017
                                                                            L20.9246212,10.6819805
                                                                            C21.5104076,11.267767
                                                                            21.5104076,12.2175144
                                                                            20.9246212,12.8033009
                                                                            L18.8033009,14.9246212
                                                                            C18.2175144,15.5104076
                                                                            17.267767,15.5104076
                                                                            16.6819805,14.9246212
                                                                            L14.5606602,12.8033009
                                                                            C13.9748737,12.2175144
                                                                            13.9748737,11.267767
                                                                            14.5606602,10.6819805
                                                                            Z
                                                                        "
                                                                        id="Combined-Shape"
                                                                        fill="#000000"
                                                                        opacity="0.3"
                                                                    ></path>
                                                                    <path
                                                                        d="
                                                                            M8.56066017,16.6819805
                                                                            L10.6819805,14.5606602
                                                                            C11.267767,13.9748737
                                                                            12.2175144,13.9748737
                                                                            12.8033009,14.5606602
                                                                            L14.9246212,16.6819805
                                                                            C15.5104076,17.267767
                                                                            15.5104076,18.2175144
                                                                            14.9246212,18.8033009
                                                                            L12.8033009,20.9246212
                                                                            C12.2175144,21.5104076
                                                                            11.267767,21.5104076
                                                                            10.6819805,20.9246212
                                                                            L8.56066017,18.8033009
                                                                            C7.97487373,18.2175144
                                                                            7.97487373,17.267767
                                                                            8.56066017,16.6819805
                                                                            Z
                                                                            M8.56066017,4.68198052
                                                                            L10.6819805,2.56066017
                                                                            C11.267767,1.97487373
                                                                            12.2175144,1.97487373
                                                                            12.8033009,2.56066017
                                                                            L14.9246212,4.68198052
                                                                            C15.5104076,5.26776695
                                                                            15.5104076,6.21751442
                                                                            14.9246212,6.80330086
                                                                            L12.8033009,8.9246212
                                                                            C12.2175144,9.51040764
                                                                            11.267767,9.51040764
                                                                            10.6819805,8.9246212
                                                                            L8.56066017,6.80330086
                                                                            C7.97487373,6.21751442
                                                                            7.97487373,5.26776695
                                                                            8.56066017,4.68198052
                                                                            Z
                                                                        "
                                                                        id="Combined-Shape"
                                                                        fill="#000000"
                                                                    ></path>
                                                                </g>
                                                            </svg>
                                                        </div>
                                                        <div class="kt-iconbox__desc">
                                                            <h3 class="kt-iconbox__title">
                                                                <a href="{{ route('reports.epc.pdfCheck', $consignment->id) }}">
                                                                    <span class="kt-link">@lang('portal.epc_check')</span>
                                                                </a>
                                                            </h3>
                                                            <div class="kt-iconbox__content">
                                                                @lang('portal.epc_check_text')
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-12 col-md-6 col-lg-3">
                                            <div class="kt-portlet kt-iconbox kt-iconbox--danger kt-iconbox--animate-slow">
                                                <div class="kt-portlet__body" style="padding: 25px 10px;">
                                                    <div class="kt-iconbox__body">
                                                        <div class="kt-iconbox__icon">
                                                            <svg
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                width="24px"
                                                                height="24px"
                                                                viewBox="0 0 24 24"
                                                                version="1.1"
                                                                class="kt-svg-icon"
                                                            >
                                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                    <rect id="bound" x="0" y="0" width="24" height="24"/>
                                                                    <path
                                                                        d="
                                                                            M6,8
                                                                            L6,20.5
                                                                            C6,21.3284271
                                                                            6.67157288,22
                                                                            7.5,22
                                                                            L16.5,22
                                                                            C17.3284271,22
                                                                            18,21.3284271
                                                                            18,20.5
                                                                            L18,8
                                                                            L6,8
                                                                            Z
                                                                        "
                                                                        id="round"
                                                                        fill="#000000"
                                                                        fill-rule="nonzero"
                                                                    />
                                                                    <path
                                                                        d="
                                                                            M14,4.5
                                                                            L14,4
                                                                            C14,3.44771525
                                                                            13.5522847,3
                                                                            13,3
                                                                            L11,3
                                                                            C10.4477153,3
                                                                            10,3.44771525
                                                                            10,4
                                                                            L10,4.5
                                                                            L5.5,4.5
                                                                            C5.22385763,4.5
                                                                            5,4.72385763
                                                                            5,5
                                                                            L5,5.5
                                                                            C5,5.77614237
                                                                            5.22385763,6
                                                                            5.5,6
                                                                            L18.5,6
                                                                            C18.7761424,6
                                                                            19,5.77614237
                                                                            19,5.5
                                                                            L19,5
                                                                            C19,4.72385763
                                                                            18.7761424,4.5
                                                                            18.5,4.5
                                                                            L14,4.5
                                                                            Z
                                                                        "
                                                                        id="Shape"
                                                                        fill="#000000"
                                                                        opacity="0.3"
                                                                    />
                                                                </g>
                                                            </svg>
                                                        </div>
                                                        <div class="kt-iconbox__desc">
                                                            <h3 class="kt-iconbox__title">
                                                                <span
                                                                    class="kt-link"
                                                                    onclick="
                                                                        GetPDF(
                                                                            '{{ route($deleteLink, $consignment->id) }}',
                                                                            '@lang('portal.deleted_package_list')'
                                                                        )
                                                                    "
                                                                >
                                                                    @lang('portal.deleted_report')
                                                                </span>
                                                            </h3>
                                                            <div class="kt-iconbox__content">
                                                                @lang('portal.deleted_report_text')
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <!--end::Widget -->
                        </div>
                    </div>
                    <!--end:: Portlet-->
                </div>
            </div>

            <div class="kt-portlet kt-portlet--height-fluid">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            @lang('portal.consignmet_details')
                        </h3>
                    </div>
                    <div class="kt-portlet__head-toolbar">
                    </div>
                </div>
                <div class="kt-portlet__body">
                    {{--gorunumlere gore dataTable tipleri değiştiriliyor--}}
                    @php
                        $gorunumId = $consignment->consignee->viewid;
                    @endphp

                    @if ($gorunumId == 1)
                        @include('consignment.dataTableZara')
                    @elseif($gorunumId == 2)
                        @include('consignment.dataTableHm')
                    @elseif($gorunumId == 3)
                        @include('consignment.dataTableMs')
                    @elseif($gorunumId == 4)
                        {{--decatchlon icin duzenlenecek--}}
                        @include('consignment.dataTableZara')
                    @elseif($gorunumId == 5)
                        @include('consignment.dataTableZara')
                    @elseif($gorunumId == 7)
                        @include('consignment.dataTableLevis')
                    @else
                        @include('consignment.dataTableZara')
                    @endif

                </div>
            </div>


        </div>
    </div>


@endsection

@section('css')

    <link href="/assets/vendors/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
    <style>
        td.details-control {
            background: url('/assets/media/icons/details_open.png') no-repeat center center;
            cursor: pointer;
        }
        tr.shown td.details-control {
            background: url('/assets/media/icons/details_close.png') no-repeat center center;
        }
        .kt-link{
            cursor: pointer;
        }
        #loader {
            border: 12px solid #f3f3f3;
            border-radius: 50%;
            border-top: 12px solid #444444;
            width: 70px;
            height: 70px;
            animation: spin 1s linear infinite;
            z-index: 5;
            display: none;
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }

        .center {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            margin: auto;
        }
    </style>

@endsection

@section('js')
    <script src="/assets/vendors/custom/datatables/datatables.bundle.js" type="text/javascript"></script>
    <script type="text/javascript">

        function GetPDF(pdfUrl, pdfType){

            var data = '';
            @if ($consignment->consignee->viewid == 3)
                var pdf = "{{ $consignment->name }} - " + pdfType + ".xlsx";
            @else
                var pdf = "{{ $consignment->name }} - " + pdfType + ".pdf";
            @endif
            $.ajax({
                url: pdfUrl,
                type: 'get',
                data: data,
                xhrFields: {
                    responseType: 'blob'
                },
                beforeSend: function(){
                    // Show image container
                    $("#loader").show();
                },
                success: function(response){
                    var blob = new Blob([response]);
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = pdf;
                    link.click();
                },
                error: function(blob){
                    console.log(blob);
                },
                complete:function(){
                    // Hide image container
                    $("#loader").hide();
                }
            });
        }

        $(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Zara gorunumu icin
            var table = $('#consignmentList').DataTable({
                responsive : true,
                processing : true,
                serverSide : true,
                searching : false,
                destroy : true,
                // dataSrc : "data",
                ajax : {
                    "url" : "{{ route('consignment.packageZara') }}",
                    "type" : "GET",
                    "datatype": 'json',
                    "data" : function (d) {
                        d.consignmentId = {{ $consignment->id }};
                        d._token = '{{ csrf_token() }}';
                    },
                },
                columns : [
                    {
                        "className" : 'details-control',
                        "orderable": false,
                        "data": null,
                        "defaultContent": '',
                    },
                    {data : 'package_no', name : 'package_no'},
                    {data : 'items', name:'items'},
                    {data : 'status', name : 'status'},
                    {data : 'desc', name : 'desc', },
                    {data : 'size', name : 'size'},
                    {data : 'created_user_id', name : 'created_user'},
                    {data : 'created_at', name : 'created_at'},
                ],
                @if(app()->getLocale() == 'tr')
                language: {
                    "url": "{{ asset('/assets/vendors/custom/datatables/locale/tr.json') }}"
                },
                @endif
                order : [],
                initComplete : function () {}
            });
            $('#consignmentList tbody').on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                var row = table.row( tr );

                if ( row.child.isShown() ) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ route('ajax') }}",
                        data: {process: 'getItems', packageId: row.data().id},
                        beforeSend: function () {},
                        success: function (data) {
                            row.child(format(data)).show();
                            tr.addClass('shown');
                        },
                        complete: function() {}
                    });
                }
            } );

            // HM gorunumu icin
            var tableHm = $('#consignmentListHm').DataTable({
                responsive : true,
                processing : true,
                serverSide : true,
                searching : false,
                destroy : true,
                // dataSrc : "data",
                ajax : {
                    "url" : "{{ route('consignment.packageHm') }}",
                    "type" : "GET",
                    "datatype": 'json',
                    "data" : function (d) {
                        d.consignmentId = {{ $consignment->id }};
                        d._token = '{{ csrf_token() }}';
                    },
                },
                columns : [
                    {
                        "className" : 'details-control',
                        "orderable": false,
                        "data": null,
                        "defaultContent": '',
                    },
                    {data : 'package_no', name : 'package_no'},
                    {data : 'items', name:'items'},
                    {data : 'status', name : 'status'},
                    {
                        data : 'desc',
                        "render": function (data) {
                            return data;
                        },
                        name : 'desc'
                    },
                    {
                        data : 'size',
                        "render": function (data) {
                            return data;
                        },
                        name : 'size'
                    },
                    {data : 'created_user_id', name : 'created_user'},
                    {data : 'created_at', name : 'created_at'},
                ],
                @if(app()->getLocale() == 'tr')
                language: {
                    "url": "{{ asset('/assets/vendors/custom/datatables/locale/tr.json') }}"
                },
                @endif
                order : [],
                initComplete : function () {}
            });
            $('#consignmentListHm tbody').on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                var row = tableHm.row( tr );

                if ( row.child.isShown() ) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ route('ajax') }}",
                        data: {process: 'getItems', packageId: row.data().id},
                        beforeSend: function () {},
                        success: function (data) {
                            row.child(format(data)).show();
                            tr.addClass('shown');
                        },
                        complete: function() {}
                    });
                }
            } );

            // MS gorunumu icin
            var tableMs = $('#consignmentListMs').DataTable({
                "responsive" : true,
                "searching": false,
                "paging": false,
                "serverSide": true,
                "bInfo": false,
                "ordering": false,
                // dataSrc : "data",
                ajax : {
                    "url" : "{{ route('consignment.packageMs') }}",
                    "type" : "GET",
                    "datatype": 'json',
                    "data" : function (d) {
                        d.consignmentId = {{ $consignment->id }};
                        d._token = '{{ csrf_token() }}';
                    },
                },
                "createdRow": function (row, data, dataIndex) {
                    $(row).addClass(data.baseRowClass);
                    $(row).addClass(data.baseTextClass);
                },
                columns: [
                    {
                        className: 'dt-control',
                        orderable: false,
                        data: null,
                        defaultContent: '',
                    },
                    {
                        data: 'UPC',
                        className: 'cell-border'
                    },
                    {data: 'SIZE'},
                    {data: 'targetCount'},
                    {data: 'counted'},
                    {data: 'undcounted'},
                ]
            });

            $('#consignmentListMs tbody').on('click', 'td.dt-control', function () {
                var tr = $(this).closest('tr');
                var row = tableMs.row(tr);
                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open this row
                    row.child(formatMs(row.data())).show();
                    tr.addClass('shown');
                }
            });

            // Levis gorunumu icin
            var tableLevis = $('#consignmentListLevis').DataTable({
                responsive : true,
                processing : true,
                serverSide : true,
                searching : false,
                destroy : true,
                // dataSrc : "data",
                ajax : {
                    "url" : "{{ route('consignment.packageLevis') }}",
                    "type" : "GET",
                    "datatype": 'json',
                    "data" : function (d) {
                        d.consignmentId = {{ $consignment->id }};
                        d._token = '{{ csrf_token() }}';
                    },
                },
                columns : [
                    {
                        "className" : 'details-control',
                        "orderable": false,
                        "data": null,
                        "defaultContent": '',
                    },
                    {data : 'package_no', name : 'package_no'},
                    {data : 'items', name:'items'},
                    {data : 'status', name : 'status'},
                    {
                        data : 'po',
                        "render": function (data) {
                            return data;
                        },
                        name : 'po'
                    },
                    {
                        data : 'product_code',
                        "render": function (data) {
                            return data;
                        },
                        name : 'product_code'
                    },
                    {data : 'created_user_id', name : 'created_user'},
                    {data : 'created_at', name : 'created_at'},
                ],
                @if(app()->getLocale() == 'tr')
                language: {
                    "url": "{{ asset('/assets/vendors/custom/datatables/locale/tr.json') }}"
                },
                @endif
                order : [],
                initComplete : function () {}
            });

            $('#consignmentListLevis tbody').on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                var row = tableLevis.row( tr );

                if ( row.child.isShown() ) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ route('ajax') }}",
                        data: {process: 'getItems', packageId: row.data().id},
                        beforeSend: function () {},
                        success: function (data) {
                            row.child(format(data)).show();
                            tr.addClass('shown');
                        },
                        complete: function() {}
                    });
                }
            } );
            // DC gorunumu icin eklenecek..

        });

        function format ( d ) {

            if(d.length > 0){
                var tb = '<table class="table table-striped- table-bordered table-hover table-checkable">\n' +
                    '<thead>\n' +
                    '<tr>\n' +
                        '<th width="25">#</th>\n' +
                        '<th>Epc</th>\n' +
                        '<th>{{ trans('portal.model') }}</th>\n' +
                        '<th>{{ trans('portal.device') }}</th>\n' +
                    '</tr>\n' +
                    '</thead>'
                ;
                tb += d;
                tb+='</table>';
            }else{
                tb = 'Kayıt bulunamadı.';
            }
            return tb;
        }

        /* Formatting function for row details - modify as you need */
        function formatMs(d) {
            var data = JSON.parse(d.boxes);
            // `d` is the original data object for the row
            var table = '<table class="table">' +
                '<thead>' +
                '<tr>' +
                '<td  >' +
                '@lang('station.barcode')' +
                '</td>' +
                '<td class="text-center" >' +
                '@lang('station.carton_nu')' +
                '</td>' +
                '<td >' +
                '@lang('station.description')' +
                '</td>' +
                '<td class="text-center">' +
                '@lang('station.target_qty')' +
                '</td>' +
                '<td class="text-center">' +
                '@lang('station.quantity')' +
                '</td>' +
                '<td class="text-center">' +
                '@lang('station.invalidQuantity')' +
                '</td>' +
                '</tr>' +
                '</thead>' +
                '<tbody>';

            for (i = 0; i < data.length; i++) {
                table = table + '<tr class="' + data[i].CssClass + '">' +
                    '<td >' + data[i].barcode + '</td>' +
                    '<td class="text-center" >' + data[i].cartonID + '</td>' +
                    '<td  onclick="descriptionModal(this)" data-description="' + d.description + '" data-carton ="' + data[i].cartonID + '">' +
                    data[i].colour +
                    '</td>' +
                    '<td class="text-center">' + data[i].singles + '</td>' +
                    '<td class="text-center">' + data[i].counted + '</td>' +
                    '<td class="text-center">' + data[i].Undefinecounted + '</td>' +
                    '</tr>';
            }

            var table = table + '</tbody>' +
                '</table>';

            return table;
        }

        function formatLevis ( d ) {


                var tb = '<table class="table table-striped- table-bordered table-hover table-checkable">\n' +
                    '<thead>\n' +
                    '<tr>\n' +
                    '<th width="25">#</th>\n' +
                    '<th>Epc</th>\n' +
                    '<th>{{ trans('portal.model') }}</th>\n' +
                    '<th>{{ trans('portal.device') }}</th>\n' +
                    '</tr>\n' +
                    '</thead>'
                ;
                tb += d;
                tb+='</table>';

            return tb;
        }

    </script>
    <script type="text/javascript">
        $(function () {
            // setTimeout(function () {
                checkConsignment();
            // }, 5000);
        });

        function checkConsignment() {
            $.ajax({
                url: "{{ route('ajax') }}",
                method : 'post',
                data : {
                    process : 'checkConsignment',
                    consignmentId : '{{ $consignment->id }}',
                },
                beforeSend : function(xhr, opts) {},
                success : function(result){

                    if(result.consignment != null){
                        updateStatus(consignmentStatusPercent(result.consignment.items_count, result.consignment.item_count));
                        $('span#items_count')[0].innerHTML  =
                            "<span class=\"fa fa-tags\"></span> " +
                            formatNumber(result.consignment.items_count)  + " @lang('portal.piece')"
                        ;
                    }

                },
                complete: function(result){
                    // setTimeout(function () {
                        checkConsignment();
                    // }, 5000);
                }
            });
        }
        function updateStatus(value){
            $('#consingnmentStatus div.progress-bar')[0].setAttribute("class", "progress-bar " + consignmentProgressBg(value));
            $('#consingnmentStatus div.progress-bar')[0].setAttribute("style", "width: " + value + "%;");
            $('#consingnmentStatus div.progress-bar')[0].setAttribute("aria-valuenow", value);
            $('#consingnmentStatus span.kt-widget__stat')[0].innerText = "%" + value;
        }

        function consignmentStatusPercent(a = 0, b = 0)
        {
            var x = 0;

            if(a != 0 && b != 0){
                x = (a / b) * 100;
            }else{
                if(a == 0){
                    x = 0;
                }else{
                    x = 100;
                }
            }

            return Math.round(x);
        }
        function consignmentProgressBg(percent)
        {

            var bg = "bg-danger";

            if(percent > 0 && percent <=25){
                bg = "bg-danger";
            }

            if(percent > 25 && percent <=75){
                bg = "bg-warning";
            }

            if(percent > 75){
                bg = "bg-success";
            }

            return bg;
        }
        function formatNumber(num) {
            return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
        }
    </script>
@endsection

