<!--  Datatables -->
<div class="row content">
    <div class="col-lg-9">
        <div class="row tables">
            <div class="col-lg-9">
                <div class="table-ss">
                    <!-- Okunan Paketler -->
                    <table cellpadding="0" cellspacing="0" class="table table-bordered custom-table" id="consignmentList">
                        <thead>
                            <tr>
                                <td colspan="5" class="th-title">@lang('station.read_packages')</td>
                            </tr>
                            <tr>
                                <th>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="checkAll">
                                        <label class="custom-control-label" for="checkAll"></label>
                                    </div>
                                </th>
                                <th>@lang('station.package_no')</th>
                                <th>@lang('station.quantity')</th>
                                <th onclick="modelAllEdit();">@lang('station.model')</th>
                                <th onclick="sizeAllEdit();">@lang('station.size')</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="table-ss">
                    @if(config('license.integration') && config('license.integration') == '1')
                    <table 
                        cellpadding="0" 
                        cellspacing="0" 
                        class="table table-bordered custom-table" 
                        id="consignmentOrderDetails"
                    >
                        <thead>
                            <tr>
                                <td colspan="2" class="th-title">SİPARİŞ BİLGİLERİ</td>
                            </tr>
                            <tr>
                                <th></th>
                                <th>Miktar</th>
                                <th>Okuma</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-color-1">
                                <td class="model-name">1510 0706/386/707 TAN PANTALON</td>
                                <td>10000</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td class="size-name">38/S/48</td>
                                <td>100</td>
                                <td>5</td>
                            </tr>
                            <tr>
                                <td class="size-name">40/M/50</td>
                                <td>200</td>
                                <td>1</td>
                            </tr>
                            <tr class="bg-color-1">
                                <td class="model-name">1510 0706/386/707 BLACK PANTALON</td>
                                <td>10000</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td class="size-name">22/M/50</td>
                                <td>200</td>
                                <td>1</td>
                            </tr>
                            <tr>
                                <td class="size-name">22/M/50</td>
                                <td>200</td>
                                <td>1</td>
                            </tr>
                            <tr>
                                <td class="size-name">22/M/50</td>
                                <td>200</td>
                                <td>1</td>
                            </tr>
                            <tr>
                                <td class="size-name">22/M/50</td>
                                <td>200</td>
                                <td>1</td>
                            </tr>
                        </tbody>
                    </table>
                    @else
                    <table cellpadding="0" cellspacing="0" class="table table-bordered custom-table" id="consignmentDetails">
                        <thead>
                            <tr>
                                <td colspan="2" class="th-title">@lang('station.details')</td>
                            </tr>
                            <tr>
                                <th>@lang('station.size')</th>
                                <th>@lang('station.quantity')</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    @endif
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
