<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{{ $title }} </title>
    </head>
    <style>
        @page {margin: 0px;}
        body {
            font-family: "DeJaVu Sans Mono", Helvetica, Arial, sans-serif;
            margin: 15px 5px 25px 5px;
            font-size: 11px;
        }
        .content {margin: 15px 5px 25px 5px;}
        table {border-collapse: collapse;}
        th, td {
            border: 1px solid #ccc;
            padding: 5px;
            text-align: left;
            vertical-align: top;
        }
        tr:nth-child(even) {background-color: #eee;}
        tr:nth-child(odd) {background-color: #fff;}
        .bg {background-color: #7bb636; border-bottom: 5px solid #679b26; padding: 15px; margin-bottom: 15px; color: #fff;}
        .logo {float: left; font-weight: bold;}
        .title {float: left; font-weight: bold; margin-left: 15px;}
        .date {float: right; font-weight: bold;}
        .clearfix {overflow: auto;}
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        .page-break {page-break-after: always;}
    </style>
    <body>
        <div class="bg clearfix">
            <div class="logo">
                <img src="{{ asset('assets/media/logos/takipsan.svg') }}" width="150px">
            </div>
            <div class="title">
                {{ getSettings('company_name', app()->getLocale()) }} <br>
                {{ $consignment->company->name }} <br>
                <small>By Takipsan</small>
            </div>
            <div class="date">{{ getLocaleDate(date('d-m-Y H:i:s'))}}</div>
        </div>
        <div class="content">
            <h3 style="text-align: center;">@lang('portal.summary_info')</h3>
            <table cellspacing="0" cellpadding="0" width="100%" style="margin-bottom: 20px;">
            <tbody>
                <tr>
                    <th>@lang('portal.po_no')</th>
                    <td>{{ $consignment->name }}</td>
                    <th>@lang('portal.order_code')</th>
                    <td>{{ $consignment->order->order_code }}</td>
                </tr>
                <tr>
                    <th>@lang('portal.number_orders')</th>
                    <td>{{ number_format($consignment->item_count) }}</td>
                    <th>@lang('portal.consignee_name')</th>
                    <td>{{  $consignment->consignee ? $consignment->consignee->name : '-' }}</td>
                </tr>
                <tr>
                    <th>@lang('portal.number_products_read')</th>
                    <td>{{ number_format($consignment->items_count) }}</td>
                    <th>@lang('portal.number_parcels_read')</th>
                    <td>{{ $consignment->packages_count }}</td>
                </tr>
                <tr>
                    <th>@lang('portal.depertman')</th>
                    <td>{{ $consignmentExtra['dep'] }}</td>
                    <th>@lang('portal.delivery_date')</th>
                    <td>{{ getLocaleDate($consignment->delivery_date)}}</td>
                </tr>

                <tr>
                    <th>@lang('portal.departmentDesc')</th>
                    <td>{{ $consignmentExtra['departmentDesc'] }}</td>
                    <th>@lang('portal.packType')</th>
                    <td>{{ $consignmentExtra['packType'] }}</td>
                </tr>
                <tr>
                    <th>@lang('portal.supplierDesc')</th>
                    <td>{{ $consignmentExtra['supplierDesc'] }}</td>
                    <th>@lang('portal.poStatusType')</th>
                    <td>{{ $consignmentExtra['poStatusType'] }}</td>
                </tr>
                <tr>
                    <th>@lang('portal.factoryDescription')</th>
                    <td>{{ $consignmentExtra['factoryDescription'] }}</td>
                    <th>@lang('portal.incotermType')</th>
                    <td>{{ $consignmentExtra['incotermType'] }}</td>
                </tr>
                <tr>
                    <th>@lang('portal.manufacturerCode')</th>
                    <td>{{ $consignmentExtra['manufacturerCode'] }}</td>
                    <th>@lang('portal.portLoadingCode')</th>
                    <td>{{ $consignmentExtra['portLoadingCode'] }}</td>
                </tr>
                <tr>
                    <th>@lang('portal.colourCode')</th>
                    <td>{{ $consignmentExtra['colourCode'] }}</td>
                    <th>@lang('portal.paymentCurrency')</th>
                    <td>{{ $consignmentExtra['paymentCurrency'] }}</td>
                </tr>
                <tr>
                    <th>@lang('portal.colourDesc')</th>
                    <td>{{ $consignmentExtra['colourDesc'] }}</td>
                    <th>@lang('portal.orderNotes')</th>
                    <td>{{ $consignmentExtra['orderNotes'] }}</td>
                </tr>
                <tr>
                    <th>@lang('portal.strokeDesc')</th>
                    <td>{{ $consignmentExtra['strokeDesc'] }}</td>
                    <th>@lang('portal.destination')</th>
                    <td>{{ $consignmentExtra['destination'] }}</td>
                </tr>
                <tr>
                    <th>@lang('portal.season')</th>
                    <td>{{ $consignmentExtra['season'] }}</td>
                    <th>@lang('portal.freightDesc')</th>
                    <td>{{ $consignmentExtra['freightDesc'] }}</td>
                </tr>
                <tr>
                    <th>@lang('portal.finalWarehouseDesc')</th>
                    <td>{{ $consignmentExtra['finalWarehouseDesc'] }}</td>
                    <th>@lang('portal.shipmentMethod')</th>
                    <td>{{ $consignmentExtra['shipmentMethod'] }}</td>
                </tr>

                <tr>
                    <th>@lang('portal.shipment_creator')</th>
                    <td>{{  $consignment->created_user ? $consignment->created_user->name : '-' }}</td>
                    <th>@lang('portal.creator_report')</th>
                    <td> {{ auth()->user()->name }}</td>
                </tr>
            </tbody>
            </table>
            <h3 style="text-align: center;">@lang('portal.package_ms_list')</h3>
            @php
                $sizeArray = array();

            @endphp
            @if(count($resultData) > 0)
                <table cellspacing="0" cellpadding="0" width="100%">
                    <tbody>
                    <tr>
                        @foreach($resultData as $key => $value)
                            @php
                                $countTotal = 0;
                                $unCountTotal = 0;
                            @endphp
                            <td style="border: 1px solid #000;">
                                <b style="display:block;padding-bottom: 5px;font-size: 12px;">
                                    UPC {{ $value['upc'] }} - {{$value['size']}}
                                </b>
                                <table cellspacing="0" cellpadding="0" width="100%">
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td>@lang('station.quantity')</td>
                                        <td>@lang('station.invalidQuantity')</td>
                                    </tr>
                                    @foreach(json_decode($value['boxes']) as $box)
                                        @php
                                            $countTotal = $countTotal + $box->counted;
                                            $unCountTotal = $unCountTotal + $box->Undefinecounted;
                                            $sizeArray[$value['size']] = [
                                                'count' => $countTotal,
                                                'unCount' => $unCountTotal
                                            ];
                                        @endphp
                                        <tr>
                                            <td style='text-align:center;border: 1px solid #000'>
                                                @lang('station.carton') : {{$box->cartonID}}
                                            </td>
                                            <td style='text-align:center;border: 1px solid #000'>
                                                {{ str_limit($value['description'], 30) }}
                                            </td>
                                            <td style='text-align:center;border: 1px solid #000'>
                                                {{$box->counted}}
                                            </td>
                                            <td style='text-align:center;border: 1px solid #000'>
                                                {{$box->Undefinecounted}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
{{--                                <b style="display:block; padding-top: 5px;">--}}
{{--                                    @lang('portal.total_product'):{{ count($value->items) }}--}}
{{--                                </b>--}}
                            </td>
                            @if(($key + 1) % 4 == 0)
                    </tr>
                    <tr>
                        @endif
                        @endforeach
                        @for($i=0; $i < tdCompletion(count($resultData), 4); $i++)
                            <td style="border: 1px solid #000;"></td>
                        @endfor
                    </tr>
                    </tbody>
                </table>
            @endif

            @if(isset($sizeArray) && count($sizeArray) > 0)
                <h3 style="text-align: center;">@lang('portal.size_list')</h3>
                <table cellspacing="0" cellpadding="0" width="100%" style="margin-bottom: 20px;">
                    <tbody>
                        <tr>
                            <td></td>
                            <td>@lang('station.quantity')</td>
                            <td>@lang('station.invalidQuantity')</td>
                        </tr>
                    @foreach($sizeArray as $key => $value)
                        @if ($value['count'] != 0)
                        <tr>
                            <th width="100">{{ $key }}</th>
                            <td>{{ $value['count'] }} @lang('portal.piece')</td>
                            <td>{{ $value['unCount'] }} @lang('portal.piece')</td>
                        </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </body>
</html>
