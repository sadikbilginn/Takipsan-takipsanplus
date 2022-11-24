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
            margin: 0;
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
            <div class="logo"><img src="{{ asset('assets/media/logos/takipsan.svg') }}" width="150px"></div>
            <div class="title">
                {{ getSettings('company_name', app()->getLocale()) }} <br>
                <br>
                <small>By Takipsan</small>
            </div>
            <div class="date">{{ getLocaleDate(date('d-m-Y H:i:s'))}}</div>
        </div>
        <div class="content">

            <table style="width: 100%;" border="1">
                <tbody>
                <tr>
                    <td>
                        <table style="width: 100%; float: left;" border="0" cellspacing="0" cellpadding="0">
                            <tbody>
                            <tr>
                                <td style="width:60px">@lang('portal.supplier')</td>
                                <td>{{$fixVal[0]->supplierDesc ? $fixVal[0]->supplierDesc : '-'}}</td>
                            </tr>
                            <tr>
                                <td style="width:60px">@lang('portal.address')</td>
                                <td> - </td>
                            </tr>
                            <tr>
                                <td style="width:60px">@lang('portal.factory')</td>
                                <td>{{$fixVal[0]->factoryDescription ? $fixVal[0]->factoryDescription : '-'}}</td>
                            </tr>
                            <tr>
                                <td style="width:60px">@lang('portal.address')</td>
                                <td> - </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                    <td>
                        <table style="width: 100%;" border="0" cellspacing="0" cellpadding="0">
                            <tbody>
                            <tr>
                                <td style="width:60px">@lang('portal.seriesNo')</td>
                                <td>{{json_decode($fixVal[0]->cartons, true)[0]['series']}}</td>
                                <td style="width:60px">M&S Dept.</td>
                                <td>{{$fixVal[0]->dep}}</td>
                            </tr>
                            <tr>
                                <td style="width:60px">PO No:</td>
                                <td>{{$fixVal[0]->poNumber}}</td>
                                <td style="width:60px">Stroke No</td>
                                <td>{{$fixVal[0]->stroke}}</td>
                            </tr>
                            <tr>
                                <td style="width:60px">@lang('portal.colour')</td>
                                <td colspan="3">{{$fixVal[0]->colourCode}} - {{$fixVal[0]->colourDesc}}</td>
                            </tr>
                            <tr>
                                <td style="width:60px">@lang('portal.description')</td>
                                <td colspan="3">{{$fixVal[0]->strokeDesc}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
            <h3 style="text-align: center;"></h3>
            <table style="width: 100%;" border="1">
                <tbody>
                <tr>
                    <td>

                        <table style="width: 100%; float: left;" border="0" cellspacing="0" cellpadding="0">
                            <tbody>
                            <tr>
                                <td style="width:210px">@lang('portal.packingList')</td>
                                <td>
                                    @php
                                        $packType = $fixVal[0]->packType;
                                        if ($packType == "B") {
                                            $packType = "Boxed";
                                        } elseif ($packType == "H") {
                                            $packType = "Hanging";
                                        } elseif ($packType == "C") {
                                            $packType = "Converted";
                                        } elseif ($packType == "D") {
                                            $packType = "Boxed to Tote";
                                        }
                                    @endphp
                                    {{$packType}}
                                </td>
                            </tr>
                            <tr>
                                <td style="width:210px">@lang('portal.transport')</td>
                                <td>{{$fixVal[0]->shipmentMethod}}</td>
                            </tr>
                            <tr>
                                <td style="width:210px">@lang('portal.prefential')</td>
                                <td>Y</td>
                            </tr>
                            <tr>
                                <td style="width:210px">@lang('portal.gr_weight') (kg)</td>
                                <td> - </td>
                            </tr>
                            </tbody>
                        </table>

                    </td>
                    <td>

                        <table style="width: 100%;" border="0" cellspacing="0" cellpadding="0">
                            <tbody>
                            <tr>
                                <td style="width:120px">Trailer</td>
                                <td colspan="3"> - </td>
                            </tr>
                            <tr>
                                <td style="width:120px">@lang('portal.sealNumber')</td>
                                <td> - </td>
                                <td style="width:120px">@lang('portal.departureDate')</td>
                                <td>{{date('d-m-Y', strtotime($fixVal[0]->cargoReadyDate))}}</td>
                            </tr>
                            <tr>
                                <td style="width:120px">@lang('portal.volume')</td>
                                <td>-</td>
                                <td style="width:120px">@lang('portal.netWeight') (kg)</td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td style="width:120px">@lang('portal.loading')</td>
                                <td>{{$fixVal[0]->portLoadingCode}}</td>
                                <td style="width:120px">@lang('portal.finalDestination')</td>
                                <td>{{$fixVal[0]->finalWarehouseDesc}}</td>
                            </tr>
                            <tr>
                                <td style="width:120px">@lang('portal.deliveryDate')</td>
                                <td>{{$fixVal[0]->deliveryDate}}</td>
                                <td style="width:120px">@lang('portal.deliveryTime')</td>
                                <td> - </td>
                            </tr>
                            </tbody>
                        </table>

                    </td>
                </tr>
                </tbody>
            </table>
            <h3 style="text-align: center;"></h3>
            <table style="width: 100%;" border="1">
                <tbody>
                <tr>

                    @foreach($tableData["th"] as $item)
                    <td>@lang($item)</td>
                    @endforeach

                </tr>
                @foreach($tableData as $key => $val)
                    @if($key!= "th")
                    <tr>
                        <td>{{$tableData[$key]['carton']}}</td>
                        @foreach($tableData[$key]['epcs'] as $epc)
                            <td>
                                {{$epc}}
                            </td>
                        @endforeach
                        <td>{{$tableData[$key]['picies']}}</td>
                        <td>{{$tableData[$key]['inValidTotal']}}</td>
                        <td>{{$tableData[$key]['countofCartons']}}</td>
                        <td>{{$tableData[$key]['rowTotal']}}</td>
                    </tr>
                  @endif
                @endforeach
                </tbody>
            </table>

        </div>
    </body>
</html>
