@extends('layouts.main')

@section('content')

<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

    <!-- begin:: Subheader -->
    <div class="kt-subheader kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">
                    @lang('portal.companies')
                </h3>
                <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                <div class="kt-subheader__group" id="kt_subheader_search">
                    <span class="kt-subheader__desc" id="kt_subheader_total">{{ $company->name }}</span>
                </div>
            </div>
            <div class="kt-subheader__toolbar"></div>
        </div>
    </div>

    <!-- end:: Subheader -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <!--Begin::Section-->
        <div class="row">
            <div class="col-xl-12">
                <!--begin:: Widgets/Applications/User/Profile3-->
                <div class="kt-portlet kt-portlet--height-fluid">
                    <div class="kt-portlet__body">
                        <div class="kt-widget kt-widget--user-profile-3">
                            <div class="kt-widget__top">
                                <div class="kt-widget__media kt-hidden-">
                                @if($company->logo)
                                    <img src="{{ config('settings.media.companies.full_path') . $company->logo }}" alt="image">
                                    @endif
                                    @if($company->logo == '')
                                    @endif
                                </div>
                                <div class="kt-widget__pic kt-widget__pic--danger kt-font-danger kt-font-boldest kt-font-light kt-hidden">
                                    JM
                                </div>
                                <div class="kt-widget__content">
                                    <div class="kt-widget__head">
                                        <a href="#" class="kt-widget__username">
                                            {{ $company->name }}
                                            {!! $company->status == true ? '<i class="flaticon2-correct kt-font-success"></i>' : '<i class="flaticon2-correct kt-font-danger"></i>' !!}
                                        </a>

                                        <div class="kt-widget__action">
                                            <button type="button" class="btn btn-label-success btn-sm btn-upper" onclick="javascript:history.go(-1);"><i class="la la-arrow-left"></i> @lang('portal.back')</button>&nbsp;
                                            <button type="button" class="btn btn-brand btn-sm btn-upper" onclick="window.location='{{ route('company.edit', $company->id) }}';"><i class="la la-edit"></i> @lang('portal.edit')</button>
                                        </div>
                                    </div>

                                    <div class="kt-widget__subhead">
                                    {{-- 
                                        <a href="mailto:{{ $company->email }}"><i class="flaticon2-new-email"></i>{{ $company->email }}</a>
                                        <a href="tel:{{ $company->phone }}"><i class="flaticon2-phone"></i>{{ $company->phone }} </a>
                                      <a href="https://www.google.com/maps/dir/{{ $company->latitude }},+{{ $company->longitude }}" target="_blank"><i class="flaticon2-placeholder"></i>{{ $company->latitude }} , {{ $company->longitude }}</a>  --}}
                                    </div>

                                    <div class="kt-widget__info">
                                        
                                        <div class="kt-widget__desc">
                                            {{ $company->email }} <br>
                                            {{ $company->phone }}
                                        </div>
                                        
                                        <div class="kt-widget__progress">
                                            <div class="kt-widget__text">
                                                @lang('portal.c_completion_rate')
                                            </div>
                                            @php $comStatus = consignmentStatusPercent(count($company->consignments->where('status', 0)), count($company->consignments)); @endphp
                                            <div class="progress" style="height: 5px;width: 100%;">
                                                <div class="progress-bar {{ consignmentProgressBg($comStatus) }}" role="progressbar" style="width: {{ $comStatus }}%;" aria-valuenow="{{ $comStatus }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="kt-widget__stats">
                                                {{ $comStatus }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="kt-widget__bottom">

                                <div class="kt-widget__item">
                                    <div class="kt-widget__icon">
                                        <i class="flaticon-laptop"></i>
                                    </div>
                                    <div class="kt-widget__details">
                                        <span class="kt-widget__title">{{ count($company->devices) }} @lang('portal.device')</span>
                                        <a href="{{ route('company.device.index', $company->id) }}" class="kt-widget__value kt-font-brand">@lang('portal.show')</a>
                                    </div>
                                </div>

                                <div class="kt-widget__item">
                                    <div class="kt-widget__icon">
                                        <i class="flaticon-truck"></i>
                                    </div>
                                    <div class="kt-widget__details">
                                        <span class="kt-widget__title">{{ count($company->consignments) }} @lang('portal.consignment')</span>
                                        <a href="{{ route('consignment.index', ['company' => $company->id]) }}" class="kt-widget__value kt-font-brand">@lang('portal.show')</a>
                                    </div>
                                </div>

                                <div class="kt-widget__item">
                                    <div class="kt-widget__icon">
                                        <i class="flaticon-open-box"></i>
                                    </div>
                                    <div class="kt-widget__details">
                                        <span class="kt-widget__title">@lang('portal.package')</span>
                                        <span class="kt-widget__value"><span>{{ number_format($company->packages()->count()) }}  @lang('portal.piece')</span></span>
                                    </div>
                                </div>

                                <div class="kt-widget__item">
                                    <div class="kt-widget__icon">
                                        <i class="flaticon-interface-9"></i>
                                    </div>
                                    <div class="kt-widget__details">
                                        <span class="kt-widget__title">@lang('portal.product')</span>
                                        <span class="kt-widget__value"><span>{{ number_format($company->items()->count()) }}  @lang('portal.piece')</span></span>
                                    </div>
                                </div>

                                <div class="kt-widget__item">
                                    <div class="kt-widget__icon">
                                        <i class="flaticon-buildings"></i>
                                    </div>
                                    <div class="kt-widget__details">
                                        <span class="kt-widget__title">@lang('portal.consignee_name')</span>
                                        <span class="kt-widget__value"><span>{{ $company->consignees()->count() }}  @lang('portal.piece')</span></span>
                                    </div>
                                </div>

                                <div class="kt-widget__item">
                                    <div class="kt-widget__icon">
                                        <i class="flaticon-users"></i>
                                    </div>
                                    <div class="kt-widget__details">
                                        <span class="kt-widget__title">@lang('portal.users')</span>
                                        <span class="kt-widget__value"><span>{{ $company->users()->count() }}  @lang('portal.piece')</span></span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!--end:: Widgets/Applications/User/Profile3-->
            </div>
        </div>
        <!--End::Section-->

        <!--Begin::Section-->
        <div class="row">
            <div class="col-xl-12">
                <div class="kt-portlet">
                    <div class="kt-portlet__body  kt-portlet__body--fit">
                        <div class="row row-no-padding row-col-separator-xl">
                            <div class="col-xl-8 col-lg-8 order-lg-2 order-xl-1">
                                <!--begin:: Widgets/Daily Sales-->
                                <div class="kt-portlet kt-portlet--height-fluid">
                                    <div class="kt-widget14">
                                        <div class="kt-widget14__header">
                                            <h3 class="kt-widget14__title">
                                                @lang('portal.monthly_consignments')
                                            </h3>
                                            <span class="kt-widget14__desc">
                                                @lang('portal.monthly_consignments_text')
                                            </span>
                                        </div>
                                        <div class="kt-widget14__chart">
                                            <div class="kt-widget20">
                                                <div class="kt-widget20__content kt-portlet__space-x" style="padding: 0">
                                                    <span class="kt-widget20__number kt-font-danger"> {{ count($company->consignments) }} +</span>
                                                    <span class="kt-widget20__desc">@lang('portal.consignment')</span>
                                                </div>
                                                <div class="kt-widget20__chart" style="height:130px;">
                                                    <canvas id="kt_chart_bandwidth2"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end:: Widgets/Daily Sales-->
                            </div>

                            @if(isset($maxConsignee))
                                <div class="col-xl-4 col-lg-4 order-lg-2 order-xl-1">
                                    <!--begin:: Widgets/Revenue Change-->
                                    <div class="kt-portlet kt-portlet--height-fluid">
                                        <div class="kt-widget14">
                                            <div class="kt-widget14__header">
                                                <h3 class="kt-widget14__title">
                                                    @lang('portal.top_shipped')
                                                </h3>
                                                <span class="kt-widget14__desc">
                                                    @lang('portal.first3_company')
                                                </span>
                                            </div>
                                            <div class="kt-widget14__content">
                                                <div class="kt-widget14__chart">
                                                    <div id="kt_chart_revenue_change" style="height: 150px; width: 150px;"></div>
                                                </div>
                                                <div class="kt-widget14__legends">
                                                    @foreach($maxConsignee as $key => $value)
                                                        <div class="kt-widget14__legend">
                                                            <span class="kt-widget14__bullet {{ getChartBg($key) }}"></span>
                                                            <span class="kt-widget14__stats">{{ $value->consignee->name}}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end:: Widgets/Revenue Change-->
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!--End::Section-->

        <!--Begin::Section-->
        <div class="row">
            <div class="col-xl-12">
                <!--begin:: Widgets/User Progress -->
                <div class="kt-portlet kt-portlet--height-fluid">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                @lang('portal.last_consignments')
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="kt-widget31">
                            @if(isset($last_consignments))
                                @foreach($last_consignments as $key => $value)
                                    <div class="kt-widget31__item">
                                        <div class="kt-widget31__content">
                                            <div class="kt-widget31__pic">
                                                <img src="{{ $value->consignee ? config('settings.media.consignees.full_path') . $value->consignee->logo : asset('assets/media/default.jpg') }}" style="border: 1px solid #aaa" height="50" alt="">
                                            </div>
                                            <div class="kt-widget31__info">
                                                <a href="{{ route('consignment.show', $value->id) }}" class="kt-widget31__username">
                                                    {{ $value->name }}
                                                </a>
                                                <small>{{ $value->created_at->diffForHumans() }}</small>
                                                <p class="kt-widget31__text">
                                                    {!! $value->status == true ? '<span class="badge badge-success">'.trans('portal.opened').'</span>' : '<span class="badge badge-danger">'.trans('portal.closed').'</span>' !!}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="kt-widget31__content">
                                            <div class="kt-widget31__progress">
                                                <a href="{{ route('consignment.show', $value->id) }}" class="kt-widget31__stats">
                                                    <span>{{ consignmentStatusPercent($value->items_count, $value->item_count) }}%</span>
                                                    <span>{{ $value->consignee ? $value->consignee->name : '' }}</span>
                                                </a>
                                                @php $conStatus = consignmentStatusPercent($value->items_count, $value->item_count); @endphp
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar {{ consignmentProgressBg($conStatus) }}" role="progressbar" style="width: {{ $conStatus }}%" aria-valuenow="{{ $conStatus }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                            <a href="{{ route('consignment.show', $value->id) }}" class="btn-label-brand btn btn-sm btn-bold">@lang('portal.details')</a>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <!--end:: Widgets/User Progress -->
            </div>
        </div>
        <!--End::Section-->
    </div>
</div>

@endsection

@section('js')
    <script>
        $(function() {
            bandwidthChart2();
            revenueChange();
        });

        //aylara bölünecek rastgele getiriyor
        @if(isset($company->consignments))
            var bandwidthChart2 = function () {
                if ($('#kt_chart_bandwidth2').length == 0) {
                    return;
                }
                var ctx = document.getElementById("kt_chart_bandwidth2").getContext("2d");

                var gradient = ctx.createLinearGradient(0, 0, 0, 240);
                gradient.addColorStop(0, Chart.helpers.color('#ffefce').alpha(1).rgbString());
                gradient.addColorStop(1, Chart.helpers.color('#ffefce').alpha(0.3).rgbString());

                var config = {
                    type: 'line',
                    data: {
                        labels: [@foreach($company->consignments->groupBy(function ($val) {return \Carbon\Carbon::parse($val->created_at)->format('F');}) as $key => $value) "{{ $key }}",  @endforeach],
                        datasets: [{
                            label: "@lang('portal.consignment')",
                            backgroundColor: gradient,
                            borderColor: KTApp.getStateColor('warning'),
                            pointBackgroundColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointBorderColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointHoverBackgroundColor: KTApp.getStateColor('danger'),
                            pointHoverBorderColor: Chart.helpers.color('#000000').alpha(0.1).rgbString(),
                            //fill: 'start',
                            data: [@foreach($company->consignments->groupBy(function ($val) {return \Carbon\Carbon::parse($val->created_at)->format('F');}) as $key => $value) {{ count($value) }},  @endforeach],
                        }]
                    },
                    options: {
                        title: {
                            display: true,
                        },
                        tooltips: {
                            mode: 'nearest',
                            intersect: false,
                            position: 'nearest',
                            xPadding: 10,
                            yPadding: 10,
                            caretPadding: 10
                        },
                        legend: {
                            display: false
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            xAxes: [{
                                display: false,
                                gridLines: false,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Ay'
                                }
                            }],
                            yAxes: [{
                                display: false,
                                gridLines: false,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Value'
                                },
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        },
                        elements: {
                            line: {
                                tension: 0.0000001
                            },
                            point: {
                                radius: 4,
                                borderWidth: 12
                            }
                        },
                        layout: {
                            padding: {
                                left: 0,
                                right: 0,
                                top: 10,
                                bottom: 0
                            }
                        }
                    }
                };

                var chart = new Chart(ctx, config);
            };
        @endif

        // en çok sevkiyat yapılan firma
        @if (isset($maxConsignee))
        var revenueChange = function () {
                if ($('#kt_chart_revenue_change').length == 0) {
                    return;
                }
                Morris.Donut({
                    element: 'kt_chart_revenue_change',
                    data: [
                            @foreach($maxConsignee as $key => $value)
                        {
                            label: "{!!  $value->consignee->name !!} ",
                            value: {!!   $value->total !!}
                        },
                        @endforeach
                    ],

                    colors: [
                        KTApp.getStateColor('danger'),
                        KTApp.getStateColor('brand'),
                        KTApp.getStateColor('dark')
                    ],
                });
            }//function
    @endif
    </script>
@endsection
