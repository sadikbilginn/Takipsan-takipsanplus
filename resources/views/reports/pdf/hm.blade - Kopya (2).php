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
    .content{
        margin: 15px 15px 30px 15px;
    }
    table {
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid #ccc;
        padding: 10px;
        text-align: left;
        vertical-align: top;
    }
    tr:nth-child(even) {
        background-color: #eee;
    }
    tr:nth-child(odd) {
        background-color: #fff;
    }
    .bg{ background-color: #7bb636; border-bottom: 5px solid #679b26; padding: 20px; margin-bottom: 20px; color: #fff;}
    .logo{ float: left; font-weight: bold;}
    .title{ float: left; font-weight: bold; margin-left: 20px;}
    .date{  float: right; font-weight: bold;}
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
    .table-main
    {
        width: 100%;
        font-family:Arial,serif;
        font-size:12.1px;
        color:rgb(0,0,0);
        font-weight:normal;
        font-style:normal;
        text-decoration: none;
    }
    .table-main td{
        /* border:none; */
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
    <div class="date">{{ getLocaleDate(date('d-m-Y H:i:s'))}}</div>
</div>

<div class="content">

    <table class="table-main" style="font-size: 13pt;">
        <tbody>
            <tr>
                <td style="padding-bottom:30px">
                    Order/Season/PM/Delivery no: {{$consignment->name}}
                </td>
            </tr>
        </tbody>
    </table>

    @php
        $curInx = 0;
        $sizeModelCount = [];
        $solidCnt = 0;
        $sLCount = 0;
        $assortCount = 0;
        foreach ($packageLoadSizes as $ky => $value) {
            $ld = explode("-",$ky)[0];
            if ($ld == "Solid") {
                 $solidCnt = $solidCnt + 1;
            }
            if ($ld == "Solid Last") {
                 $sLCount = $sLCount + 1;
            }
            if ($ld == "Assortment") {
                 $assortCount = $assortCount + 1;
            }
        }
    @endphp

<table>
<tr>
    <td>
        <table style="width:%50" class="table-main border-table">
            <tbody>
                <tr class="table-header">
                    <td>
                        Ctn
                    </td>
                    <td>
                        Msrmnt (cm)
                    </td>
                    <td>
                        Total (Qty)
                    </td>
                    <td rowspan="">
                        Art no
                    </td>
                </tr>
                @foreach ($packageTypes as $key => $value)

                    <tr>
                        <td>
                            {{$key}}
                        </td>
                        <td>
                            {{getPackageMsmrnt($boxTypes,$key)}}
                        </td>
                        <td>
                            {{getPackageTypeCount($consignment->packages,$key)}}
                        </td>
                        <td class="">
                            {{$artNo}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </td>
    <td>


<table style="margin-top:20px">
    <tr>
        <td colspan="2">Packaging mode</td>
    </tr>
    <tr>
        <td>Asst</td>
        @if (count($loadTypes) == 1 && array_key_exists("Assortment",$loadTypes))
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
        @if (count($loadTypes) == 1 && array_key_exists("Solid",$loadTypes))
            <td>X</td>
        @else
            <td></td>
        @endif
    </tr>
    {{-- <tr>
        <td colspan="2"></td>
    </tr> --}}
</table>
    </td>
    <td>
        <table>
            <tr>
                <td colspan="2">
                    Ctns/Col
                </td>
                <td rowspan="2">
                    Qty/Col
                </td>
                <td rowspan="2">
                    Asst/Col
                </td>
            </tr>
            <tr>
                <td>
                    Solid
                </td>
                <td>
                    Asst
                </td>
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
            </tr>
        </table>
    </td>
</tr>
</table>


<p style="margin-top:30px; font-size: 14pt;">SS/SC full packing units</p>

@if ($assrSizes != null)
    <table class="table-main border-table">
        <tr class="table-header">
            <td rowspan="2">Ctn</td>
            <td rowspan="2">Ctn no</td>
            <td rowspan="2">Ctn qty</td>
            <td rowspan="2">Art no</td>
            <td colspan="{{count($assrSizes)}}" style="text-align: center;">Solid pcs per size</td>
            <td rowspan="2" style="text-align: center;">Items/Ctn</td>
            <tr class="table-header" style="text-align: center;">
                @foreach($assrSizes as $ky => $value)
                        <td>{{$ky}}</td>
                @endforeach
            </tr>
        </tr>
        @foreach($assrSizes as $ky => $value)
            <tr>
                <td>{{$key}} {{getPackageMsmrnt($boxTypes,$key)}}</td>
                <td>{{getPackageNosByType($consignment->packages,$key)}}</td>
                <td>{{$value}}</td>
                <td>{{$artNo}}</td>
                @foreach($assrSizes as $ky => $value)
                    <td>{{getPackageSizeCount($consignment->packages,$ky,$key )}}</td>
                @endforeach
                <td style="text-align: center;">{{getPackageTypeCount($consignment->packages,$key)}}</td>
            </tr>
        @endforeach
        @foreach ($packageTypes as $key => $value)
            <tr>
                <td>{{$key}} {{getPackageMsmrnt($boxTypes,$key)}}</td>
                <td>{{getPackageNosByType($consignment->packages,$key)}}</td>
                <td>{{$value}}</td>
                <td>{{$artNo}}</td>
                @foreach($sizes as $ky => $value)
                    <td>{{getPackageSizeCount($consignment->packages,$ky,$key )}}</td>
                @endforeach
                <td style="text-align: center;">{{getPackageTypeCount($consignment->packages,$key)}}</td>
            </tr>

        @endforeach
    </table>
@endif


    @foreach ($loadTypes as $key => $lt)
        @if ($key == "Assortment")
            <p style="margin-top:30px; font-size: 14pt;">{{$key}} qty/Article (colour) & size</p>
            <table class="table-main border-table">
                <tr class="table-header">
                    <td rowspan="2">Art no</td>
                    <td rowspan="2">Items/Asst</td>
                    <td colspan="{{$assortCount}}" style="text-align: center;">Asst: Total pcs per article (colour) and size</td>
                    <td rowspan="2" style="text-align: center;">Asst: Total pcs / Article (col) </td>
                </tr>
                <tr class="table-header" style="text-align: center;">
                    @foreach($packageLoadSizes as $ky => $value)
                        @if (explode("-",$ky)[0] == $key)
                            <td style="padding-bottom:15px !important;font-size: 7pt">{{explode("-",$ky)[1]}}</td>
                        @endif
                    @endforeach
                </tr>
                <tr>
                    <td>{{$artNo}}</td>
                    <td>{{getPackageCountByLT($consignment->packages,"Assortment")}}</td>
                    @foreach($packageLoadSizes as $ky => $value)
                        @if (explode("-",$ky)[0] == $key)
                            <td style="padding-bottom:15px !important;">{{$value}}</td>
                        @endif
                    @endforeach
                    <td style="text-align: center;">{{$lt}}</td>
                </tr>
            </table>
        @endif
        @if ($key == "Solid")
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
            <p style="margin-top:30px; font-size: 14pt;">{{$key}} qty/Article (colour) & size</p>
            <table class="table-main border-table">
                <tr class="table-header">
                    <td rowspan="2">Art no</td>
                    <td rowspan="2">Items/Asst</td>
                    <td colspan="{{$solidCnt}}" style="text-align: center;">Asst: Total pcs per article (colour) and size</td>
                    <td rowspan="2" style="text-align: center;">Asst: Total pcs / Article (col) </td>
                </tr>
                <tr class="table-header" style="text-align: center;">
                    @foreach($packageLoadSizes as $ky => $value)
                        @if (explode("-",$ky)[0] == $key)
                            <td style="padding-bottom:15px !important;font-size: 7pt">{{explode("-",$ky)[1]}}</td>
                        @endif
                    @endforeach
                </tr>
                <tr>
                    <td>{{$artNo}}</td>
                    <td>{{getPackageCountByLT($consignment->packages,"Solid")}}</td>
                    @foreach($packageLoadSizes as $ky => $value)
                        @if (explode("-",$ky)[0] == $key)
                            <td style="padding-bottom:15px !important;">{{$value}}</td>
                        @endif
                    @endforeach
                    <td style="text-align: center;">{{$lt}}</td>
                </tr>
            </table>
        @endif
        @if ($key == "SolidLast")
            <p style="margin-top:30px; font-size: 14pt;">{{$key}} qty/Article (colour) & size</p>
            <table class="table-main border-table">
                <tr class="table-header">
                    <td rowspan="2">Art no</td>
                    <td rowspan="2">Items/Asst</td>
                    <td colspan="{{$sLCount}}" style="text-align: center;">Asst: Total pcs per article (colour) and size</td>
                    <td rowspan="2" style="text-align: center;">Asst: Total pcs / Article (col) </td>
                </tr>
                <tr class="table-header" style="text-align: center;">
                    @foreach($packageLoadSizes as $ky => $value)
                        @if (explode("-",$ky)[0] == $key)
                            <td style="padding-bottom:15px !important;font-size: 7pt">{{explode("-",$ky)[1]}}</td>
                        @endif
                    @endforeach
                </tr>
                <tr>
                    <td>{{$artNo}}</td>
                    <td>{{getPackageCountByLT($consignment->packages,"SolidLast")}}</td>
                    @foreach($packageLoadSizes as $ky => $value)
                        @if (explode("-",$ky)[0] == $key)
                            <td style="padding-bottom:15px !important;">{{$value}}</td>
                        @endif
                    @endforeach
                    <td style="text-align: center;">{{$lt}}</td>
                </tr>
            </table>
        @endif
    @endforeach
