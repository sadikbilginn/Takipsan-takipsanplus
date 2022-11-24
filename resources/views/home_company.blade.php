@extends('layouts.main')

@section('content')

    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

        <!-- begin:: Content Head -->
        <div class="kt-subheader  kt-grid__item" id="kt_subheader">
            <div class="kt-container  kt-container--fluid ">
                <div class="kt-subheader__main">
                    <h3 class="kt-subheader__title">{{ $company_name }}</h3>
                    <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                    <span class="kt-subheader__desc">Dashboard</span>
                </div>
                <div class="kt-subheader__toolbar">
                </div>
            </div>
        </div>
        <!-- begin:: Content -->
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <!--Begin::Dashboard 1-->

            <div class="row">
                <div class="col-xl-8">
                    <div class="kt-portlet kt-portlet--mobile">
                        <div class="kt-portlet__head kt-portlet__head--lg">
                            <div class="kt-portlet__head-label">
                                <span class="kt-portlet__head-icon">
                                    <i class="kt-font-brand flaticon2-open-box"></i>
                                </span>
                                <h3 class="kt-portlet__head-title">
                                    @lang('portal.open_consignments')
                                </h3>
                            </div>
                            <div class="kt-portlet__head-toolbar">
                                <div class="kt-portlet__head-wrapper">
                                    <div class="kt-portlet__head-actions">
                                        <a href="{{ route('consignment.create') }}" class="btn btn-brand btn-elevate btn-icon-sm">
                                            <i class="la la-plus"></i>
                                            @lang('portal.new_consignment')
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <table class="table table-striped- table-bordered table-hover table-checkable" id="consignmentList">
                                <thead>
                                <tr>
                                    <th>Po No</th>
                                    <th>@lang('portal.consignee_name')</th>
                                    <th>@lang('portal.delivery_date')</th>
                                    <th>@lang('portal.status')</th>
                                    <th>@lang('portal.consignmet_status')</th>
                                    @if(roleCheck(config('settings.roles.anaUretici')))
                                        <th width="100">@lang('portal.actions')</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($open_consignments as $key => $value)
                                    <tr id="{{ $value->id }}">
                                        <td><a href="{{ route('consignment.show', $value->id)  }}">{{ $value->name }}</a></td>
                                        <td>{{ $value->consignee ? $value->consignee->name : '-' }}</td>
                                        <td>{{ date('d-m-Y', strtotime($value->delivery_date)) }}</td>
                                        <td align="center">{!! $value->status == 1 ? '<span class="badge badge-success">Açık</span>' : '<span class="badge badge-danger">Kapalı</span>' !!}</td>
                                        <td>
                                            <div class="kt-widget__progress d-flex align-items-center">
                                                @php $comStatus = consignmentStatusPercent($value->items_count, $value->item_count); @endphp
                                                <div class="progress" style="height: 5px;width: 100%;">
                                                    <div class="progress-bar {{ consignmentProgressBg($comStatus) }}" role="progressbar" style="width: {{ $comStatus }}%;" aria-valuenow="{{ $comStatus }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <span class="kt-widget__stats">%{{ $comStatus }}</span>
                                            </div>
                                        </td>
                                        @if(roleCheck(config('settings.roles.anaUretici')))

                                            <td width="100" align="center">
                                                <span class="dropdown">
                                                    <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                                                    <i class="la la-cogs"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a class="dropdown-item" href="{{ route('consignment.show', $value->id) }}"><i class="la la-search"></i> @lang('portal.show')</a>
                                                        <a class="dropdown-item" href="{{ route('consignment.status', $value->id) }}"><i class="la la-truck"></i> @lang('portal.close_consignment')</a>
                                                        <a class="dropdown-item" href="{{ route('consignment.edit', $value->id) }}"><i class="la la-edit"></i> @lang('portal.edit')</a>
                                                        <a class="dropdown-item" href="{{ route('consignment.destroy', $value->id) }}" data-method="delete" data-token="{{ csrf_token() }}" data-confirm="@lang('portal.delete_text')"><i class="la la-trash"></i> @lang('portal.delete')</a>
                                                    </div>
                                                </span>
                                            </td>
                                        @endif

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="kt-portlet kt-portlet--height-fluid ">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    @lang('portal.general_information')
                                </h3>
                            </div>
                        </div>
                        <!--full height portlet body-->
                        <div class="kt-portlet__body kt-portlet__body--fluid kt-portlet__body--fit">
                            <div class="kt-widget4 kt-widget4--sticky">
                                <div class="kt-widget4__items kt-portlet__space-x kt-margin-t-15">
                                    <div class="kt-widget4__item">
                                    <span class="kt-widget4__icon">
                                        <i class="flaticon2-lorry kt-font-brand"></i>
                                    </span>
                                        <a href="#" class="kt-widget4__title">
                                            @lang('portal.consignments')
                                        </a>
                                        <span class="kt-widget4__number kt-font-brand" id="consignments_count">{{ $company->consignments_count }}  @lang('portal.piece')</span>
                                    </div>
                                    <div class="kt-widget4__item">
                                    <span class="kt-widget4__icon">
                                        <i class="flaticon2-laptop  kt-font-success"></i>
                                    </span>
                                        <a href="#" class="kt-widget4__title">
                                            @lang('portal.device')
                                        </a>
                                        <span class="kt-widget4__number kt-font-success" id="devices_count">{{ $company->devices_count }}  @lang('portal.piece')</span>
                                    </div>
                                    <div class="kt-widget4__item">
                                    <span class="kt-widget4__icon">
                                        <i class="flaticon2-delivery-package  kt-font-danger"></i>
                                    </span>
                                        <a href="#" class="kt-widget4__title">
                                            @lang('portal.packages')
                                        </a>
                                        <span class="kt-widget4__number kt-font-danger" id="packages_count">{{ number_format($company->packages_count) }}  @lang('portal.piece')</span>
                                    </div>
                                    <!-- <div class="kt-widget4__item">
                                    <span class="kt-widget4__icon">
                                        <i class="flaticon2-tag kt-font-warning"></i>
                                    </span>
                                        <a href="#" class="kt-widget4__title">
                                            @lang('portal.products')
                                        </a>
                                        <span class="kt-widget4__number kt-font-warning" id="items_count">{{ number_format($company->items_count) }}  @lang('portal.piece')</span>
                                    </div> -->
                                    <div class="kt-widget4__item">
                                    <span class="kt-widget4__icon">
                                        <i class="flaticon2-architecture-and-city kt-font-skype"></i>
                                    </span>
                                        <a href="#" class="kt-widget4__title">
                                            @lang('portal.firms_to_ship')
                                        </a>
                                        <span class="kt-widget4__number kt-font-skype" id="consignees_count">{{ $company->consignees_count }}  @lang('portal.piece')</span>
                                    </div>
                                    <div class="kt-widget4__item">
                                    <span class="kt-widget4__icon">
                                        <i class="flaticon2-group kt-font-dark"></i>
                                    </span>
                                        <a href="#" class="kt-widget4__title">
                                            @lang('portal.users')
                                        </a>
                                        <span class="kt-widget4__number kt-font-dark" id="users_count">{{ $company->users_count }}  @lang('portal.piece')</span>
                                    </div>
                                </div>
                                <div class="kt-widget4__chart kt-margin-t-15">
                                    <canvas id="kt_chart_latest_updates" style="height: 150px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--End::Dashboard 1-->
        </div>
        <!-- end:: Content -->
    </div>
    <!-- end:: Content Head -->


@endsection

@section('css')


@endsection

@section('js')
    <script type="text/javascript">
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // setTimeout(function () {
                 checkConsignment();
            // }, 5000);
        });


        function checkConsignment() {

            $.ajax({
                url: "{{ route('ajax') }}",
                method: 'post',
                data : {
                    process         : 'checkConsignmentHome'
                },
                beforeSend: function(xhr, opts) {},
                success: function(result){


                    // if(result.openConsignments.length != {{ count($open_consignments) }}){
                    //     window.location.reload();
                    // }

                    if(result.openConsignments != null){
                        result.openConsignments.forEach(function (value, key) {
                            if ($("#consignmentList > tbody > tr").is("#" + value.id)) {
                                console.log('var');
                                updateStatus(value.id, consignmentStatusPercent(value.items_count, value.item_count));
                            }else{
                                // window.location.reload();
                                $(document).ready(function() {
                                    var refreshId = setInterval(function() {
                                    console.log('veri geldi: ' + refreshId);
                                    $("#consignmentListBody").load('{{ route('tdurl') }}');
                                }, 1250); // 10 saniyede bir verileri çek
                                });
                            }
                        });
                    }
                    if(result.company != null){

                        $('#consignments_count')[0].innerText   = result.company.consignments_count  + " @lang('portal.piece')";
                        $('#devices_count')[0].innerText        = result.company.devices_count  + " @lang('portal.piece')";
                        $('#packages_count')[0].innerText       = formatNumber(result.company.packages_count)  + " @lang('portal.piece')";
                        //$('#items_count')[0].innerText          = formatNumber(result.company.items_count)  + " @lang('portal.piece')";
                        $('#consignees_count')[0].innerText     = result.company.consignees_count  + " @lang('portal.piece')";
                        $('#users_count')[0].innerText          = result.company.users_count  + " @lang('portal.piece')";

                    }

                },
                complete: function(result){
                    // setTimeout(function () {
                        checkConsignment();
                    // }, 5000);
                }
            });
        }

        @if(isset($consignments))
            $(function () {
                latestUpdates();
            });
            var latestUpdates = function() {
                if ($('#kt_chart_latest_updates').length == 0) {
                    return;
                }

                var ctx = document.getElementById("kt_chart_latest_updates").getContext("2d");

                var config = {
                    type: 'line',
                    data: {
                        labels: [@foreach($consignments as $key => $value) "{{ $key }}",  @endforeach],
                        datasets: [{
                            label: "@lang('portal.consignment')",
                            backgroundColor: KTApp.getStateColor('danger'), // Put the gradient here as a fill color
                            borderColor: KTApp.getStateColor('danger'),
                            pointBackgroundColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointBorderColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointHoverBackgroundColor: KTApp.getStateColor('success'),
                            pointHoverBorderColor: Chart.helpers.color('#000000').alpha(0.1).rgbString(),

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
                                tension: 0.0000001
                            },
                            point: {
                                radius: 4,
                                borderWidth: 12
                            }
                        }
                    }
                };

                var chart = new Chart(ctx, config);
            };

        @endif

        function updateStatus(key, value){
            $('#consignmentList > tbody > tr#' + key + ' div.progress-bar')[0].setAttribute("class", "progress-bar " + consignmentProgressBg(value));
            $('#consignmentList > tbody > tr#' + key + ' div.progress-bar')[0].setAttribute("style", "width: " + value + "%;");
            $('#consignmentList > tbody > tr#' + key + ' div.progress-bar')[0].setAttribute("aria-valuenow", value);
            $('#consignmentList > tbody > tr#' + key + ' span.kt-widget__stats')[0].innerText = "%" + value;
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


        $(document).ready(function() {
            var refreshId = setInterval(function() {
            console.log('veri geldi: ' + refreshId);
            $("#consignmentListBody").load('{{ route('tdurl') }}');
          }, 1250); // 10 saniyede bir verileri çek
          });
    </script>
@endsection


