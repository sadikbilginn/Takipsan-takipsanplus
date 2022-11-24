<table>
    <tbody>
    <tr>
        <td></td>
        <td>@lang('portal.msHeader')</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td><b>@lang('portal.supplier')</b></td>
        <td>{{$fixVal[0]->supplierDesc ? $fixVal[0]->supplierDesc : '-'}}</td>
        <td></td>
        <td></td>
        <td style="width:60px"><b>@lang('portal.seriesNo')</b></td>
        <td>{{json_decode($fixVal[0]->cartons, true)[0]['series']}}</td>
        <td style="width:60px"><b>M&#38;S Dept.</b></td>
        <td>{{$fixVal[0]->dep}}</td>
    </tr>
    <tr>
        <td><b>@lang('portal.address')</b></td>
        <td> -</td>
        <td></td>
        <td></td>
        <td style="width:60px"><b>PO No:</b></td>
        <td>{{$fixVal[0]->poNumber}}</td>
        <td><b>Stroke No</b></td>
        <td>{{$fixVal[0]->stroke}}</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td><b>@lang('portal.colour')</b></td>
        <td>{{$fixVal[0]->colourCode}} - {{$fixVal[0]->colourDesc}}</td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td><b>@lang('portal.factory')</b></td>
        <td>{{$fixVal[0]->factoryDescription ? $fixVal[0]->factoryDescription : '-'}}</td>
        <td></td>
        <td></td>
        <td><b>@lang('portal.description')</b></td>
        <td>{{$fixVal[0]->strokeDesc}}</td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td><b>@lang('portal.address')</b></td>
        <td> -</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td><b>@lang('portal.comment')</b></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>

    <tr>
        <td><b>@lang('portal.packingList')</b></td>
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
        <td></td>
        <td><b>@lang('portal.Trailer')</b></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td><b>@lang('portal.transport')</b></td>
        <td>{{$fixVal[0]->shipmentMethod}}</td>
        <td></td>
        <td><b>@lang('portal.sealNumber')</b></td>
        <td></td>
        <td></td>
        <td><b>@lang('portal.departureDate')</b></td>
        <td>{{date('d-m-Y', strtotime($fixVal[0]->cargoReadyDate))}}</td>
    </tr>
    <tr>
        <td><b>@lang('portal.prefential')</b></td>
        <td></td>
        <td></td>
        <td><b>@lang('portal.volume')</b></td>
        <td></td>
        <td></td>
        <td><b>@lang('portal.netWeight') (kg)</b></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td><b>@lang('portal.loading')</b></td>
        <td></td>
        <td>{{$fixVal[0]->portLoadingCode}}</td>
        <td><b>@lang('portal.finalDestination')</b></td>
        <td>{{$fixVal[0]->finalWarehouseDesc}}</td>
    </tr>
    <tr>
        <td><b>@lang('portal.gr_weight') (kg)</b></td>
        <td></td>
        <td></td>
        <td><b>@lang('portal.deliveryDate')</b></td>
        <td>{{$fixVal[0]->deliveryDate}}</td>
        <td></td>
        <td><b>@lang('portal.deliveryTime')</b></td>
        <td></td>
    </tr>
    </tbody>
</table>
@php

    $rowCnt = 0;
    $tableCnt = count($tableData) - 1;
    //$tableCnt2 = 20 / 4;
    if ($tableCnt % 4 == 0){

        $tableSize = floor ($tableCnt / 4);

    }else{

        $tableSize = floor ($tableCnt / 4) + 1;

    }
    //$tableCnt2 = 5;
    $myIterator =0;
@endphp

@for($i = 0; $i < $tableSize; $i++)

    <table border="1">
        <tbody>
        <tr>
            <td><b>@lang($tableData["th"][0])</b></td>
            <td><b>{!!$tableData["th"][$myIterator+1]!!}</b></td>
            <td><b>{!!$tableData["th"][$myIterator+2]!!}</b></td>
            <td><b>{!!$tableData["th"][$myIterator+3]!!}</b></td>
            <td><b>{!!$tableData["th"][$myIterator+4]!!}</b></td>
            <td><b>@lang($tableData["th"][count($tableData['th'])-4])</b></td>
            <td><b>@lang($tableData["th"][count($tableData['th'])-3])</b></td>
            <td><b>@lang($tableData["th"][count($tableData['th'])-2])</b></td>
            <td><b>@lang($tableData["th"][count($tableData['th'])-1])</b></td>
        </tr>
        @if(array_key_exists($myIterator, $tableData))
            <tr>
            <td>
                @php
                    if (!empty($tableData[$myIterator]['carton'])){
                        $min = min(explode(',',$tableData[$myIterator]['carton']));
                        $max = max(explode(',',$tableData[$myIterator]['carton']));
                        $min != $max ?    $value = $min.'-'. $max :    $value = $min;
                    }
                @endphp
                {{!empty($value) ? $value : '-'}}
            </td>

            <td>{{array_key_exists($myIterator, $tableData[$myIterator]['epcs']) ? $tableData[$myIterator]['epcs'][$myIterator] : ''}}</td>
            <td>{{array_key_exists($myIterator+1, $tableData[$myIterator]['epcs']) ? $tableData[$myIterator]['epcs'][$myIterator+1] : '-'}}</td>
            <td>{{array_key_exists($myIterator+2, $tableData[$myIterator]['epcs']) ? $tableData[$myIterator]['epcs'][$myIterator+2] : '-'}}</td>
            <td>{{array_key_exists($myIterator+3, $tableData[$myIterator]['epcs']) ? $tableData[$myIterator]['epcs'][$myIterator+3] : '-'}}</td>
            <td>{{!empty($tableData[$myIterator]['picies']) ? $tableData[$myIterator]['picies'] : '-'}}</td>
            <td>{{!empty($tableData[$myIterator]['inValidTotal']) && $tableData[$myIterator]['inValidTotal'] != 0 ? $tableData[$myIterator]['inValidTotal'].'' : '0' }}</td>
            <td>{{!empty($tableData[$myIterator]['countofCartons']) ? $tableData[$myIterator]['countofCartons'] : '-'}}</td>
            <td>{{!empty($tableData[$myIterator]['rowTotal']) && $tableData[$myIterator]['rowTotal'] != 0 ? $tableData[$myIterator]['rowTotal'].'' : '0'}}</td>
        </tr>

        @endif
        @if(array_key_exists($myIterator+1, $tableData))
            <tr>
                <td>
                    @php
                        if (!empty($tableData[$myIterator+1]['carton'])){
                            $min = min(explode(',',$tableData[$myIterator+1]['carton']));
                            $max = max(explode(',',$tableData[$myIterator+1]['carton']));
                            $min != $max ?    $value = $min.'-'. $max :    $value = $min;
                        }
                    @endphp
                    {{!empty($value) ? $value : '-'}}
                </td>
                <td>{{array_key_exists($myIterator, $tableData[$myIterator+1]['epcs']) ? $tableData[$myIterator+1]['epcs'][$myIterator] : '-'}}</td>
                <td>{{array_key_exists($myIterator+1, $tableData[$myIterator+1]['epcs']) ? $tableData[$myIterator+1]['epcs'][$myIterator+1] : '-'}}</td>
                <td>{{array_key_exists($myIterator+2, $tableData[$myIterator+1]['epcs']) ? $tableData[$myIterator+1]['epcs'][$myIterator+2] : '-'}}</td>
                <td>{{array_key_exists($myIterator+3, $tableData[$myIterator+1]['epcs']) ? $tableData[$myIterator+1]['epcs'][$myIterator+3] : '-'}}</td>
                <td>{{!empty($tableData[$myIterator+1]['picies']) ? $tableData[$myIterator+1]['picies'] : '-'}}</td>
                <td>{{!empty($tableData[$myIterator+1]['inValidTotal']) && $tableData[$myIterator+1]['inValidTotal'] != 0 ? $tableData[$myIterator+1]['inValidTotal'].'' : '0 '}}</td>
                <td>{{!empty($tableData[$myIterator+1]['countofCartons']) ? $tableData[$myIterator+1]['countofCartons'] : '-'}}</td>
                <td>{{!empty($tableData[$myIterator+1]['rowTotal']) && $tableData[$myIterator]['rowTotal'] != 0 ? $tableData[$myIterator+1]['rowTotal'].'' : '0 '}}</td>
            </tr>
        @endif
        @if(array_key_exists($myIterator+2, $tableData))
            <tr>
                <td>
                    @php
                        if (!empty($tableData[$myIterator+2]['carton'])){
                            $min = min(explode(',',$tableData[$myIterator+2]['carton']));
                            $max = max(explode(',',$tableData[$myIterator+2]['carton']));
                            $min != $max ?    $value = $min.'-'. $max :    $value = $min;
                        }
                    @endphp
                    {{!empty($value) ? $value : '-'}}
                </td>
                <td>{{array_key_exists($myIterator, $tableData[$myIterator+2]['epcs']) ? $tableData[$myIterator+2]['epcs'][$myIterator] : '-'}}</td>
                <td>{{array_key_exists($myIterator+1, $tableData[$myIterator+2]['epcs']) ? $tableData[$myIterator+2]['epcs'][$myIterator+1] : '-'}}</td>
                <td>{{array_key_exists($myIterator+2, $tableData[$myIterator+2]['epcs']) ? $tableData[$myIterator+2]['epcs'][$myIterator+2] : '-'}}</td>
                <td>{{array_key_exists($myIterator+3, $tableData[$myIterator+2]['epcs']) ? $tableData[$myIterator+2]['epcs'][$myIterator+3] : '-'}}</td>
                <td>{{!empty($tableData[$myIterator+2]['picies']) ? $tableData[$myIterator+2]['picies'] : '-'}}</td>
                <td>{{!empty($tableData[$myIterator+2]['inValidTotal']) && $tableData[$myIterator+2]['inValidTotal'] != 0 ? $tableData[$myIterator+2]['inValidTotal'].'' : '0 '}}</td>
                <td>{{!empty($tableData[$myIterator+2]['countofCartons']) ? $tableData[$myIterator+2]['countofCartons'] : '-'}}</td>
                <td>{{!empty($tableData[$myIterator+2]['rowTotal']) && $tableData[$myIterator]['rowTotal'] != 0 ? $tableData[$myIterator+2]['rowTotal'].'' : '0 '}}</td>
            </tr>
        @endif
        @if(array_key_exists($myIterator+2, $tableData))
            <tr>
                <td>
                    @php
                        if (!empty($tableData[$myIterator+3]['carton'])){
                            $min = min(explode(',',$tableData[$myIterator+3]['carton']));
                            $max = max(explode(',',$tableData[$myIterator+3]['carton']));
                            $min != $max ?    $value = $min.'-'. $max :    $value = $min;
                        }
                    @endphp
                    {{!empty($value) ? $value : '-'}}
                </td>
                <td>{{array_key_exists($myIterator, $tableData[$myIterator+3]['epcs']) ? $tableData[$myIterator+3]['epcs'][$myIterator] : '-'}}</td>
                <td>{{array_key_exists($myIterator+1, $tableData[$myIterator+3]['epcs']) ? $tableData[$myIterator+3]['epcs'][$myIterator+1] : '-'}}</td>
                <td>{{array_key_exists($myIterator+2, $tableData[$myIterator+3]['epcs']) ? $tableData[$myIterator+3]['epcs'][$myIterator+2] : '-'}}</td>
                <td>{{array_key_exists($myIterator+3, $tableData[$myIterator+3]['epcs']) ? $tableData[$myIterator+3]['epcs'][$myIterator+3] : '-'}}</td>
                <td>{{!empty($tableData[$myIterator+3]['picies']) ? $tableData[$myIterator+3]['picies'] : '-'}}</td>
                <td>{{!empty($tableData[$myIterator+3]['inValidTotal']) && $tableData[$myIterator+3]['inValidTotal'] != 0 ? $tableData[$myIterator+3]['inValidTotal'].'' : '0 '}}</td>
                <td>{{!empty($tableData[$myIterator+3]['countofCartons']) ? $tableData[$myIterator+3]['countofCartons'] : '-'}}</td>
                <td>{{!empty($tableData[$myIterator+3]['rowTotal']) && $tableData[$myIterator]['rowTotal'] != 0 ? $tableData[$myIterator+3]['rowTotal'].'' : '0 '}}</td>
            </tr>
        @endif

        </tbody>
    </table>

    @php
        $myIterator+=4;
    @endphp
@endfor
