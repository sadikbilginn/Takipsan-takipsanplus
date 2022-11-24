<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $data['title'] }} </title>
</head>
<style>
    @page { margin: 0px; }
    body {margin: 0; font-family: "DeJaVu Sans Mono", Helvetica, Arial, sans-serif; font-size: 10px;}
    .content{margin: 15px 15px 30px 15px;}
    table {border-collapse: collapse;}
    th, td {border: 1px solid #ccc;padding: 10px;text-align: left;}
    tr:nth-child(even) {background-color: #eee;}
    tr:nth-child(odd) {background-color: #fff;}
    .bg{ background-color: #7bb636; border-bottom: 5px solid #679b26; padding: 20px; margin-bottom: 20px; color: #fff;}
    .logo{ float: left; font-weight: bold;}
    .title{ float: left; font-weight: bold; margin-left: 20px;}
    .date{  float: right; font-weight: bold; text-align: right;}
    .clearfix {overflow: auto;}
    .clearfix::after { content: ""; clear: both; display: table;}
    .page-break {page-break-after: always;}
</style>
<body>
    <div class="bg clearfix">
        <div class="logo"><img src="assets/media/logos/takipsan.svg" width="150px"></div>
        <div class="title">
            {{ getSettings('company_name', app()->getLocale()) }} <br>
            {{ $data['consignment']['company']['name'] }} <br>
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
                <td>{{ $data['consignment']['name'] }}</td>
                <th>@lang('portal.order_code')</th>
                <td>{{ $data['consignment']['order']['order_code'] }}</td>
            </tr>
            <tr>
                <th>@lang('portal.number_orders')</th>
                <td>{{ number_format($data['consignment']['item_count']) }}</td>
                <th>@lang('portal.number_parcels_read')</th>
                <td>{{ $data['consignment']['packages_count'] }}</td>
            </tr>
            <tr>
                <th>@lang('portal.number_products_read')</th>
                <td>{{ number_format($data['consignment']['items_count']) }}</td>
                <th>@lang('portal.consignee_name')</th>
                <td>{{  $data['consignment']['consignee'] ? $data['consignment']['consignee']['name'] : '-' }}</td>
            </tr>
            <tr>
                <th>@lang('portal.plate')</th>
                <td>{{ $data['consignment']['plate_no'] != '' ? $data['consignment']['plate_no'] : '-' }}</td>
                <th>@lang('portal.delivery_date')</th>
                <td>{{ getLocaleDate($data['consignment']['delivery_date'])}}</td>
            </tr>
            <tr>
                <th>@lang('portal.shipment_creator')</th>
                <td>{{  $data['consignment']['created_user'] ? $data['consignment']['created_user']['name'] : '-' }}</td>
                <th>@lang('portal.creator_report')</th>
                <td> {{ auth()->user()->name }}</td>
            </tr>
        </tbody>
        </table>

        <h3 style="text-align: center;">@lang('portal.epc_check_list')</h3>

        @if (count($checkData) > 0 )
        <table cellspacing="0" cellpadding="0" width="100%">
        <tbody>
            <tr>
                @foreach($checkData as $key => $value)
                    <td>
                        <strong>@lang('portal.yeni_paket_sirasi') : {{$value['yeniSira']}}</strong> |
                        @lang('portal.okutulan_paket_sirasi') : {{$value['okutulanSira']}}
                    </td>
                    @if (($key + 1) % 2 == 0 )
            </tr>
            <tr>
                    @endif
                @endforeach

                @if(count($checkData) >= 2)
                    @for($i=0; $i < tdCompletion(count($checkData), 2); $i++)
                        <td></td>
                    @endfor
                @endif
            </tr>
        </tbody>
        </table>
        @endif

    </div>
</body>
</html>