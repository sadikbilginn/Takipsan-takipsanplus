<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{{ $title }} </title>
    </head>
    <style>
        @page { margin: 0px; }
        body {
            font-family: "DeJaVu Sans Mono", Helvetica, Arial, sans-serif;
            margin: 15px 15px 30px 15px;
            font-size: 11px;
        }
        .content {margin: 15px 15px 30px 15px;}
        table {border-collapse: collapse;}
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
            vertical-align: top;
        }
        tr:nth-child(even) {background-color: #eee;}
        tr:nth-child(odd) {background-color: #fff;}
        .bg {background-color: #7bb636; border-bottom: 5px solid #679b26; padding: 20px; margin-bottom: 20px; color: #fff;}
        .logo {float: left; font-weight: bold;}
        .title {float: left; font-weight: bold; margin-left: 20px;}
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
            @php
                $sizes = [];
                $renderedGtins =[];
                $renderedGtinss =[];
            @endphp
            <h3 style="text-align: center;">@lang('portal.package_ms_list')</h3>
            @if(count($consignment->packages) > 0)
                <table cellspacing="0" cellpadding="0" width="100%">
                <tbody>
                    <tr>
                        @foreach($consignment->packages as $key => $value)
                            @php
                                $renderedGtins=[];
                                $renderedGtinss=[];
                            @endphp
                            <td style="border: 1px solid #000;">
                                <b style="display:block;padding-bottom: 5px;font-size: 12px;">
                                    @lang('portal.package') {{ $value->package_no }}
                                </b>
                                <table cellspacing="0" cellpadding="0" width="100%">
                                    <tr>
                                        <td style="text-align:center;border: 1px solid #000;" colspan="2">
                                            @foreach($value->items as $item)
                                                @if(in_array($item->gtin,$renderedGtins)==false)
                                                    @if(count($item->itemDetails) > 0)
                                                        <b>{{ explode(',', $item->itemDetails[0]->description)[0] }}</b>
                                                    @else
                                                        <b>UND </b>
                                                    @endif
                                                    @php
                                                        array_push($renderedGtins,$item->gtin);
                                                    @endphp
                                                @endif
                                            @endforeach
                                        </td>
                                    </tr>
                                    @foreach($value->items as $item)
                                        @if(in_array($item->gtin,$renderedGtinss)==false)
                                            @if(count($item->itemDetails) > 0)
                                                <tr>
                                                    <td style='text-align:center;border: 1px solid #000'>
                                                        {{ explode(',', $item->itemDetails[0]->description)[1] }}
                                                    </td>
                                                    <td style='text-align:center;border: 1px solid #000'>
                                                        {{ getPackageItemCount($value,$item->gtin)}} @lang('portal.piece')
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td style='text-align:center;border: 1px solid #000'> UND</td>
                                                    <td style='text-align:center;border: 1px solid #000'>
                                                        {{ getPackageItemCount($value,$item->gtin)}} @lang('portal.piece')
                                                    </td>
                                                </tr>
                                            @endif
                                            {{array_push($renderedGtinss,$item->gtin)}}
                                            @php
                                                if(isset($item->itemDetails[0]))
                                                    if(isset($sizes[$item->itemDetails[0]->description]))
                                                        $sizes[$item->itemDetails[0]->description] = 
                                                            $sizes[$item->itemDetails[0]->description] + 
                                                            getPackageItemCount($value,$item->gtin);
                                                    else
                                                        $sizes[$item->itemDetails[0]->description] = getPackageItemCount($value,$item->gtin);
                                                else
                                                    if(isset($sizes["UND"]))
                                                        $sizes["UND"] = $sizes["UND"] + getPackageItemCount($value,$item->gtin);
                                                    else
                                                        $sizes["UND"] =  getPackageItemCount($value,$item->gtin);
                                            @endphp
                                        @endif
                                    @endforeach
                                </table>
                                <b style="display:block; padding-top: 5px;">
                                    @lang('portal.total_product'):{{ count($value->items) }}
                                </b>
                            </td>
                            @if(($key + 1) % 4 == 0)
                    </tr>
                    <tr>
                            @endif
                        @endforeach
                        @for($i=0; $i<tdCompletion($consignment->packages_count, 4); $i++)
                            <td style="border: 1px solid #000;"></td>
                        @endfor
                    </tr>
                </tbody>
                </table>
            @endif
            @if(isset($sizes) && count($sizes) > 0)
                <h3 style="text-align: center;">@lang('portal.size_list')</h3>
                <table cellspacing="0" cellpadding="0" width="100%" style="margin-bottom: 20px;">
                <tbody>
                    @foreach($sizes as $key => $value)
                        <tr>
                            <th width="100">{{ $key }}</th>
                            <td>{{ $value }} @lang('portal.piece')</td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            @endif
        </div>
    </body>
</html>