<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title }} </title>
</head>
<style>
    @page { margin: 0px; }
    body {font-family: "DeJaVu Sans Mono", Helvetica, Arial, sans-serif; margin: 15px 15px 30px 15px; font-size: 11px;}
    .content{margin: 15px 15px 30px 15px;}
    table {border-collapse: collapse;}
    th, td {border: 1px solid #ccc; padding: 10px; text-align: left;}
    tr:nth-child(even) {background-color: #eee;}
    tr:nth-child(odd) {background-color: #fff;}
    .bg{ background-color: #7bb636; border-bottom: 5px solid #679b26; padding: 20px; margin-bottom: 20px; color: #fff;}
    .logo{ float: left; font-weight: bold;}
    .title{ float: left; font-weight: bold; margin-left: 20px;}
    .date{  float: right; font-weight: bold;}
    .clearfix {overflow: auto;}
    .clearfix::after {content: "";clear: both;display: table;}
    .page-break {page-break-after: always;}
</style>
<body>
    <div class="bg clearfix">
        <div class="logo"><img src="{{ asset('assets/media/logos/takipsan.svg') }}" width="150px"></div>
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
                <th>@lang('portal.number_parcels_read')</th>
                <td>{{ $consignment->packages_count }}</td>
            </tr>
            <tr>
                <th>@lang('portal.number_products_read')</th>
                <td>{{ number_format($consignment->items_count) }}</td>
                <th>@lang('portal.consignee_name')</th>
                <td>{{  $consignment->consignee ? $consignment->consignee->name : '-' }}</td>
            </tr>
            <tr>
                <th>@lang('portal.plate')</th>
                <td>{{ $consignment->plate_no != '' ? $consignment->plate_no : '-' }}</td>
                <th>@lang('portal.delivery_date')</th>
                <td>{{ getLocaleDate($consignment->delivery_date)}}</td>
            </tr>
            <tr>
                <th>@lang('portal.shipment_creator')</th>
                <td>{{  $consignment->created_user ? $consignment->created_user->name : '-' }}</td>
                <th>@lang('portal.creator_report')</th>
                <td> {{ auth()->user()->name }}</td>
            </tr>
        </tbody>
        </table>

        <h3 style="text-align: center;">@lang('portal.epc_list')</h3>
        @if(count($consignment->packages) > 0)
            @foreach($consignment->packages as $key => $value)
                <table cellspacing="0" cellpadding="0" width="100%" style="margin-bottom: 20px;">
                <thead>
                    <tr>
                        <th colspan="4">
                            @lang('portal.package') {{ $value->package_no }} 
                            (@lang('portal.total') : {{ count($value->items)  }})
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @foreach($value->items as $key2 => $val)
                            <td style="font-size: 8px;">{{ $val->epc }}</td>
                            @if(($key2+1) % 4 == 0)
                    </tr>
                    <tr>
                        @endif
                        @endforeach
                        @for($i=0; $i < tdCompletion(count($value->items), 4); $i++)
                            <td></td>
                        @endfor
                    </tr>
                </tbody>
                </table>
            @endforeach
        @endif
    </div>
</body>
</html>
