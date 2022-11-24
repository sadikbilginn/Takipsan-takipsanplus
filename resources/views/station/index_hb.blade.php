@extends('station.layout.main')

@section('content')

    @include('station.header')

    <div class="animated fadeIn test" style="position:relative; z-index:99;">
        <div class="row header">
            @include('station.read.select')
            <div class="col-lg-3">
                <div class="row consignment-info">
                    <div class="col-12">@lang('station.company') : <span id="conn" ></span></div>
                    <div class="col-12">@lang('station.delivery_date') : <span id="deliveryDate"></span></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row btn-set">
                    <div class="col-3">
                        <a href="javascript:;" onclick="getPage('consignmentVote');" class="btn btn-plus btn-circle">
                            <img src="/station/img/plus.svg" alt="plus">
                        </a>
                    </div>
                    <div class="col-3">
                        <a href="javascript:;" onclick="editConsignment(this);" class="btn btn-edit btn-circle">
                            <img src="/station/img/edit.svg" alt="edit">
                        </a>
                    </div>
                    @if(auth()->user()->company->consignment_close == true)
                    <div class="col-3">
                        <a href="javascript:;" class="btn btn-close btn-circle" onclick="closeConsignment(this);">
                            <img src="/station/img/close.svg" alt="close">
                        </a>
                    </div>
                    @endif
                    <div class="col-3">
                        <a href="javascript:;" onclick="getPage('notification');" class="btn btn-notification btn-circle">
                            <img src="/station/img/notification.svg" id="notification-img" alt="notification">
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @include('station.read.read_hb')

    </div>

    <div
        class="modal fade"
        id="pageModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="pageModalLabel"
        aria-hidden="true"
        data-backdrop="static"
        data-keyboard="false"
    >
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body"></div>
            </div>
        </div>
    </div>

    @include('station.read.readModal')

@endsection

@section('headScripts')
    <script type="text/javascript">
        let ddd = '{{ session('device.id') }}';
        let bridgeIp            = '127.0.0.1';
        let bridgePort          = '8025';
        var recordStatus        = false;
        var deviceType          = '{{ session('device.device_type') }}';
        var package_close_time  = '{{ session('device.package_timeout') }}';
        var reader              = '{{ session('device.readType.reader') == null ? session('device.reader') : session('device.readType.reader') }}';
        var readerIp            = '{{ session('device.ip_address') }}';
        var readType            = '{{ session('device.read_type_id') }}';
        var deviceSet           = {!! session('device')->toJson() !!};
        var printerAddress      = '{{ session('device.printer_address') }}'; /* /dev/ttyS2 */
        var gpioStart           = '{{ session('device.gpio_start') }}';
        var gpioStop            = '{{ session('device.gpio_stop') }}';
        var gpioError           = '{{ session('device.gpio_error') }}';
        var auto_print          = '{{ session('device.auto_print') }}';
        var auto_model_name     = '{{ session('device.auto_model_name') }}';
        var auto_size_name      = '{{ session('device.auto_size_name') }}';
        var startBtnText        = '@lang('station.start')';
        var stopBtnText         = '@lang('station.stop')';
        var langOkText          = '@lang('station.ok')';
        var langFailedText      = '@lang('station.failed')';
        var langErrorText       = '@lang('station.error_text')';
        var langTryAgainText    = '@lang('station.try_again')';
        // barcode parametresi icin eklendi.
        var barcode_ip_address = 'COM0';
        var barcode_status = 'false';
        var bridgeCloseTime = '{{ session('device.bridgeCloseTime') }}';
    </script>
@endsection

@section('js')

    <script src="/station/js/connection.js" crossorigin="use-credentials"></script>
    <script src="/station/js/bridge-hb.js" crossorigin="anonymous"></script>
    <script type="text/javascript">

        @if(request()->has('consignment'))
            localStorage.setItem('consignmentId', {{ request()->get('consignment') }});
        @endif

        var currentPType="";
        var currentLType="";;
        var boxTypes = [];
        var prodDetails;

        class Tag {
            constructor(epc, firstSeenTime) {
                this.epc = epc;
                this.firstSeenTime = firstSeenTime;
            }
        }

        getBoxTypes();

        //Test Epc
        document.addEventListener("keyup", function(event) {
            // Number 13 is the "Enter" key on the keyboard
            if (event.keyCode === 13) {
                // var tag = new Tag('303ACA17831C' + Math.floor(Math.random() * 1000000000000).toString(),new Date());
                var tag = new Tag('303ACA49801A89D99C82DDEB',new Date());
                tag.firstSeenTime = new Date();

                var tags = [];
                tags.push(tag);
                onTagRead(tags);
            }
        });

        // StationControler url process olacak şekilde veri gönderimi
        function getPage(url, param){

            //eğer reader start ise durdur
            if(readerStatus == true){
                stopReader(readerId);
            }

            $.getScript("/station/js/bootstrap-datepicker.min.js");
            $.getScript("/station/js/bootstrap-datepicker.tr.min.js");

            //db işlemleri
            axios({
                url   : stationAjaxUrl,
                method: 'post',
                data  : {
                    process         : url,
                    param           : param
                }
            }).then(function (response) {

                $('#pageModal .modal-body').html(response.data.html);
                $('#pageModal').modal('show');

            }).catch(function (error) {
                console.log(error);
            });

        }

        $(function() {

            //$('#consignments').select2();
            // $(document).on('touchend', function(){
            //     //$(".select2-search, .select2-focusser").remove();
            //     //$(".select2-search, .select2-focusser").prop('readonly',true);
            //     $(".select2-search__field").attr('readonly', true);
            // });
            // $('#consignments').on('.select2:opening, .select2:closing', function( event ) {
            //     var $searchfield = $(this).parent().find('.select2-search__field');
            //     $searchfield.prop('disabled', true);
            // });
            //modalOpen();
        });

        $("#consignments").on('change', function () {

            $('#loading').show();
            $('#consignmentDetails tbody tr').remove();

            var view = $(this).find(':selected').data('view');
            var select = $(this).val();
            var consignmentId = $(this).val();
            var selected = $(this).find('option:selected');
            var id = $(this).find(':selected').data('consigneeview');

            localStorage.setItem('consignmentId', consignmentId);
            localStorage.setItem('selectId', consignmentId);
            localStorage.setItem('gorunum', id);

            $('#consignmentDetails tbody tr').remove();

            if (id == 1){
                window.location.href="{{ URL::asset('/read') }}";
                return false;
            }else if (id == 2){
                window.location.href="{{ URL::asset('/read2') }}";
                return false;
            }else if (id == 3){
                window.location.href="{{ URL::asset('/read3') }}";
                return false;
            }else if (id == 4){
                window.location.href="{{ URL::asset('/read4') }}";
                return false;
            }else if (id == 5){
                // window.location.href="{{ URL::asset('/read5') }}";
                // return false;
            }else if (id == 6){
                window.location.href="{{ URL::asset('/read6') }}";
                return false;
            }

            getProductDetails(consignmentId,selected);

        });

        //Detay Verileri
        function getProductDetails(consignmentId,selected){
            axios({
                url   : stationAjaxUrl,
                method: 'post',
                data  : {
                    process : 'getProductDetails',
                    consignmentId   : $('#consignments').val()
                }
            }).then(function (response) {
                prodDetails = response.data.prods;

                localStorage.setItem('consignmentId', consignmentId);

                $('#totalQuantity').text(0);
                $('#itemCount').text(0);
                $('#selectedQuantity').text(0);
                $('#itemCount').text(selected.data('itemcount'));
                $('#deliveryDate').text(selected.data('deliverydate'));
                $('#conn').text(selected.data('consignee'));


                //eğer reader start ise durdur
                if(readerStatus == true){
                    stopReader(readerId);
                }

                //db işlemleri
                axios({
                    url   : stationAjaxUrl,
                    method: 'post',
                    data  : {
                        process         : 'getPackages',
                        consignmentId   : consignmentId
                    }
                }).then(function (response) {
                    insertFromDbPackage(response.data.list);

                    $(".check").prop('checked', false);

                }).catch(function (error) {
                    console.log(error);
                    $('#loading').hide();
                });

            }).catch(function (error) {
                console.log(error);
                $('#loading').hide();
            });
        }
        function startStop(e) {
            //alert('sad');

            //Sevkiyat kontrol
            if($('#consignments').val() == '' || $('#consignments').val() == null){
                sweetAlert('@lang('station.caution')', '@lang('station.choose_consignment')', 'warning', "@lang('station.ok')");
                return false;
            }
            readerMode  = 'consignment';

            var type   = $(e).attr('data-default');
            if(type == 'start') {

                startReader(readerId);

            }else{
                consignment.allClose();

                stopReader(readerId);

            }
        }
        var lastPackageNo = 0;
         // Table basma HM
        function insertRow(packageNo,package){

            if(package && package.items){
                var pNo = packageNo;

                var countCell;

                if(lastPackageNo !== packageNo){
                    $(".check").each(function(inx,ch) {

                        $(ch).prop('checked', false);

                    });
                }
                var gtin = getGTINFromEPC( Array.from(package.items.keys()).pop());
                //alert(gtin);
                var prds =  prodDetails.filter(x => x.gtin == gtin);

                var prod = prds && prds.length > 0 ? prds[0] : null;

                    var sizeList = [];
                    var curSize = "";
                    package.size = "";
                    package.items.forEach((values, keys)=>{
                        if (!values.gtin || values.gtin.length == 0) {
                            values.gtin = getGTINFromEPC(values.epc)
                        }
                        prds =  prodDetails.filter(x => x.gtin == values.gtin);

                        prd = prds && prds.length > 0 ? prds[0] : null;
                        if(prd){

                            curSize = prd.sds_code;
                        }
                        else
                        {
                            curSize = "UND";
                        }

                        if (sizeList.indexOf(curSize) == -1) {
                            sizeList.push(curSize);
                            package.size += curSize + ", ";

                        }
                    });


                    if( package.items)
                    {
                        if(prod)
                        {
                            package.items.get(Array.from(package.items.keys())[package.items.size -1]).size = prod.size;
                            if (prod.description.indexOf(',' != -1)) {
                                prod.description = prod.description.split(',')[0];
                            }
                        }else{
                            package.items.get(Array.from(package.items.keys())[package.items.size -1]).size = "UND";
                        }
                    }


                var packageRow = $( "tr[id='row_" + package.id + "']" );

                if(packageRow.length > 0){

                    var countCell = packageRow.find('#count_' + pNo);
                    countCell.html((parseInt($('#count_' + pNo).html()) + 1));

                    var sizeCell = $( "td[id='size_" + package.id + "']" );

                    if (sizeCell) {
                        sizeCell.html(package.size.replace(/,\s*$/, ""));
                    }

                }else{

                    var row = '<tr id="row_'+ package.id +'">'
                    +'<td style="width:20px!important; text-align: center;">' +
                        '<div class="custom-control custom-checkbox">' +
                            '<input type="checkbox"' +
                                'class="custom-control-input check"' +
                                'onchange="checkClick(this);"' +
                                'checked package-no="' + pNo + '"' +
                                'package-id="' + pNo + '"' +
                                'id="customCheck'+ pNo + '"' +
                                'data-id="' + package.id + '"' +
                                'value="'+ package.id +'">' +
                            '<label class="custom-control-label" for="customCheck'+ pNo + '">&nbsp;</label>' +
                        '</div>' +
                    '</td>' +
                    '<td style="width:30px!important;">@lang('station.package') '+ pNo +'</td>' +
                    // '<td onclick="typesEdit(this)" id="ptype_' + package.id + '">' + (package.box_type_id ? package.box_type_id : currentPType) + '</td>' +
                    '<td id="description_' + package.id + '">' + (prod ? prod.description : 'UND') + '</td>' +
                    // '<td onclick="typesEdit(this)" id="loadtype_' + package.id + '">' +  (package.load_type  ? package.load_type : currentLType) + '</td>' +
                    '<td style="width:20px!important; text-align: center;" id="count_' + package.id + '">' +
                        package.itemsCount +
                    '</td>' +
                    '<td style="width:30px!important;" id="size_' + package.id + '">' +
                        package.size.replace(/,\s*$/, "")  +
                    '</td>' +
                    '</tr>';

                    var table = $('#consignmentList tbody');
                    table.prepend(row);
                }

                // if(package.load_type)
                // {
                //     currentLType = package.load_type;
                // }

                // if(package.box_type_id)
                // {
                //  currentPType = package.box_type_id;
                // }

                var cnt = parseInt($('#totalQuantity').html());
                        $('#totalQuantity').html((cnt+package.itemsCount).toString());

                        if(lastPackageNo !== packageNo)
                        {
                            lastPackageNo = packageNo;
                        }
                        updateSelectedCount();

            }
            consignment.getSizes();
        }
        //Kutu Tipi
        function getBoxTypes(){

            return axios({
                url   : stationAjaxUrl,
                method: 'post',
                data  : {
                    process : 'getBoxTypes'
                }
            }).then(function (response) {
                boxTypes = response.data.boxes;
            }).catch(function (error) {
                Swal.showValidationMessage(
                    `Request failed: ${error}`
                )
            });
        }
        //16 lık 2 lik tabana
        function hex2bin(hex){
            return ("0000" + (parseInt(hex, 16)).toString(2)).substr(-4);
        }
        // Epc Anlamlandırma
        function getGTINFromEPC(epc) {

            if(!epc)
            return;

            var resultBinary = ""
            epc.split('').forEach(str => {
                resultBinary += hex2bin(str)
            })

            if (epc == "" ) {
                alert("Value must be filled out!");
                return false;
            }else {

                var companyBinary = resultBinary.substr(15, 23);
                var itemBinary = resultBinary.substr(41, 17);

                var company = (parseInt(companyBinary, 2)).toString();
                var item = (parseInt(itemBinary, 2)).toString();
                // if(item.length < 6){
                //     item = "0" + item;
                // }

                var gtin = company+item;
                var dual = parseInt(gtin.substr(1, 1)) + parseInt(gtin.substr(3, 1)) + parseInt(gtin.substr(5, 1)) + parseInt(gtin.substr(7, 1)) + parseInt(gtin.substr(9, 1)) + parseInt(gtin.substr(11, 1));
                var odd =  parseInt(gtin.substr(0, 1)) + parseInt(gtin.substr(2, 1)) + parseInt(gtin.substr(4, 1)) + parseInt(gtin.substr(6, 1)) + parseInt(gtin.substr(8, 1)) + parseInt(gtin.substr(10, 1));

                var sum = (dual * 3) + odd;
                var digit = (Math.ceil(sum / 10) * 10) - sum;

                gtin = gtin + digit;

                if(gtin[0] == '0'){
                    gtin  = gtin.substr(1,gtin.length);
                }

                return gtin;
            }
        }
        //bit dönüştürücü
        function bitsHelper(val, len, valbase) {
            var self = this;

            self.val = val;
            self.bitlength = len;

            if (!valbase)
                valbase = 16;

            self.bits = BigInt(val, valbase).toString(2);
            self.bits = Array(len - self.bits.length + 1).join('0') + self.bits;
        }
        //Sevkiyat Düzenleme
        function editConsignment(){

            //Sevkiyat kontrol
            if($('#consignments').val() == '' || $('#consignments').val() == null){
                sweetAlert('@lang('station.caution')', '@lang('station.choose_consignment')', 'warning', "@lang('station.ok')");
                return false;
            }

            getPage('editConsignment', { id : $('#consignments').val() })
        }
        //Paket Birleştirme
        function combinePackage() {

            //eğer reader start ise durdur
            if(readerStatus == true){
                stopReader(readerId);
            }

            //Sevkiyat kontrol
            if($('#consignments').val() == '' || $('#consignments').val() == null){
                sweetAlert('@lang('station.caution')', '@lang('station.choose_consignment')', 'warning', "@lang('station.ok')");
                return false;
            }
            var allVals = [];
            $(".check:checked").each(function() {
                allVals.push($(this).attr('package-id'));
            });

            if(allVals.length <= 1){
                sweetAlert('@lang('station.caution')', '@lang('station.select_package')', 'warning', "@lang('station.ok')");
                return false;
            }

            $("#startStop").prop("disabled", true);

            Swal.fire({
                title: '@lang('station.are_you_sure')',
                text: "@lang('station.combine_caution')",
                icon: 'warning',
                allowOutsideClick: false,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: '@lang('station.cancel')',
                confirmButtonText: '@lang('station.yes_combine')'
            }).then((result) => {
                if (result.value) {
                    swal.fire({
                        allowOutsideClick: false,
                        icon: 'warning',
                        title: '@lang('station.wait')',
                        onOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    axios({
                        url   : stationAjaxUrl,
                        method: 'post',
                        data  : {
                            process         : 'combinePackages',
                            consignmentId   : $('#consignments').val(),
                            packages        : allVals,
                        }
                    }).then(function (response) {

                        insertFromDbPackage(response.data.list);

                        axios({
                            url   : stationAjaxUrl,
                            method: 'post',
                            data  : {
                                process         : 'getItems',
                                consignmentId   : $('#consignments').val(),
                                ids             : response.data.ids
                            }
                        }).then(function (response) {

                            window.location.href =  window.location.href;
                            window.location.reload();

                            // insertFromDbItem(response.data.list);

                            // sweetAlert('@lang('station.successful')', '@lang('station.selected_package_combined')', 'success', "@lang('station.ok')");
                            // $('#selectedQuantity').text(0);
                            // $("#checkAll, .check").prop('checked', false);

                        }).catch(function (error) {
                            sweetAlert('@lang('station.failed')', '@lang('station.error_text')', 'error', "@lang('station.ok')");
                            console.log(error);
                        });

                    }).catch(function (error) {
                        sweetAlert('@lang('station.failed')', '@lang('station.error_text')', 'error', "@lang('station.ok')");
                        console.log(error);
                    });
                }
            })

        }
        //Paket Silme
        function deletePackage() {

            //eğer reader start ise durdur
            if(readerStatus == true){
                stopReader(readerId);
            }

            //Sevkiyat kontrol
            if($('#consignments').val() == '' || $('#consignments').val() == null){
                sweetAlert('@lang('station.caution')', '@lang('station.choose_consignment')', 'warning', "@lang('station.ok')");
                return false;
            }

            var allVals = [];
            $(".check:checked").each(function() {
                allVals.push($(this).attr('package-id') + '_' + $(this).attr('package-no') );
            });

            if(allVals.length <= 0){
                sweetAlert('@lang('station.caution')', '@lang('station.select_package')', 'warning', "@lang('station.ok')");
                return false;
            }

            $("#startStop").prop("disabled", true);

            Swal.fire({
                title: '@lang('station.are_you_sure')',
                text: "@lang('station.delete_caution')",
                icon: 'warning',
                allowOutsideClick: false,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: '@lang('station.cancel')',
                confirmButtonText: '@lang('station.yes_delete')'
            }).then((result) => {
                if (result.value) {
                    swal.fire({
                        allowOutsideClick: false,
                        icon: 'warning',
                        title: '@lang('station.wait')',
                        onOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    axios({
                        url   : stationAjaxUrl,
                        method: 'post',
                        data  : {
                            process         : 'deletePackages',
                            consignmentId   : $('#consignments').val(),
                            packages        : allVals,
                        }
                    }).then(function (response) {
                        insertFromDbPackage(response.data.list);

                        axios({
                            url   : stationAjaxUrl,
                            method: 'post',
                            data  : {
                                process         : 'getItems',
                                consignmentId   : $('#consignments').val(),
                                ids             : response.data.ids
                            }
                        }).then(function (response) {

                            window.location.href =  window.location.href;
                            window.location.reload();
                            // insertFromDbItem(response.data.list);

                            // sweetAlert('@lang('station.successful')', '@lang('station.selected_packages_deleted')', 'success', "@lang('station.ok')");
                            // $('#selectedQuantity').text(0);
                            // $("#checkAll, .check").prop('checked', false);

                        }).catch(function (error) {
                            sweetAlert('@lang('station.failed')', '@lang('station.error_text')', 'error', "@lang('station.ok')");
                            console.log(error);
                        });

                    }).catch(function (error) {
                        sweetAlert('@lang('station.failed')', '@lang('station.error_text')', 'error', "@lang('station.ok')");
                        console.log(error);
                    });
                }
            })

        }
        //Çıktı Programı
        function print() {

            //Sevkiyat kontrol
            if($('#consignments').val() == '' || $('#consignments').val() == null){
                sweetAlert('@lang('station.caution')', '@lang('station.choose_consignment')', 'warning', "@lang('station.ok')");
                return false;
            }

            var allVals = [];
            $(".check:checked").each(function() {
                allVals.push($(this).val());
            });

            // if(allVals.length <= 0){
            //     sweetAlert('@lang('station.caution')', '@lang('station.select_package')', 'warning', "@lang('station.ok')");
            //     return false;
            // }

            // //eğer reader start ise durdur
            // if(readerStatus == true){
            //     stopReader(readerId);
            // }

            Swal.fire({
                title: '@lang('station.are_you_sure')',
                text: "@lang('station.print_caution')",
                icon: 'warning',
                allowOutsideClick: false,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: '@lang('station.cancel')',
                confirmButtonText: '@lang('station.yes_print')'
            }).then((result) => {
                if (result.value) {
                    ld              = new PrintData();
                    ld.printer      = printerAddress;
                    ld.headMargin   = false;
                    ld.footMargin   = false;

                    ld.lines.push(new LineData("", true));
                    ld.lines.push(new LineData("", true));

                    br.printLinesPlus2(ld);

                    ld                  = new LabelData();
                    ld.printer          = printerAddress;
                    ld.headMargin       = false;
                    ld.footMargin       = true;
                    ld.client           = $('#consigneeName').text();
                    ld.boxNo            = allVals.length > 1 ? allVals.length : allVals[0];
                    ld.numberOfBoxes    = allVals.length;

                    $('#consignmentDetails tbody tr').each(function(){
                        ld.sizes.push(new SizeData($(this).find('td:nth-child(1)').text(), $(this).find('td:nth-child(2)').text()));
                    });

                    br.printLabelPlus2(ld);
                    ld              = new PrintData();
                    ld.printer      = printerAddress;
                    ld.headMargin   = false;
                    ld.footMargin   = false;

                    ld.lines.push(new LineData("", true));
                    ld.lines.push(new LineData(printLineCheck("Date:   {{ date('Y/m/d H:i:s') }}"), true));
                    ld.lines.push(new LineData("", true));

                    br.printLinesPlus2(ld);
                    ld              = new PrintData();
                    ld.printer      = printerAddress;
                    ld.headMargin   = true;
                    ld.footMargin   = false;
                    ld.lines.push(new LineData("", true));
                    ld.lines.push(new LineData("", true));
                    ld.lines.push(new LineData(printLineCheck('PO:' + $('#consignments option:selected').text()), true));

                    br.printLinesPlus2(ld);

                }
            })
        }
        // Otomatik Çıktı
        function autoPrint() {

            var allVals = [];
            $(".check:checked").each(function() {
                allVals.push($(this).val());
            });

            if(allVals.length > 0){
                var printer = printerAddress;
                ld              = new PrintData();
                ld.printer      = printer;
                ld.headMargin   = false;
                ld.footMargin   = true;

                ld.lines.push(new LineData("", true));
                ld.lines.push(new LineData("", true));

                br.printLinesPlus2(ld);

                ld                  = new LabelData();
                ld.printer          = printer;
                ld.headMargin       = false;
                ld.footMargin       = false;
                ld.client           = $('#consigneeName').text();
                ld.boxNo            = allVals.length > 1 ? allVals.length : allVals[0];
                ld.numberOfBoxes    = allVals.length;

                $('#consignmentDetails tbody tr').each(function(){
                    ld.sizes.push(new SizeData($(this).find('td:nth-child(1)').text(), $(this).find('td:nth-child(2)').text()));
                });

                br.printLabelPlus2(ld);
                ld              = new PrintData();
                ld.printer      = printer;
                ld.headMargin   = false;
                ld.footMargin   = false;

                ld.lines.push(new LineData("", true));
                ld.lines.push(new LineData(printLineCheck("Date:   {{ date('Y/m/d H:i:s') }}"), true));
                ld.lines.push(new LineData("", true));

                br.printLinesPlus2(ld);
                ld              = new PrintData();
                ld.printer      = printer;
                ld.headMargin   = true;
                ld.footMargin   = false;
                ld.lines.push(new LineData("", true));
                ld.lines.push(new LineData("", true));
                ld.lines.push(new LineData(printLineCheck('PO:' + $('#consignments option:selected').text()), true));

                br.printLinesPlus2(ld);
            }

        }
        //Satır Okuma
        function printLineCheck(text) {

            return text.substring(0, 24);
        }
        //Paket Bul
        function findPackage() {

            //eğer reader start ise durdur
            if(readerStatus == true){
                stopReader(readerId);
            }

            //Sevkiyat kontrol
            if($('#consignments').val() == '' || $('#consignments').val() == null){
                sweetAlert('@lang('station.caution')', '@lang('station.choose_consignment')', 'warning', "@lang('station.ok')");
                return false;
            }

            Swal.fire({
                title: '@lang('station.are_you_sure')',
                text: '@lang('station.find_caution')',
                icon: 'warning',
                allowOutsideClick: false,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: '@lang('station.cancel')',
                confirmButtonText: '@lang('station.yes_find')',
            }).then((result) => {
                if (result.value) {
                    swal.fire({
                        allowOutsideClick: false,
                        icon: 'warning',
                        title: '@lang('station.wait')',
                        onOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    //eğer reader start ise durdur
                    if(readerStatus == true && readerMode == 'consignment'){
                        stopReader(readerId);
                    }

                    readerMode  = 'find';
                    buffer.epcs = new Set();
                    startReader(readerId);

                    setTimeout(function () {

                        stopReader(readerId);

                        if(buffer.epcs.size  > 0){

                            axios({
                                url   : stationAjaxUrl,
                                method: 'post',
                                data  : {
                                    process         : 'findPackages',
                                    consignmentId   : $('#consignments').val(),
                                    epc             : Array.from(buffer.epcs)
                                }
                            }).then(function (response) {
                                if(response.data == 'nonpackage'){

                                    sweetAlert('@lang('station.failed')', "@lang('station.no_package')", 'error', "@lang('station.ok')");

                                }else{

                                    $("input[type=checkbox]").prop('checked', false);
                                    $("tr#"+response.data.package.package_no+" input[type=checkbox]").prop('checked', true);

                                    var html = "<b>@lang('station.quantity') :</b> "+ response.data.package.items_count;

                                    if(response.data.package.model !== null){
                                        html += "<br> <b>@lang('station.model') :</b> : " + response.data.package.model;
                                    }
                                    if(response.data.package.size !== null){
                                        html += "<br> <b>@lang('station.size') :</b> : " + response.data.package.size;
                                    }

                                    Swal.fire({
                                        icon: "success",
                                        title: "@lang('station.package') " + response.data.package.package_no,
                                        html: html,
                                        confirmButtonText: '@lang('station.ok')'
                                    });
                                }


                            }).catch(function (error) {
                                sweetAlert('@lang('station.failed')', '@lang('station.error_text')', 'error', "@lang('station.ok')");
                                console.log(error);
                            });
                        }else{
                            sweetAlert('@lang('station.failed')', "@lang('station.reading_failed')", 'error', "@lang('station.ok')");
                        }
                    }, 2000);


                }
            })

        }
        //Sevkiyat Kapatma
        @if(auth()->user()->company->consignment_close == true)

        function closeConsignment(CloseShipmentValue) {

            //Sevkiyat kontrol
            if($('#consignments').val() == '' || $('#consignments').val() == null){
                sweetAlert('@lang('station.caution')', '@lang('station.choose_consignment')', 'warning', "@lang('station.ok')");
                return false;
            }

            //eğer reader start ise durdur
            if(readerStatus == true){
                stopReader(readerId);
            }

            Swal.fire({
                title: '@lang('station.are_you_sure')',
                text: "@lang('station.consignment_caution')",
                icon: 'warning',
                allowOutsideClick: false,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: '@lang('station.cancel')',
                confirmButtonText: '@lang('station.yes_close')'
            }).then((result) => {
                if (result.value) {
                    swal.fire({
                        allowOutsideClick: false,
                        icon: 'warning',
                        title: '@lang('station.wait')',
                        onOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    axios({
                        url   : stationAjaxUrl,
                        method: 'post',
                        data  : {
                            process         : 'closeConsignment',
                            consignmentId   : $('#consignments').val(),
                            CloseShipmentSt : 1
                        }
                    }).then(function (response) {
                        localStorage.removeItem('consignmentId');
                        sweetAlert('@lang('station.successful')', '@lang('station.consignment_closed')', 'success', "@lang('station.ok')");
                        window.location.reload();
                    }).catch(function (error) {
                        sweetAlert('@lang('station.failed')', '@lang('station.error_text')', 'error', "@lang('station.ok')");
                        console.log(error);
                    });
                }
            })

        }
        @endif
        //Tip düzenleme
        function typesEdit(e,package) {
            //eğer reader start ise durdur
            if(readerStatus == true){
                stopReader(readerId);
            }
            // var val = $('#consignmentList tbody tr#' + packageNo + ' td')[3].innerText;
            var options = "";
            if(boxTypes && boxTypes.length > 0)
            {
                for (let i = 0; i < boxTypes.length; i++) {

                    options += "<option required value='" + boxTypes[i].name + "'>" + boxTypes[i].name + '   ' + boxTypes[i].length + 'x' + boxTypes[i].width + 'x' + boxTypes[i].height + "</option>";

                }
            }

            Swal.fire({
                position: 'top',
                title: '@lang('station.model_name')',
                html:'<div class="form-row">' +
                            '<div class="form-group col-md-6">' +
                                '<label for="load_type">Load Type</label>' +
                                '<select id="load_type" class="form-control">' +
                                    '<option value="Please">Please select...</option>' +
                                    '<option value="Assortment">Assortment</option>' +
                                    '<option value="AssortmentLast">Assortment Last</option>' +
                                    '<option value="Solid">Solid</option>' +
                                    '<option value="SolidLast">Solid Last</option>' +
                                    '<option value="SolidMix">Solid Mix</option>' +
                                '</select>' +
                            '</div>' +
                            '<div class="form-group col-md-6 package_type">' +
                                '<label for="package_type">Package Type</label>' +
                                '<select id="package_type" class="form-control">' +
                                    '<option  value="Please">Please select...</option>' +
                                    options +
                                '</select>' +
                            '</div>' +
                        '</div>',
                showCancelButton: true,
                onOpen: function(){
                    //$('#package_type').select2();
                },
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: '@lang('station.cancel')',
                confirmButtonText: '{{ title_case(trans('station.save')) }}',
                showLoaderOnConfirm: true,
                preConfirm: (model) => {
                    pid = e.id.split('_')[1];
                    var pVal = $("#package_type").val();
                    var tVal = $("#load_type").val();

                    if (pVal && pVal.length > 0 && pVal.indexOf('Please') == -1 && tVal && tVal.length > 0 && tVal.indexOf('Please') == -1) {

                        axios({
                        url   : stationAjaxUrl,
                        method: 'post',
                        data  : {
                            process         : 'typesEdit',
                            box_type_id     : pVal,
                            load_type       : tVal,
                            packageId       : pid
                        }
                        }).then(function (response) {
                            if (package) {
                                package.box_type_id = pVal;
                                package.load_type = tVal;
                            }
                            currentPType = pVal;
                            currentLType = tVal;
                            $('#loadtype_' + pid).html(tVal);
                            $('#ptype_' + pid).html(pVal);
                        }).catch(function (error) {
                            sweetAlert('@lang('station.failed')', '@lang('station.error_text')', 'error', "@lang('station.ok')");
                            console.log(error);
                        });

                    }
                    else
                    {
                        sweetAlert('@lang('station.caution')', 'Please fill the form completely.', 'warning', "@lang('station.ok')");
                    }

                    // if(pVal && pVal !== "0"){
                    //  currentPType = $("#package_type").val();
                    //  $('#ptype_' + pid).html($( "#package_type option:selected" ).text());
                    // }

                    //  if(tVal && tVal !== "0"){
                    //     currentLType = $("#load_type").val();
                    //     $('#loadtype_' + pid).html(currentLType);
                    // }



                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                console.log('Yeap!')
            });
        }
        //Boyut düzenleme kullanılmıyor
        function sizeEdit(packageNo) {

            //eğer reader start ise durdur
            if(readerStatus == true){
                stopReader(readerId);
            }

            var val = $('#consignmentList tbody tr#' + packageNo + ' td')[4].innerText;

            Swal.fire({
                position: 'top',
                title: '@lang('station.size')',
                input: 'text',
                inputValue : val,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: '@lang('station.cancel')',
                confirmButtonText: '{{ title_case(trans('station.save')) }}',
                showLoaderOnConfirm: true,
                preConfirm: (size) => {
                    return axios({
                        url   : stationAjaxUrl,
                        method: 'post',
                        data  : {
                            process         : 'sizeEdit',
                            consignmentId   : $('#consignments').val(),
                            packageNo       : packageNo,
                            size            : size
                        }
                    }).then(function (response) {
                        if (response.data.status != 'ok') {
                            throw new Error(response.statusText)
                        }

                        consignment.packages.get(packageNo).size = size;
                        consignment.getSizes();

                        return response.data.message;
                    }).catch(function (error) {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                        )
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.value) {
                    $('#consignmentList tbody tr#' + packageNo + ' td')[4].innerText = result.value;
                }
            });
        }
        //Tüm modelleri düzenleme kullanılmıyor
        function modelAllEdit() {

            //Sevkiyat kontrol
            if($('#consignments').val() == '' || $('#consignments').val() == null){
                sweetAlert('@lang('station.caution')', '@lang('station.choose_consignment')', 'warning', "@lang('station.ok')");
                return false;
            }

            var allVals = [];
            $(".check:checked").each(function() {
                allVals.push($(this).val());
            });

            if(allVals.length <= 0){
                sweetAlert('@lang('station.caution')', '@lang('station.select_package')', 'warning', "@lang('station.ok')");
                return false;
            }

            //eğer reader start ise durdur
            if(readerStatus == true){
                stopReader(readerId);
            }

            Swal.fire({
                position: 'top',
                title: '@lang('station.model_name')',
                input: 'text',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: '@lang('station.cancel')',
                confirmButtonText: '{{ title_case(trans('station.save')) }}',
                showLoaderOnConfirm: true,
                preConfirm: (model) => {
                    return axios({
                        url   : stationAjaxUrl,
                        method: 'post',
                        data  : {
                            process         : 'modelAllEdit',
                            consignmentId   : $('#consignments').val(),
                            packages        : allVals,
                            model           : model
                        }
                    }).then(function (response) {
                        if (response.data.status != 'ok') {
                            throw new Error(response.statusText)
                        }
                        return response.data.message;
                    }).catch(function (error) {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                        )
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.value) {
                    allVals.forEach(function(item){
                        $('#consignmentList tbody tr#' + item + ' td')[3].innerText = result.value;
                    });
                }
            });
        }
        //Tüm boyutları düzenleme kullanılmıyor
        function sizeAllEdit() {

            //Sevkiyat kontrol
            if($('#consignments').val() == '' || $('#consignments').val() == null){
                sweetAlert('@lang('station.caution')', '@lang('station.choose_consignment')', 'warning', "@lang('station.ok')");
                return false;
            }

            var allVals = [];
            $(".check:checked").each(function() {
                allVals.push($(this).val());
            });

            if(allVals.length <= 0){
                sweetAlert('@lang('station.caution')', '@lang('station.select_package')', 'warning', "@lang('station.ok')");
                return false;
            }

            //eğer reader start ise durdur
            if(readerStatus == true){
                stopReader(readerId);
            }

            Swal.fire({
                position: 'top',
                title: '@lang('station.size')',
                input: 'text',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: '@lang('station.cancel')',
                confirmButtonText: '{{ title_case(trans('station.save')) }}',
                showLoaderOnConfirm: true,
                preConfirm: (size) => {
                    return axios({
                        url   : stationAjaxUrl,
                        method: 'post',
                        data  : {
                            process         : 'sizeAllEdit',
                            consignmentId   : $('#consignments').val(),
                            packages        : allVals,
                            size            : size
                        }
                    }).then(function (response) {
                        if (response.data.status != 'ok') {
                            throw new Error(response.statusText)
                        }
                        return response.data.message;
                    }).catch(function (error) {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                        )
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.value) {
                    allVals.forEach(function(item){
                        $('#consignmentList tbody tr#' + item + ' td')[4].innerText = result.value;
                    });
                }
            });
        }

        //Okuma ekranı aç
        function modalOpen() {
            $('#readModal .read .read-header').html('@lang('station.package_read')');
            $('#readModal .read .read-header').removeClass('read-success');
            $('#packageTotal').text('0');
            $('#readModal .read .read-media').html("<img src=\"/station/img/box-animation.gif\">\n" +
                "                                    <br>\n" +
                "                                    <img src=\"/station/img/load.gif\">");
            $('#readModal').modal('show');
        }
        //Okuma ekranı kapat
        function modalClose() {

            $('#readModal .read .read-header').html('@lang('station.package_closed')');
            $('#readModal .read .read-header').addClass('read-success');
            $('#readModal .read .read-media').html("<img src=\"/station/img/check-animation.gif\">");

            setTimeout(function () {
                $('#readModal').modal('hide');
            }, 500)
        }
        //Okuma ekranı Hatası
        function modalCloseFail() {

            $('#readModal .read .read-header').html('@lang('station.error_text')');
            $('#readModal .read .read-header').addClass('read-fail');
            $('#readModal .read .read-media').html("<img src=\"/station/img/box-animation.gif\">");

            setTimeout(function () {
                $('#readModal').modal('hide');
            }, 500);
        }
        //Storage Sıfırla
        function resetStorage(){
            window.localStorage.clear();
            window.location.reload();
        }

    </script>

@endsection
