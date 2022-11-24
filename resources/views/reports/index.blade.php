@extends('layouts.main')

@section('content')

    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

        <!-- begin:: Content Head -->
        <div class="kt-subheader  kt-grid__item" id="kt_subheader">
            <div class="kt-container  kt-container--fluid ">
                <div class="kt-subheader__main">
                    <h3 class="kt-subheader__title">{{ $company_name }}</h3>
                    <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                    <span class="kt-subheader__desc">@lang('portal.reports')</span>
                </div>
                <div class="kt-subheader__toolbar">
                </div>
            </div>
        </div>

        <!-- begin:: Content -->
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <!--Begin::Dashboard 1-->

            <!--Begin::Row-->
            <div class="row">
                <div class="col-xl-8">
                    <div class="kt-portlet kt-portlet--head--noborder kt-portlet--height-fluid">
                        <div class="kt-portlet__head kt-portlet__head--noborder">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    @lang('portal.monthly_shipment_report')
                                </h3>
                            </div>
                            <div class="kt-portlet__head-toolbar">
                            </div>
                        </div>
                        <div class="kt-portlet__body kt-portlet__body--fluid kt-portlet__body--fit">
                            <div class="kt-widget4 kt-widget4--sticky">
                                <div class="kt-widget4__chart">
                                    <canvas id="kt_chart_trends_stats" style="height: 240px;"></canvas>
                                </div>
                                <div class="kt-widget4__items kt-widget4__items--bottom kt-portlet__space-x kt-margin-b-20">
                                    @foreach($companies as $key => $value)
                                        <div class="kt-widget4__item">
                                            @if(!roleCheck(config('settings.roles.uretici')))
                                            <a href="{{ route('company.show', $value->id) }}" class="kt-widget4__img kt-widget4__img--logo">
                                                <img src="{{ config('settings.media.companies.full_path') . $value->logo }}" width="50" alt="">
                                            </a>
                                            @else
                                            <a class="kt-widget4__img kt-widget4__img--logo">
                                                <img src="{{ config('settings.media.companies.full_path') . $value->logo }}" width="50" alt="">
                                            </a>
                                            @endif
                                            <div class="kt-widget4__info">
                                                @if(!roleCheck(config('settings.roles.uretici')))
                                                <a href="{{ route('company.show', $value->id) }}" class="kt-widget4__title">
                                                    {{ $value->name }}
                                                </a>
                                                @endif

                                                <span class="kt-widget4__sub">
                                                    {{ $value->name }}
                                                </span>
                                            </div>
                                            <span class="kt-widget4__ext">
                                                <span class="kt-widget4__number kt-font-success">{{ $value->consignment_total }} @lang('portal.piece')</span>
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if(isset($maxConsignee))
                    <div class="col-xl-4">
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
            <!--End::Row-->
            <!--End::Dashboard 1-->
        </div>
        <!-- end:: Content -->

    </div>
    <!-- end:: Content Head -->

@endsection

@section('css')


@endsection

@section('js')
    <script>
        $(function () {
            revenueChange();
            trendsStats();
        });

        // Trends Stats.
        // Based on Chartjs plugin - http://www.chartjs.org/
        var trendsStats = function() {
            if ($('#kt_chart_trends_stats').length == 0) {
                return;
            }

            var ctx = document.getElementById("kt_chart_trends_stats").getContext("2d");

            var gradient = ctx.createLinearGradient(0, 0, 0, 240);
            gradient.addColorStop(0, Chart.helpers.color('#00c5dc').alpha(0.7).rgbString());
            gradient.addColorStop(1, Chart.helpers.color('#f2feff').alpha(0).rgbString());

            var config = {
                type: 'line',
                data: {
                    labels: [@foreach($consignments as $key => $value) "{{ $key }}",  @endforeach],

                    datasets: [{
                        label: "@lang('portal.consignment')",
                        backgroundColor: gradient, // Put the gradient here as a fill color
                        borderColor: '#0dc8de',

                        pointBackgroundColor: Chart.helpers.color('#ffffff').alpha(0).rgbString(),
                        pointBorderColor: Chart.helpers.color('#ffffff').alpha(0).rgbString(),
                        pointHoverBackgroundColor: KTApp.getStateColor('danger'),
                        pointHoverBorderColor: Chart.helpers.color('#000000').alpha(0.2).rgbString(),

                        //fill: 'start',
                        data: [@foreach($consignments as $key => $value) {{ count($value) }},  @endforeach],

                    }]
                },
                options: {
                    title: {
                        display: false,
                    },
                    tooltips: {
                        intersect: false,
                        mode: 'nearest',
                        xPadding: 10,
                        yPadding: 10,
                        caretPadding: 10
                    },
                    legend: {
                        display: false
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    hover: {
                        mode: 'index'
                    },
                    scales: {
                        xAxes: [{
                            display: false,
                            gridLines: false,
                            scaleLabel: {
                                display: true,
                                labelString: 'Month'
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
                            tension: 0.19
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
                            top: 5,
                            bottom: 0
                        }
                    }
                }
            };

            var chart = new Chart(ctx, config);
        }

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
                        KTApp.getStateColor('success')
                    ],
                });
            }//function
        @endif
    </script>
@endsection


