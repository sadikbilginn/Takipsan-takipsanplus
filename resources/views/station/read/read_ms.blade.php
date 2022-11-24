<style>
    .table td, .table th {
        padding-right: 0 !important
    }
</style>
<!-- commit !-->

<!--  Datatables -->
<div class="row content">
    <div class="col-lg-9">
        <div class="row tables">
            <div class="col-lg-12">

                <div class="table-ss overflow-auto">
                    <table id="msDataTable" class="cell-border">
                        <thead>
                        <th></th>
                        <th>U.P.C.</th>
                        <th>@lang('station.size')</th>
                        <th>@lang('station.target_qty')</th>
                        <th>@lang('station.read_quantity')</th>
                        <th>@lang('station.invalidQuantity')</th>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>

            </div>

            <div class="col-lg-12">
                <div
                    class="consignment-total"
                    style="width:100%; height:auto!important; line-height:88px; display:inline-block;"
                >
                    <div class="col-3">
                        <div class="row">
                            <div
                                class="col-8"
                                style="font-size: 18px;line-height:88px; padding-left:0; padding-right:0;"
                            >
                                @lang('station.consignment_total')
                            </div>
                        </div>
                    </div>
                    <div class="col-9">

                        <div class="row">

                            {{--toplam koli adedi--}}
                            <div
                                class="col-2"
                                style="
                                    padding-left:0;
                                    padding-right:0;
                                    padding-top:10px;
                                    border-right: 1px solid #D8E1EC;
                                "
                            >
                                <span class="h5" id="totalCarton" style="line-height:20px; display:block;">
                                    0
                                </span>
                                <span style="font-size:13px; line-height:20px!important; display:block;">
                                    @lang('station.totalCarton')
                                </span>
                            </div>

                            {{--anlık olarak okunan koli adedi--}}
                            <div
                                class="col-2"
                                style="
                                    padding-left:0;
                                    padding-right:0;
                                    padding-top:10px;
                                    border-right: 1px solid #D8E1EC;
                                "
                            >
                                <span class="h5" id="totalReadCarton" style="line-height:20px; display:block;">
                                    0
                                </span>
                                <span style="font-size:13px; line-height:20px!important; display:block;">
                                    @lang('station.totalReadCarton')
                                </span>
                            </div>

                            {{--toplam ürün adedi--}}
                            <div
                                class="col-2"
                                style="
                                    padding-left:0;
                                    padding-right:0;
                                    padding-top:10px;
                                    border-right: 1px solid #D8E1EC;
                                "
                            >
                                <span class="h5" id="itemCount" style="line-height:20px; margin:0; display:block;">
                                    0
                                </span>
                                <span style="font-size:13px; line-height:20px!important; display:block;">
                                    @lang('station.itemCount')
                                </span>
                            </div>

                            {{--anlik okunan ürün adedi--}}
                            <div
                                class="col-3"
                                style="
                                    padding-left:0;
                                    padding-right:0;
                                    padding-top:10px;
                                    border-right: 1px solid #D8E1EC;
                                "
                            >
                                <span
                                    class="h5"
                                    id="totalQuantity"
                                    style="line-height:20px; margin:0; display:block;"
                                >
                                    0
                                </span>
                                <span style="font-size:13px; line-height:20px!important; display:block;">
                                    @lang('station.totalQuantity')
                                </span>
                            </div>

                            {{--gecersiz miktar--}}
                            <div class="col-3" style="line-height:88px; padding-left:0; padding-right:0;">
                                <span
                                    class="h5"
                                    id="invalidCount"
                                    style="line-height:88px; margin:0; display:block; background:#ff5050; color:#fff"
                                >
                                    0
                                </span>
                            </div>

                            {{--<div class="col-3" style="line-height:88px; padding-left:0; padding-right:0; ">
                                <span
                                    class="h5"
                                    id="totalFileQuantity"
                                    style="line-height:88px; margin:0; display:block; background:#1C7EFC; color:#fff"
                                >
                                    0
                                </span>
                            </div>--}}

                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
    @include('station.read.menuButon')
</div>
