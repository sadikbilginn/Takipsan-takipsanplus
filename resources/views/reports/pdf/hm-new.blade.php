<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $title }} </title>
</head>
<style>
    @page {
        margin: 0px;
    }

    body {
        font-family: "DeJaVu Sans Mono", Helvetica, Arial, sans-serif;
        margin: 15px 15px 30px 15px;
        font-size: 11px;
    }

    .content {
        margin: 15px 15px 30px 15px;
    }

    table {
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid #ccc;
        padding: 10px;
        text-align: left;
        vertical-align: top;
    }

    /* tr:nth-child(even) {
        background-color: #eee;
    }
    tr:nth-child(odd) {
        background-color: #fff;
    } */
    .bg {
        background-color: #7bb636;
        border-bottom: 5px solid #679b26;
        padding: 20px;
        margin-bottom: 20px;
        color: #fff;
    }

    .logo {
        float: left;
        font-weight: bold;
    }

    .title {
        float: left;
        font-weight: bold;
        margin-left: 20px;
    }

    .date {
        float: right;
        font-weight: bold;
    }

    .clearfix {
        overflow: auto;
    }

    .clearfix::after {
        content: "";
        clear: both;
        display: table;
    }

    .page-break {
        page-break-after: always;
    }

    .table-main {
        width: 100%;
        font-family: Arial, serif;
        font-size: 14px;
        color: rgb(0, 0, 0);
        font-weight: normal;
        font-style: normal;
        text-decoration: none;
    }

    th {
        text-align: center;
        vertical-align: middle;
    }

    p {
        font-weight: bold;
        font-size: 16px;
    }

    .borderless td {
        border: none !important;
    }

</style>

<body>
    <div class="bg clearfix">
        <div class="logo"><img src="{{ asset('assets/media/logos/takipsan.svg') }}" width="150px"></div>
        <div class="title">
            {{ getSettings('company_name', app()->getLocale()) }} <br>
            {{ $consignment->company->name }} <br>
            <small>By Takipsan</small>
        </div>
        <div class="date">{{ getLocaleDate(date('d-m-Y H:i:s')) }}</div>
    </div>

    <div class="content">

        <table class="table-main borderless">
            <tbody>
                <tr>
                    <td>
                        Packing List
                    </td>
                    <td>
                        {{ $consignment->delivery_date }}
                    </td>
                </tr>
                <tr>
                    <td>
                        Order/Season/PM/Delivery no: {{ $consignment->name }}
                    </td>
                    <td>
                        Packing list no: ?
                    </td>
                </tr>
                <tr>
                    <td>
                        Department/Sub index: ?
                    </td>
                    <td>
                        Carton Marking v. 3
                    </td>
                </tr>
                <tr>
                    <td>
                        Product ID: {{ $productId }}
                    </td>
                    <td>

                    </td>
                </tr>
            </tbody>
        </table>
        <br>
        <table class="table-main">
            <tbody>
                <tr>
                    <td style="width:50%">
                        Supplier Name & Supplier ID<br>
                        {{ $consignment->company->name }}<br>
                        ?
                    </td>
                    <td style="width:50%">
                        Final receiver<br>
                        {{ $consignment->consignee->name }}<br>
                        {{ $consignment->consignee->address }}
                    </td>
                </tr>
            </tbody>
        </table>
        
        <br>

        @php
            $curInx = 0;
            $sizeModelCount = [];
            $solidCnt = 0;
            $sLCount = 0;
            $assortCount = 0;
            foreach ($packageLoadSizes as $ky => $value) {
                $ld = explode('-', $ky)[0];
                if ($ld == 'Solid') {
                    $solidCnt = $solidCnt + 1;
                }
                if ($ld == 'Solid Last') {
                    $sLCount = $sLCount + 1;
                }
                if ($ld == 'Assortment') {
                    $assortCount = $assortCount + 1;
                }
            }
        @endphp

        @isset($packageTypes)
            <table class="table-main" width="100%">
                <tbody>
                    <tr class="table-header">
                        <th rowspan="2">
                            Ctn
                        </th>
                        <th rowspan="2">
                            Msrmnt (cm)
                        </th>
                        <th rowspan="2">
                            Total (Qty)
                        </th>
                        <th rowspan="2" colspan="2">
                            Packing mode
                        </th>
                        <th rowspan="2">
                            Art no
                        </th>
                        <th rowspan="2">
                            P/T Art No
                        </th>
                        <th colspan="2">
                            Ctns/Col
                        </th>
                        <th rowspan="2">
                            Qty/Col
                        </th>
                        <th rowspan="2">
                            Asst/Col
                        </th>
                    </tr>
                    <tr>
                        <td align="center">
                            Solid
                        </td>
                        <td align="center">
                            Asst
                        </td>
                    </tr>


                    @foreach ($packageTypes as $key => $value)
                        <tr>
                            <td rowspan="{{ count($loadTypes) }}">
                                {{ $key }}
                            </td>
                            <td rowspan="{{ count($loadTypes) }}">
                                {{ getPackageMsmrnt($boxTypes, $key) }}
                            </td>
                            <td rowspan="{{ count($loadTypes) }}">
                                {{ getPackageTypeCount($consignment->packages, $key) }}
                            </td>
                    @endforeach

                    @foreach ($loadTypes as $key => $value)
                            <td>
                                {{ $key }}
                            </td>
                            <td>
                                {{ count($loadTypes) }}
                            </td>
                            <td>
                                {{ $artNo }}
                            </td>
                            <td>
                                ?
                            </td>
                            <td>
                                {{ getPackageCountByLT($consignment->packages, 'Solid') }}
                            </td>
                            <td>
                                {{ getPackageCountByLT($consignment->packages, 'Assortment') }}
                                {{-- getPackageCountByLT($consignment->packages,"SolidLast") --}}
                            </td>
                            <td>
                                {{ $totalCount }}
                            </td>
                            <td>
                                {{ getAssrCount($consignment->packages) }}
                            </td>
                        </tr>
                    @endforeach


                    {{-- Eski Bozuk Olan --}}
                    {{-- @isset($packageTypes)
                        @php 
                            $i = 0;
                            $arrLoad = [];
                            $arrPackage = [];
                            print_r($loadTypes);
                            foreach($loadTypes as $key => $type){
                                $arrLoad[] = $key;
                            }
                            foreach($packageTypes as $key => $type){
                                $arrPackage[] = $key;
                            }
                        @endphp

                        @if (count($loadTypes) > count($packageTypes))
                            @php $dongu = $loadTypes; @endphp
                        @else
                            @php $dongu = $packageTypes; @endphp
                        @endif
                    @endisset

                    @foreach ($dongu as $key => $value)
                        <tr>
                            <td>
                                @isset( $arrPackage[$i] )
                                    {{ $arrPackage[$i] }}
                                @endisset
                            </td>
                            <td>
                                {{ getPackageMsmrnt($boxTypes, $key) }}
                            </td>
                            <td>
                                {{ getPackageTypeCount($consignment->packages, $key) }}
                            </td>
                            <td>
                                {{ $arrLoad[$i] ? $arrLoad[$i] : '' }}
                            </td>
                            <td>
                                {{ $arrLoad[$i] == $packingMode ? 'X' : '' }}
                            </td>
                            <td>
                                @if ($loop->first)
                                    {{ $artNo }}
                                @endif
                            </td>
                            <td>
                                @if ($loop->first)
                                    ?
                                @endif
                            </td>
                            <td>
                                @if ($loop->first)
                                    {{ getPackageCountByLT($consignment->packages, 'Solid') }}
                                @endif
                                @if ($loop->first + 1)
                                    Mixed
                                @endif
                            </td>
                            <td>
                                @if ($loop->first)
                                    {{ getPackageCountByLT($consignment->packages, 'Assortment') }}
                                @endif
                                @if ($loop->first + 1)
                                    {{ getPackageCountByLT($consignment->packages, "SolidLast") }}
                                @endif
                            </td>
                            <td>
                                @if ($loop->first)
                                    {{ $totalCount }}
                                @endif
                            </td>
                            <td>
                                @if ($loop->first)
                                    {{ getAssrCount($consignment->packages) }}
                                @endif
                            </td>
                        </tr>
                        @php $i++; @endphp
                    @endforeach --}}




                    {{-- <tr>
                <td>
                    Solid
                </td>
                <td>
                    Asst
                </td>
            </tr>
        <td colspan="2">Packaging mode</td>
    <tr>
        <td>Asst</td>
        @if (count($loadTypes) == 1 && array_key_exists('Assortment', $loadTypes))
            <td>X</td>
        @else
            <td></td>
        @endif
    </tr>
    <tr>
        <td>Mixed</td>
        @if (count($loadTypes) > 1)
            <td>X</td>
        @else
            <td></td>
        @endif
    </tr>
    <tr>
        <td>Solid</td>
        @if (count($loadTypes) == 1 && array_key_exists('Solid', $loadTypes))
            <td>X</td>
        @else
            <td></td>
        @endif
    </tr>
                
            <tr>
                <td>
                    {{getPackageCountByLT($consignment->packages,"Solid")}}
                </td>
                <td>
                    {{getPackageCountByLT($consignment->packages,"Assortment")}}
                </td>
                <td>
                    {{$totalCount}}
                </td>
                <td>
                    {{getAssrCount($consignment->packages)}}
                </td>
            </tr>
            <tr>
                <td colspan="2">
                Mixed : {{getPackageCountByLT($consignment->packages,"SolidLast")}}
                </td>
                <td  colspan="2">
                    Pcs incl. in totals
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    {{count($consignment->packages)}}
                </td>
                <td>
                    {{$totalCount}}
                </td>
                <td>
                    {{getAssrCount($consignment->packages)}}
                </td>
            </tr> --}}
                </tbody>
            </table>
        @endisset

        <br>

        <p style="margin-top:30px;">SS/SC full packing units</p>

        <table class="table-main border-table">
            <tr class="table-header">
                <th rowspan="2">Ctn</th>
                <th rowspan="2">Ctn no</th>
                <th rowspan="2">Ctn qty</th>
                <th rowspan="2">Art no</th>
                <th colspan="{{ count($sizes) }}" style="text-align: center;">Solid pcs per size</th>
                <th rowspan="2" style="text-align: center;">Items/Ctn</th>
            <tr class="table-header" style="text-align: center;">
                @foreach ($sizes as $ky => $value)
                    <td align="center">{{ $ky }}</td>
                @endforeach
            </tr>
            </tr>
            @foreach ($packageTypes as $key => $value)
                <tr>
                    <td>{{ $key }} {{ getPackageMsmrnt($boxTypes, $key) }} </td>
                    <td>{{ getPackageNosByType($consignment->packages, $key) }}</td>
                    <td>{{ $value }}</td>
                    <td>{{ $artNo }}</td>
                    @foreach ($sizes as $ky => $value)
                        <td>{{ getPackageSizeCount($consignment->packages, $ky, $key) }}</td>
                    @endforeach
                    <td style="text-align: center;">{{ getPackageTypeCount($consignment->packages, $key) }}</td>
                </tr>

            @endforeach
        </table>

        @foreach ($loadTypes as $key => $lt)
            @if ($key == 'Assortment')
                <p style="margin-top:30px;">{{ $key }} qty/Article (colour) & size</p>
                <table class="table-main border-table">
                    <tr class="table-header">
                        <th rowspan="2">Art no</th>
                        <th rowspan="2">Art mark</th>
                        <th rowspan="2">P/T art no</th>
                        <th rowspan="2">Items/Asst</th>
                        <th colspan="{{ $assortCount }}" style="text-align: center;">Asst: Total pcs per article
                            (colour) and size</th>
                        <th rowspan="2" style="text-align: center;">Asst: Total pcs / Article (col) </th>
                    </tr>
                    <tr class="table-header" style="text-align: center;">
                        @foreach ($packageLoadSizes as $ky => $value)
                            @if (explode('-', $ky)[0] == $key)
                                <td align="center" style="padding-bottom:15px !important;">{{ explode('-', $ky)[1] }}
                                </td>
                            @endif
                        @endforeach
                    </tr>
                    <tr>
                        <td>{{ $artNo }}</td>
                        <td>{{ getPackageCountByLT($consignment->packages, 'Assortment') }}</td>
                        @foreach ($packageLoadSizes as $ky => $value)
                            @if (explode('-', $ky)[0] == $key)
                                <td style="padding-bottom:15px !important;">{{ $value }}</td>
                            @endif
                        @endforeach
                        <td style="text-align: center;">{{ $lt }}</td>
                    </tr>
                </table>
            @endif
            @if ($key == 'Solid')
                <br>
                <br>
                <br>

                <br>
                <p style="margin-top:30px;">{{ $key }} qty/Article (colour) & size</p>
                <table class="table-main border-table">
                    <tr class="table-header">
                        <th rowspan="2">Art no</th>
                        <th rowspan="2">Items/Asst</th>
                        <th colspan="{{ $solidCnt }}" style="text-align: center;">Asst: Total pcs per article
                            (colour) and size</th>
                        <th rowspan="2" style="text-align: center;">Asst: Total pcs / Article (col) </th>
                    </tr>
                    <tr class="table-header" style="text-align: center;">
                        @foreach ($packageLoadSizes as $ky => $value)
                            @if (explode('-', $ky)[0] == $key)
                                <td align="center" style="padding-bottom:15px !important;">{{ explode('-', $ky)[1] }}
                                </td>
                            @endif
                        @endforeach
                    </tr>
                    <tr>
                        <td>{{ $artNo }}</td>
                        <td>{{ getPackageCountByLT($consignment->packages, 'Solid') }}</td>
                        @foreach ($packageLoadSizes as $ky => $value)
                            @if (explode('-', $ky)[0] == $key)
                                <td style="padding-bottom:15px !important;">{{ $value }}</td>
                            @endif
                        @endforeach
                        <td style="text-align: center;">{{ $lt }}</td>
                    </tr>
                </table>
            @endif
            @if ($key == 'SolidLast')
                <p style="margin-top:30px;">Total qty/Article (colour) & size</p>
                <table class="table-main border-table">
                    <tr class="table-header">
                        <th rowspan="2">Art no</th>
                        <th rowspan="2">Items/Asst</th>
                        <th colspan="{{ $sLCount }}" style="text-align: center;">Asst: Total pcs per article
                            (colour) and size</th>
                        <th rowspan="2" style="text-align: center;">Asst: Total pcs / Article (col) </th>
                    </tr>
                    <tr class="table-header" style="text-align: center;">
                        @foreach ($packageLoadSizes as $ky => $value)
                            @if (explode('-', $ky)[0] == $key)
                                <td style="padding-bottom:15px !important;font-size: 7pt">{{ explode('-', $ky)[1] }}
                                </td>
                            @endif
                        @endforeach
                    </tr>
                    <tr>
                        <td>{{ $artNo }}</td>
                        <td>{{ getPackageCountByLT($consignment->packages, 'SolidLast') }}</td>
                        @foreach ($packageLoadSizes as $ky => $value)
                            @if (explode('-', $ky)[0] == $key)
                                <td style="padding-bottom:15px !important;">{{ $value }}</td>
                            @endif
                        @endforeach
                        <td style="text-align: center;">{{ $lt }}</td>
                    </tr>
                </table>
            @endif
        @endforeach
