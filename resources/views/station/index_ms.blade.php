@extends('station.layout.main')

@section('content')

    @include('station.header')
    <!-- commit !-->
    <style>
        .swal2-popup {
            width: 35em !important;
        }
    </style>

    <div class="animated fadeIn" style="position:relative; z-index:99;">
        <div class="row header">
            @include('station.read.select')
            <div class="col-lg-3">
                <div class="row consignment-info">
                    <div class="col-12">@lang('station.company'): <span id="conn"></span></div>
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
                        <a href="javascript:;" onclick="getPage('notification');"
                           class="btn btn-notification btn-circle">
                            <img src="/station/img/notification.svg" id="notification-img" alt="notification">
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @include('station.read.read_ms')

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
        let bridgeIp = '127.0.0.1';
        let bridgePort = '8025';
        var recordStatus = false;
        var deviceType = '{{ session('device.device_type') }}';
        var package_close_time = '{{ session('device.package_timeout') }}';
        var reader = '{{ session('device.readType.reader') == null ? session('device.reader') : session('device.readType.reader') }}';
        var readerIp = '{{ session('device.ip_address') }}';
        var readType = '{{ session('device.read_type_id') }}';
        var deviceSet = {!! session('device')->toJson() !!};
        var printerAddress = '{{ session('device.printer_address') }}'; /* /dev/ttyS2 */
        var gpioStart = '{{ session('device.gpio_start') }}';
        var gpioStop = '{{ session('device.gpio_stop') }}';
        var gpioError = '{{ session('device.gpio_error') }}';
        var auto_print = '{{ session('device.auto_print') }}';
        var auto_model_name = '{{ session('device.auto_model_name') }}';
        var auto_size_name = '{{ session('device.auto_size_name') }}';
        var startBtnText = '@lang('station.start')';
        var stopBtnText = '@lang('station.stop')';
        var langOkText = '@lang('station.ok')';
        var langFailedText = '@lang('station.failed')';
        var langErrorText = '@lang('station.error_text')';
        var langTryAgainText = '@lang('station.try_again')';
        var barcode_ip_address = '{{ session('device.barcode_ip_address') }}';
        var barcode_status = '{{ session('device.barcode_status') }}';
        var bridgeCloseTime = '{{ session('device.bridgeCloseTime') }}';

    </script>
@endsection
@section('css')
    <link href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
          integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
@endsection
@section('js')

    <script src="/station/js/connection.js" crossorigin="use-credentials"></script>
    <script src="/station/js/bridge-ms.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>

    <script type="text/javascript">
        var MSdatatable;
        var MSdatatableHanging;
        @if(request()->has('consignment'))
        localStorage.setItem('consignmentId', {{ request()->get('consignment') }});
        @endif

        var hangingStorage = localStorage.getItem('hangingProduct');

        var currentPType = "";
        var currentLType = "";

        var boxTypes = [];
        var prodDetails;

        class Tag {
            constructor(epc, firstSeenTime) {
                this.epc = epc;
                this.firstSeenTime = firstSeenTime;
            }
        }

        //getBoxTypes();

        //Test Epc
        // document.addEventListener("keyup", function (event) {
        //     // Number 13 is the "Enter" key on the keyboard
        //     if (event.keyCode === 13) {
        //         // var tag = new Tag('303ACA17831C' + Math.floor(Math.random() * 1000000000000).toString(),new Date());
        //         var tag = new Tag('303ACA49801A89D99C82DDEB', new Date());
        //         tag.firstSeenTime = new Date();
        //
        //         var tags = [];
        //         tags.push(tag);
        //         onTagRead(tags);
        //     }
        // });

        // StationControler url process olacak şekilde veri gönderimi
        function getPage(url, param) {

            //eğer reader start ise durdur
            if (readerStatus == true) {
                stopReader(readerId);
            }

            $.getScript("/station/js/bootstrap-datepicker.min.js");
            $.getScript("/station/js/bootstrap-datepicker.tr.min.js");

            //db işlemleri
            axios({
                url: stationAjaxUrl,
                method: 'post',
                data: {
                    process: url,
                    param: param
                }
            }).then(function (response) {

                $('#pageModal .modal-body').html(response.data.html);
                $('#pageModal').modal('show');

            }).catch(function (error) {
                console.log(error);
            });

        }

        $("#consignments").on('change', function () {

            $('#loading').show();
            $('#consignmentDetails tbody tr').remove();

            var view = $(this).find(':selected').data('view');
            var select = $(this).val();
            var consignmentId = $(this).val();
            var selected = $(this).find('option:selected');
            var id = $(this).find(':selected').data('consigneeview');
            var hangingProduct = $(this).find(':selected').data('hanging');

            localStorage.setItem('consignmentId', consignmentId);
            localStorage.setItem('selectId', consignmentId);
            localStorage.setItem('gorunum', id);
            localStorage.setItem('hangingProduct', hangingProduct);
            hangingStorage = localStorage.getItem('hangingProduct');
            // if (localStorage.getItem('activePackageId')){
            //     alert(localStorage.getItem('activePackageId'));
            // }

            $('#consignmentDetails tbody tr').remove();

            if (id == 1) {
                window.location.href = "{{ URL::asset('/read') }}";
                return false;
            } else if (id == 2) {
                window.location.href = "{{ URL::asset('/read2') }}";
                return false;
            } else if (id == 3) {
                // window.location.href="{{ URL::asset('/read3') }}";
                // return false;
            } else if (id == 4) {
                window.location.href = "{{ URL::asset('/read4') }}";
                return false;
            } else if (id == 5) {
                window.location.href = "{{ URL::asset('/read5') }}";
                return false;
            }else if (id == 6){
                window.location.href="{{ URL::asset('/read6') }}";
                return false;
            }

            //getProductMsDetails(consignmentId, selected);
            totalSizeQuantityMs();

            if (typeof (MSdatatable) != "undefined"){

                MSdatatable.ajax.reload();

            }

            if (typeof (MSdatatableHanging) != "undefined"){

                MSdatatableHanging.ajax.reload();

            }

            if (hangingStorage == 1){
                // askili urunde gosterilmesi gereksiz alanlar tablodan kaldırılıyor
                MSdatatable.column(3).visible(false);
                MSdatatable.column(5).visible(false);
                $('.btn-bil').attr("style", "display: none !important");

            }else{
                // askili urunde gosterilmesi gereksiz alanlar tablodan gosteriliyor
                MSdatatable.column(3).visible(true);
                MSdatatable.column(5).visible(true);
                $('.btn-bil').attr("style", "display: block !important");

            }

        });

        //Detay Verileri
        function getProductMsDetails(consignmentId, selected) {


            axios({
                url: stationAjaxUrl,
                method: 'post',
                data: {
                    process: 'getProductMsDetails',
                    consignmentId: $('#consignments').val()
                }
            }).then(function (response) {

                localStorage.setItem('consignmentId', consignmentId);
                prodDetails = response.data.prods;

                //$('#totalQuantity').text(0);
                //$('#itemCount').text(0);
                //$('#selectedQuantity').text(0);
                //$('#itemCount').text(selected.data('itemcount'));
                $('#deliveryDate').text(selected.data('deliverydate'));
                $('#conn').text(selected.data('consignee'));


                //eğer reader start ise durdur
                if (readerStatus == true) {
                    stopReader(readerId);
                }

                //db işlemleri
                axios({
                    url: stationAjaxUrl,
                    method: 'post',
                    data: {
                        process: 'getPackages',
                        consignmentId: consignmentId
                    }
                }).then(function (response) {

                    //alert(JSON.stringify(response.data.list));

                    insertFromDbPackage(response.data.list);

                    $(".check").prop('checked', false);

                    // if (localStorage.getItem('activePackageId')){

                    //     startReader(readerId);

                    // }

                }).catch(function (error) {
                    console.log(error);
                    $('#loading').hide();
                });

            }).catch(function (error) {
                console.log(error);
                $('#loading').hide();
            });

        }

        var lastPackageNo = 0;

        // Table basma MS
        function _insertRow(packageNo, package) {


            if (package && package.items) {

                var pNo = packageNo;
                var countCell;

                if (lastPackageNo !== packageNo) {
                    $(".check").each(function (inx, ch) {

                        $(ch).prop('checked', false);

                    });
                }
                //alert(JSON.stringify(package));
                if (package.itemsCount > 0) {
                    var gtin = getGTINFromEPC(Array.from(package.items.keys()).pop());
                    //console.log('gtin_'+gtin);
                    //var prds =  prodDetails.filter(x => x.gtin == gtin);

                    var gtinEpcUzunluk = gtin.substring(gtin.length - 8);
                    if (gtinEpcUzunluk.substr(0, 1) == 0) {
                        gtinEpcUzunluk = gtin.substring(gtin.length - 7);
                    }
                    //alert(gtinEpcUzunluk);
                    var prds = prodDetails.filter(x => x.upc == gtinEpcUzunluk);
                    var prod = prds && prds.length > 0 ? prds[0] : null;
                    var sizeList = [];
                    var curSize = "";
                    //package.size = "";
                    package.items.forEach((values, keys) => {
                        if (!values.gtin || values.gtin.length == 0) {
                            values.gtin = getGTINFromEPC(values.epc)
                        }
                        //prds =  prodDetails.filter(x => x.gtin == values.gtin);
                        var gtinEpcUzunluk2 = values.gtin.substring(values.gtin.length - 8);
                        if (gtinEpcUzunluk2.substr(0, 1) == 0) {
                            gtinEpcUzunluk2 = values.gtin.substring(values.gtin.length - 7);
                        }
                        //alert (gtinEpcUzunluk2);
                        var prds = prodDetails.filter(x => x.upc == gtinEpcUzunluk2);
                        prd = prds && prds.length > 0 ? prds[0] : null;

                        if (prd) {

                            curSize = prd.sds_code;
                        } else {
                            curSize = "UND";
                        }

                        if (sizeList.indexOf(curSize) == -1) {
                            sizeList.push(curSize);
                            //package.size += curSize + ', ';

                        }

                    });


                }

                if (package.itemsCount > 0) {


                    //alert(prod);
                    /*if(prod){
                        package.items.get(Array.from(package.items.keys())[package.items.size -1]).size = prod.size;
                        if (prod.description.indexOf(',' != -1)) {
                            prod.description = prod.description.split(',')[0];
                        }
                    }else{*/
                    package.items.get(Array.from(package.items.keys())[package.items.size - 1]).size = "UND";
                    /*}*/

                }

                var packageRow = $("tr[id='row_" + package.id + "']");

                if (packageRow.length > 0) {

                    var countCell = packageRow.find('#count_' + pNo);
                    countCell.html((parseInt($('#count_' + pNo).html()) + 1));

                    /*var sizeCell = $( "td[id='size_" + package.id + "']" );

                    if (sizeCell) {
                        sizeCell.html(package.size+'ftm_sdk2');
                    }*/


                } else {

                    var row = '<tr id="row_' + package.id + '">' +
                        '<td style="width:50px!important; text-align: center;">' +
                        '<div class="custom-control custom-checkbox">' +
                        '<input type="checkbox"' +
                        'class="custom-control-input check"' +
                        'onchange="checkClick(this);"' +
                        'checked package-no="' + pNo + '"' +
                        'package-id="' + package.id + '"' +
                        'id="customCheck' + pNo + '"' +
                        'data-id="' + package.id + '"' +
                        'value="' + package.id + '">' +
                        '<label class="custom-control-label" for="customCheck' + pNo + '">&nbsp;</label>' +
                        '</div>' +
                        '</td>' +
                        '<td style="width:65px!important;">No : ' + pNo + '</td>' +
                        '<td style="width:180px!important;">' + package.barcode + '</td>' +
                        '<td onclick="descriptionModal(this)" + id="description_' + package.id + '" data-carton="' + pNo + '" data-colourDesc="' + package.colourDesc + '" data-strokeDesc="' + package.strokeDesc + '" data-departmentDesc="' + package.departmentDesc + '" style="width:100px!important;">' + package.upc + '</td>' +
                        '<td style="width:60px!important;">' + package.singles + '</td>' +
                        '<td style="width:60px!important; text-align: center;" id="count_' + package.id + '">' +
                        package.itemsCount +
                        '</td>' +
                        '<td style="width:120px!important;" id="size_' + package.id + '">' +
                        package.size +
                        '</td>' +
                        '</tr>';


                    var table = $('#consignmentList tbody');
                    table.prepend(row);

                    if ($.inArray(package.size, sizeList) === -1) {
                        //alert('icinde gecmiyor.');
                        $('#row_' + package.id).addClass('fail-bg');
                    }

                    if (package.itemsCount != package.singles) {

                        $('#row_' + package.id).addClass('fail-bg');

                    }

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
                $('#totalQuantity').html((cnt + package.itemsCount).toString());

                if (lastPackageNo !== packageNo) {
                    lastPackageNo = packageNo;
                }

                updateSelectedCount();

            }

            consignment.getSizes();

        }

        //Kutu Tipi
        function _getBoxTypes() {

            return axios({
                url: stationAjaxUrl,
                method: 'post',
                data: {
                    process: 'getBoxTypes'
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
        function hex2bin(hex) {

            return ("0000" + (parseInt(hex, 16)).toString(2)).substr(-4);
        }

        // Epc Anlamlandırma
        function getGTINFromEPC(epc) {

            if (!epc)
                return;

            var resultBinary = ""
            epc.split('').forEach(str => {
                resultBinary += hex2bin(str)
            })

            if (epc == "") {
                return false;
            } else {

                var companyBinary = resultBinary.substr(14, 20);
                var itemBinary = resultBinary.substr(34, 24);

                var company = (parseInt(companyBinary, 2)).toString();
                var item = (parseInt(itemBinary, 2)).toString();
                if (item.length < 6) {
                    item = "0" + item;
                }

                var gtin = company + item;

                var dual = parseInt(gtin.substr(1, 1)) + parseInt(gtin.substr(3, 1)) + parseInt(gtin.substr(5, 1)) + parseInt(gtin.substr(7, 1)) + parseInt(gtin.substr(9, 1)) + parseInt(gtin.substr(11, 1));
                var odd = parseInt(gtin.substr(0, 1)) + parseInt(gtin.substr(2, 1)) + parseInt(gtin.substr(4, 1)) + parseInt(gtin.substr(6, 1)) + parseInt(gtin.substr(8, 1)) + parseInt(gtin.substr(10, 1));

                var sum = (dual * 3) + odd;
                var digit = (Math.ceil(sum / 10) * 10) - sum;

                gtin = gtin + digit;

                if (gtin[0] == '0') {
                    gtin = gtin.substr(1, gtin.length);
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
        function editConsignment() {

            //Sevkiyat kontrol
            if ($('#consignments').val() == '' || $('#consignments').val() == null) {
                sweetAlert('@lang('station.caution')', '@lang('station.choose_consignment')', 'warning', "@lang('station.ok')");
                return false;
            }

            getPage('editConsignment', {id: $('#consignments').val()})
        }

        //Paket Silme
        function _deletePackage() {

            //eğer reader start ise durdur
            if (readerStatus == true) {
                stopReader(readerId);
            }

            //Sevkiyat kontrol
            if ($('#consignments').val() == '' || $('#consignments').val() == null) {
                sweetAlert('@lang('station.caution')', '@lang('station.choose_consignment')', 'warning', "@lang('station.ok')");
                return false;
            }

            var allVals = [];
            $(".check:checked").each(function () {
                allVals.push($(this).attr('package-id') + '_' + $(this).attr('package-no'));
            });

            if (allVals.length <= 0) {
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
                        url: stationAjaxUrl,
                        method: 'post',
                        data: {
                            process: 'deletePackages',
                            consignmentId: $('#consignments').val(),
                            packages: allVals,
                        }
                    }).then(function (response) {
                        insertFromDbPackage(response.data.list);

                        axios({
                            url: stationAjaxUrl,
                            method: 'post',
                            data: {
                                process: 'getItems',
                                consignmentId: $('#consignments').val(),
                                ids: response.data.ids
                            }
                        }).then(function (response) {

                            //localStorage.removeItem('activePackageId');

                            var consignmentId = $('#consignments').val();
                            var selected = $('#consignments').find('option:selected');
                            getProductMsDetails(consignmentId, selected);
                            window.location.href = window.location.href;
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

        //Paket Bul
        function findPackage() {

            //eğer reader start ise durdur
            if (readerStatus == true) {
                stopReader(readerId);
            }

            //Sevkiyat kontrol
            if ($('#consignments').val() == '' || $('#consignments').val() == null) {
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
                    if (readerStatus == true && readerMode == 'consignment') {
                        stopReader(readerId);
                    }

                    readerMode = 'find';
                    buffer.epcs = new Set();
                    startReader(readerId);

                    setTimeout(function () {

                        stopReader(readerId);

                        if (buffer.epcs.size > 0) {

                            axios({
                                url: stationAjaxUrl,
                                method: 'post',
                                data: {
                                    process: 'findPackages',
                                    consignmentId: $('#consignments').val(),
                                    epc: Array.from(buffer.epcs)
                                }
                            }).then(function (response) {
                                if (response.data == 'nonpackage') {

                                    sweetAlert('@lang('station.failed')', "@lang('station.no_package')", 'error', "@lang('station.ok')");

                                } else {

                                    $("input[type=checkbox]").prop('checked', false);
                                    $("tr#" + response.data.package.package_no + " input[type=checkbox]").prop('checked', true);

                                    var html = "<b>@lang('station.quantity') :</b> " + response.data.package.items_count;

                                    if (response.data.package.model !== null) {
                                        html += "<br> <b>@lang('station.model') :</b> : " + response.data.package.model;
                                    }
                                    if (response.data.package.size !== null) {
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
                        } else {
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
            if ($('#consignments').val() == '' || $('#consignments').val() == null) {
                sweetAlert('@lang('station.caution')', '@lang('station.choose_consignment')', 'warning', "@lang('station.ok')");
                return false;
            }

            //eğer reader start ise durdur
            if (readerStatus == true) {
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
                        url: stationAjaxUrl,
                        method: 'post',
                        data: {
                            process: 'closeConsignment',
                            consignmentId: $('#consignments').val(),
                            CloseShipmentSt: 1
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

        //Okuma ekranı aç
        function modalOpen() {
            $('#readModal .read .read-header').html('@lang('station.package_read')');
            $('#readModal .read .read-header').removeClass('read-success');
            $('#packageTotal').text('0');
            $('#readModal .read .read-media').html("<img src=\"/station/img/box-animation.gif\">\n" +
                "                                    <br>\n" +
                "                                    <img src=\"/station/img/load.gif\">");
            $('#readModal').modal('show');

            if (hangingStorage == 1){
                $('.hangingButtonContent').show();
            }

        }

        //Okuma ekranı kapat
        function modalClose() {

            $('#readModal .read .read-header').html('@lang('station.package_closed')');
            $('#readModal .read .read-header').addClass('read-success');
            $('#readModal .read .read-media').html("<img src=\"/station/img/check-animation.gif\">");

            setTimeout(function () {
                $('#msDataTable').DataTable
                $('#msDataTableHanging').DataTable
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
                $('#readModal .read .read-header').removeClass('read-fail');
            }, 1000);
        }

        //Storage Sıfırla
        function resetStorage() {
            //window.localStorage.clear();
            window.location.reload();
        }

        function descriptionModal(e) {
            // alert($(e).data('name'));
            // alert($(e).data('package'));
            // colourDesc strokeDesc departmentDesc


            Swal.fire({
                icon: "info",
                title: '@lang('station.carton') : ' + $(e).data('carton') + '',
                confirmButtonColor: '#3085d6',
                confirmButtonText: '{{ title_case(trans('station.close')) }}',
                html: '<div class="form-row">' +
                    '<div class="form-group col-md-12 mb-0">' +
                    '<label style="text-align:left;">' + $(e).data('description') + '</label>' +
                    '</div>' +
                    '</div>',
                showLoaderOnConfirm: true,
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                console.log('Yeap!')
            });

        }

        function totalSizeQuantityMs() {

            var selected = $("#consignments").find(':selected').text();
            var selectedData = $("#consignments").find('option:selected');

            axios({
                url: stationAjaxUrl,
                method: 'post',
                data: {
                    process: 'totalSizeQuantityMs',
                    poNo: selected
                }

            }).then(function (response) {

                var consignmentId = $("#consignments").val();
                axios({
                    url: stationAjaxUrl,
                    method: 'post',
                    data: {
                        process: 'MsTotalQuantity',
                        consignmentId: consignmentId,
                    }
                }).then(function (response) {

                    $('#totalQuantity').html(response.data);

                }).catch(function (error) {
                    console.log(error);
                });

                var itemCount = response.data.totalSinglesValue;
                var totalFileQuantity = response.data.totalQuantityValue;

                var y2 = (itemCount * 2) / 100;
                var pPlus = itemCount + y2;
                var pSour = itemCount - y2;

                if (itemCount == totalFileQuantity) {
                    //yesil
                    $("#totalFileQuantity").css("background-color", "#81e276");

                } else if (itemCount > pSour && itemCount < pPlus) {
                    //sari
                    $("#totalFileQuantity").css("background-color", "#FD8624");

                } else if (itemCount < pSour || itemCount > pPlus) {
                    //kirmizi
                    $("#totalFileQuantity").css("background-color", "#ff5050");

                }

                $("#itemCount").html(itemCount);
                $("#totalFileQuantity").html(totalFileQuantity);

                $('#deliveryDate').text(selectedData.data('deliverydate'));
                $('#conn').text(selectedData.data('consignee'));
                $('#loading').hide();

            }).catch(function (error) {
                console.log(error);
            });

        }

        function _totalQuantityMs() {

            var dataTotalVal = "{{ session('totalQuantity')}}";
            alert(dataTotalVal);
            $('#totalQuantity').html(parseInt(dataTotalVal));

        }

        function readBarcode(barcodeValue) {

            var selected = $("#consignments").find(':selected').text();
            var consignmentId = $("#consignments").val();
            //var selected = $("#consignments").find('option:selected');
            var barcode = barcodeValue.replace(/\r?\n|\r/g, '');
            var barcode = barcodeValue.replace(/\s/g, '');
            var selected = selected.replace(/\r?\n|\r/g, '');
            var selected = selected.replace(/\s/g, '');

            axios({
                url: stationAjaxUrl,
                method: 'post',
                data: {
                    process: 'barcodeCheck',
                    consignmentId: consignmentId,
                    barcode: barcode
                }
            }).then(function (response) {

                //alert(response.data.res);
                //alert(checkBarcode);
                if (response.data.status == 'ok' && $('#consignments').val()) {

                    //localStorage.setItem('upc_cartons_id', JSON.stringify(response.data.res.upc_cartons_id));
                    var upc_cartons_id = response.data.res.upc_cartons_id;
                    var upc = response.data.res.upc;
                    var cartonID = response.data.res.cartonID;
                    var barcode = response.data.res.barcode;
                    var singles = response.data.res.singles;
                    var colourCode = response.data.res.colourCode;
                    var descriptions = response.data.res.descriptions;

                    //alert('okuyucuyu baslat verileri yaz');
                    //alert(response.data.status);

                    //alert(response.data.status);
                    $("#startStop").prop("disabled", false);
                    $('#startStop').trigger('click');

                    $('.btn-bil span').html(

                        "UPC: "+upc+" <br>"+
                        "Barcode: "+barcode

                    );

                } else if (response.data.status == 'nok'){

                    sweetAlert('@lang('station.barcode_not_warning') barcode_check', '', 'warning', "@lang('station.ok')");

                }

            }).catch(function (error) {
                console.log(error);
            });

        }

        function barcodeReturn(barcode){

            //alert('function_barcode_'+ barcode);
            if (barcode_status == 'Açık') {


                //totalSizeQuantityMs();
                //readBarcode('00050456000964584547');
                //console.log('gelen_barcode_' + barcode);
                //alert(barcode.length);
                var barcodeText = "";
                var barcodeTextOld = "";
                var readBarcodeValue = false;

                if (barcode.substr(0,4) == 'null'){
                    barcodeText = barcode.substr(4);
                }else{
                    barcodeText = barcode;
                }
                //barcodeText.substr(1);
                //alert(barcodeText);
                if (barcodeText != barcodeTextOld) {
                    //alert(barcodeText);
                    //console.log(e.key + '_<br>');
                    if ($("#startStop").prop("disabled", true)) {
                        readBarcode(barcodeText);
                    }
                }

                barcodeTextOld = barcodeText;
                barcodeText = "";
                if (!readBarcodeValue) {

                    readBarcodeValue = true;
                    barcodeText = "";
                    readBarcodeValue = false;
                }

            }

        }

        function startStop(e) {
            //alert('sad');

            //Sevkiyat kontrol
            if ($('#consignments').val() == '' || $('#consignments').val() == null) {
                sweetAlert('@lang('station.caution')', '@lang('station.choose_consignment')', 'warning', "@lang('station.ok')");
                return false;
            }
            readerMode = 'consignment';

            var type = $(e).attr('data-default');
            if (type == 'start') {



                startReader(readerId);



            } else {

                if (hangingStorage == 1){

                    consignment.checkClose(0);
                    stopReader(readerId);

                }else{

                    consignment.allClose();
                    stopReader(readerId);
                    window.location.reload();

                }

            }
        }

        $(function () {

            //localStorage.removeItem('upc_cartons_id');
            //alert(hangingStorage);
            msDataTableFunction();

            $.fn.dataTable.ext.errMode = 'none';
            setInterval(function () {

                var consignment_id = $('#consignments').val();
                $.ajax({

                    type: "POST",
                    url: "<?=Route('station.ajax')?>",
                    headers: {
                        'X-CSRF-Token': '{{ csrf_token() }}',
                    },
                    data: {
                        'process': "getTotalCountBar",
                        'consigment_id': consignment_id
                    },
                    success: function (data) {
                        //console.log(data);
                        //var data = JSON.parse(data);
                        $("#totalCarton").html(parseInt(data.carton));
                        $("#invalidCount").html(parseInt(data.invalidCount));
                        var total = parseInt($("totalQuantity").text());
                        if (total > 0 ){
                            $("totalQuantity").html( total - data.invalidCount);
                        }
                        $("#totalReadCarton").html(parseInt(data.totalReadBoxCount));
                    },
                    error: function (data) {
                        Swal.fire('@lang('station.cancelledMSG')', '', 'info');
                    }

                });

            }, 1500);

            // barcodeReturn("00050456000964584653");
            /*local test icin*/
            /*if (barcode_status == 'Açık') {
                //totalSizeQuantityMs();
                //readBarcode('00050456000964584547');
                var barcodeText = "";
                var barcodeTextOld = "";
                var readBarcodeValue = false;
                $(document).on('keypress', function (e) {

                    if (barcodeText.length < 20) {
                        //alert(barcodeText);
                        var regex = /^[0-9]+$/;
                        if (e.key.match(regex)) {
                            barcodeText += e.key;
                        }

                    } else {

                        //barcodeText.substr(1);
                        //alert(barcodeText);

                        if (barcodeText != barcodeTextOld) {
                            //alert(barcodeText);

                            //console.log(e.key + '_<br>');
                            if ($("#startStop").prop("disabled", true)) {

                                readBarcode(barcodeText);

                            }
                        }
                        barcodeTextOld = barcodeText;
                        barcodeText = "";
                    }

                    if (!readBarcodeValue) {
                        readBarcodeValue = true;
                        setTimeout(() => {

                            barcodeText = "";
                            readBarcodeValue = false;

                        }, 400);
                    }

                });

            } */

            if (hangingStorage == 1){

                MSdatatable.column(3).visible(false);
                MSdatatable.column(5).visible(false);

            }else{

                MSdatatable.column(3).visible(true);
                MSdatatable.column(5).visible(true);

            }

        });

        function msDataTableFunction(){

            MSdatatable = $('#msDataTable').DataTable({
                "searching": false,
                "paging": false,
                "serverSide": true,
                "bInfo": false,
                "ordering": false,
                "retrieve": true,
                'ajax': {
                    url: '<?=Route('station.msdata')?>',
                    method: 'POST',
                    headers: {
                        'X-CSRF-Token': '{{ csrf_token() }}',
                    },
                    data: function (d) {
                        d.consignmentId = $('#consignments').val()
                    },

                },
                "createdRow": function (row, data, dataIndex) {
                    //console.log(row);
                    //$(row).remove(data.targetCount);
                    $(row).addClass(data.baseRowClass);
                    $(row).addClass(data.baseTextClass);
                },
                columns: [
                    {
                        className: 'dt-control',
                        orderable: false,
                        data: null,
                        defaultContent: '',
                    },
                    {
                        data: 'UPC',
                        className: 'cell-border'
                    },
                    {data: 'SIZE'},
                    {data: 'targetCount',},
                    {data: 'counted'},
                    {data: 'undcounted'},
                ]
            });


            $('#msDataTable tbody').on('click', 'td.dt-control', function () {
                var tr = $(this).closest('tr');
                var row = MSdatatable.row(tr);

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open this row
                    row.child(format(row.data())).show();
                    tr.addClass('shown');
                }
            });

        }

        /* Formatting function for row details - modify as you need */
        function format(d) {
            var data = JSON.parse(d.boxes);
            // `d` is the original data object for the row
            if (hangingStorage == 1 ){

                var table = '<table class="table">' +
                    '<thead>' +
                    '<tr>' +
                    '<td >' +
                    '@lang('station.description')' +
                    '</td>' +
                    '<td class="text-center">' +
                    '@lang('station.quantity')' +
                    '</td>' +
                    '<td class="text-center">' +
                    '@lang('station.actions')' +
                    '</td>' +
                    '</tr>' +
                    '</thead>' +
                    '<tbody>';

                for (i = 0; i < data.length; i++) {
                    table = table + '<tr class="' + data[i].CssClass + '">' +
                    '<td  onclick="descriptionModal(this)" data-description="' + d.description + '" data-carton ="' + data[i].cartonID + '">' +
                    data[i].colour +
                    '</td>' +
                    '<td class="text-center">' + data[i].counted + '</td>' +
                    '<td class="text-center">' +
                    '<a class="rereadButton"' +
                    'data-upc ="' + d.UPC + '" data-cosigment_id="' + $("#consignments").val() + '" data-barcode ="' + data[i].barcode + '"' +
                    '><i class="fa-solid fa-recycle"></i></a>' +
                    '</td>' +
                    '</tr>';
                }

            }else{

                var table = '<table class="table">' +
                    '<thead>' +
                    '<tr>' +
                    '<td  >' +
                    '@lang('station.barcode')' +
                    '</td>' +
                    '<td class="text-center" >' +
                    '@lang('station.carton_nu')' +
                    '</td>' +

                    '<td >' +
                    '@lang('station.description')' +
                    '</td>' +

                    '<td class="text-center">' +
                    '@lang('station.target_qty')' +
                    '</td>' +

                    '<td class="text-center">' +
                    '@lang('station.quantity')' +
                    '</td>' +
                    '<td class="text-center">' +
                    '@lang('station.invalidQuantity')' +
                    '</td>' +
                    '<td class="text-center">' +
                    '@lang('station.actions')' +
                    '</td>' +
                    '</tr>' +
                    '</thead>' +
                    '<tbody>';

                for (i = 0; i < data.length; i++) {
                    table = table + '<tr class="' + data[i].CssClass + '">' +
                        '<td >' + data[i].barcode + '</td>' +
                        '<td class="text-center" >' + data[i].cartonID + '</td>' +
                        '<td  onclick="descriptionModal(this)" data-description="' + d.description + '" data-carton ="' + data[i].cartonID + '">' +
                        data[i].colour +
                        '</td>' +
                        '<td class="text-center">' + data[i].singles + '</td>' +

                        '<td class="text-center">' + data[i].counted + '</td>' +
                        '<td class="text-center">' + data[i].Undefinecounted + '</td>' +
                        '<td class="text-center">' +
                        '<a class="rereadButton"' +
                        'data-upc ="' + d.UPC + '" data-cosigment_id="' + $("#consignments").val() + '" data-barcode ="' + data[i].barcode + '"' +
                        '><i class="fa-solid fa-recycle"></i></a>' +
                        '</td>' +
                        '</tr>';
                }

            }

            var table = table + '</tbody>' +
                '</table>';

            return table;
        }

        $(document).on('click', '.rereadButton', function (e) {
            e.stopPropagation();
            e.stopImmediatePropagation();
            var consigment_id = $(this).data('cosigment_id');
            var barcode = $(this).data('barcode');
            var upc = $(this).data('upc');

            Swal.fire({
                title: '@lang('station.rereadQuestion')',
                showDenyButton: true, showCancelButton: false,
                confirmButtonText: '@lang('station.readAgain')',
                denyButtonText: '@lang('station.cancel')',
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "<?=Route('station.reReadCarton')?>",
                        headers: {
                            'X-CSRF-Token': '{{ csrf_token() }}',
                        },
                        data: {
                            'barcode': barcode,
                            'upc': upc,
                            'consigment_id': consigment_id
                        },
                        success: function (data) {
                            var data = JSON.parse(data);
                            if (data.status == 200) {
                                Swal.fire('Rereading', data.msg, 'success');
                            } else {
                                Swal.fire('Rereading', data.msg, 'error');
                            }
                            //MSdatatable.ajax.reload();
                            window.location.reload();
                        },
                        fail: function (data) {
                            Swal.fire('@lang('station.cancelledMSG')', '', 'info')
                        }

                    });
                } else if (result.isDenied) {
                    Swal.fire('Changes are not saved', '', 'info')
                }
            });

        });

        $(document).on('click', '#startStopHanging', function (e){
           //alert('durdur');
           $('#startStop').trigger('click');
        });

    </script>

@endsection

