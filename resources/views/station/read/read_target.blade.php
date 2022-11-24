<!--  Datatables -->
<div class="row content">
    <div class="col-lg-9">
        <div class="row tables">
            <div class="col-lg-10">
                <div class="table-ss">
                    <table
                        cellpadding="0"
                        cellspacing="0"
                        class="table table-bordered custom-table"
                        id="consignmentList"
                        style="font-size: 12px;"
                    >
                    <thead>
                        <tr >
                            <td colspan="5" class="th-title">@lang('station.read_packages')</td>
                        </tr>
                        <tr>
                            <th style="width:20px!important;">
                                <div  class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="checkAll">
                                    <label class="custom-control-label" for="checkAll"></label>
                                </div>
                            </th>
                            <th style="width:30px!important; ">@lang('station.package')</th>
                            <th>@lang('station.description')</th>
                            <th style="width:20px!important; text-align: center;">@lang('station.count')</th>
                            <th style="width:30px!important; text-align: center;">@lang('station.size')</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="table-ss">
                    <table
                        cellpadding="0"
                        cellspacing="0"
                        class="table table-bordered custom-table"
                        id="consignmentDetails"
                        style="font-size: 12px;"
                    >
                    <thead>
                        <tr>
                            <td colspan="2" class="th-title">@lang('station.details')</td>
                        </tr>
                        <tr>
                            <th>@lang('station.size')</th>
                            <th>@lang('station.count')</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="consignment-total">
                    <div class="col-4">@lang('station.consignment_total')</div>
                    <div class="col-8">
                        <span id="totalQuantity">0</span>
                        <span class="h3">/</span>
                        <span class="h5" id="itemCount">0</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="package-total">
                    <div class="col-5">@lang('station.package_total')</div>
                    <div class="col-7"><span id="selectedQuantity">0</span></div>
                </div>
            </div>
        </div>
    </div>
    @include('station.read.menuButon')
</div>
