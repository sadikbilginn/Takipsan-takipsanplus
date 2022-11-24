@extends('station.layout.main')

@section('content')

    @include('station.header')

    <div class="animated fadeIn test" style="position:relative; z-index:99;">
        <div class="row header">
            @include('station.read.select')
            <div class="col-lg-3">
                <div class="row consignment-info">
                    <div class="col-12">@lang('station.company'): <span id="conn" ></span></div>
                    <div class="col-12">@lang('station.delivery_date') : <span id="deliveryDate"></span></div>
                </div>
            </div>
            <div class="col-lg-4">
                @include('station.read.butons')
            </div>
        </div>

        @include('station.read.read_zara')

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
    <script src="/station/js/bridge-zara.js" crossorigin="anonymous"></script>
    <script type="text/javascript">

        @if(request()->has('consignment'))
            localStorage.setItem('consignmentId', {{ request()->get('consignment') }});
        @endif

        class Tag {
            constructor(epc, firstSeenTime) {
                this.epc = epc;
                this.firstSeenTime = firstSeenTime;
            }
        }

        // Epc Testi & Epc Test
        document.addEventListener("keyup", function(event) {

            if ( event.keyCode == 13) {
                const testEpcs = [
                    "3036143A582105400000000A",
                    "3036143A5821050000000001",
                    "3036143A000000CB0B032147",
                    "3036143A000000CB0B03604B",
                    "3036143A000000CB0B03EC1D"
                ];
                const tags = [];

                testEpcs.forEach((epc) => {
                    const tag = new Tag(epc, new Date());
                    tag.firstSeenTime = new Date();

                    tags.push(tag);
                })

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
                url : stationAjaxUrl,
                method : 'post',
                data : {
                    process : url,
                    param : param
                }
            }).then(function (response) {

                $('#pageModal .modal-body').html(response.data.html);
                $('#pageModal').modal('show');

            }).catch(function (error) {
                console.log(error);
            });

        }

        // $(function() {

        //     $('#consignments > option').each(function(){

        //         var bugun = new Date();
        //         bugun.setMonth(bugun.getMonth() - 1);

        //         var gun = bugun.getDate();
        //         var ay = bugun.getMonth() + 1;
        //         if(ay<10){
        //             ay =('0'+ay);
        //         }
        //         var yil = bugun.getFullYear();
        //         var birAyOncekiTarih = gun +"."+ ay +"."+ yil;

        //         var acilisTarih = $(this).data('createdate');

        //         if (acilisTarih != undefined){

        //             acilisTarih = new Date($(this).data('createdate'));
        //             acilisTarih.setMonth(acilisTarih.getMonth() - 1);
        //             var acilisGun = acilisTarih.getDate();
        //             var acilisAy = acilisTarih.getMonth() + 1;
        //             if(acilisAy<10){
        //                 acilisAy =('0'+acilisAy);
        //             }
        //             var acilisYil = acilisTarih.getFullYear();
        //             var acilisBirAyOncesi = acilisGun +"."+ acilisAy +"."+ acilisYil;
        //             alert(acilisBirAyOncesi);
        //             // alert(acilisBirAyOncesi+'_acilis_oncekiay_'+birAyOncekiTarih);

        //             // if (acilisBirAyOncesi >= birAyOncekiTarih){
        //             //     alert('sad');
        //             //     var id = $(this).val();
        //             //     // axios({
        //             //     //     url : stationAjaxUrl,
        //             //     //     method : 'post',
        //             //     //     data : {
        //             //     //         process : 'closeConsignment',
        //             //     //         consignmentId : id,
        //             //     //         CloseShipmentSt : 1
        //             //     //     }
        //             //     // }).then(function (response) {
        //             //     //     localStorage.removeItem('consignmentId');
        //             //     //     sweetAlert(
        //             //     //         '@lang('station.successful')',
        //             //     //         '@lang('station.consignment_old_closed')',
        //             //     //         'success',
        //             //     //         "@lang('station.ok')"
        //             //     //     );
        //             //     //     window.location.reload();
        //             //     // }).catch(function (error) {
        //             //     //     sweetAlert('@lang('station.failed')', '@lang('station.error_text')', 'error', "@lang('station.ok')");
        //             //     //     console.log(error);
        //             //     // });

        //             // }
        //         }

        //     });

        // });

        var current_model;
        $("#consignments").on('change', function () {
            $('#loading').show();

            var view = $(this).find(':selected').data('view');
            var select = $(this).val();
            var consignmentId = $(this).val();
            var selected = $(this).find('option:selected');
            var id = $(this).find(':selected').data('consigneeview');
            // if (!id){
            //     id = localStorage.getItem('gorunum');
            // }

            localStorage.setItem('consignmentId', consignmentId);
            localStorage.setItem('selectId', consignmentId);
            localStorage.setItem('gorunum', {{ $gorunum }});

            if (id == 1){
                //script.src = '/station/js/zara/bridge-zara.js?v={{date('YmdHs')}}?eraseCache=true';
                // window.location.href="{{ URL::asset('/read') }}";
                // return false;
            }else if (id == 2){
                //script.src = '/station/js/hm/bridge.js?v={{date('YmdHs')}}?eraseCache=true';
                window.location.href="{{ URL::asset('/read2') }}";
                return false;
            }else if (id == 3){
                window.location.href="{{ URL::asset('/read3') }}";
                return false;
            }else if (id == 4){
                window.location.href="{{ URL::asset('/read4') }}";
                return false;
            }else if (id == 5){
                window.location.href="{{ URL::asset('/read5') }}";
                return false;
            }else if (id == 6){
                window.location.href="{{ URL::asset('/read6') }}";
                return false;
            }


            $('#totalQuantity').text(0);
            $('#itemCount').text(0);
            $('#itemLeft').text(0);
            $('#selectedQuantity').text(0);
            $('#itemCount').text(selected.data('itemcount'));
            $('#itemLeft').text(selected.data('itemcount'));
            $('#deliveryDate').text(selected.data('deliverydate'));
            $('#conn').text(selected.data('consignee'));
            //current_model = selected.data('model');

            //eğer reader start ise durdur
            if(readerStatus == true){
                stopReader(readerId);
            }

            //db işlemleri
            axios({
                url : stationAjaxUrl,
                method : 'post',
                data : {
                    process : 'getPackages',
                    consignmentId : consignmentId
                }
            }).then(function (response) {
                insertFromDbPackage(response.data.list);

                axios({
                    url : stationAjaxUrl,
                    method : 'post',
                    data : {
                        process : 'getItems',
                        consignmentId : consignmentId,
                        ids : response.data.ids
                    }
                }).then(function (response) {
                    insertFromDbItem(response.data.list);
                    $('#loading').hide();
                }).catch(function (error) {
                    $('#loading').hide();
                    console.log(error);
                });

                $(".check").prop('checked', false);

            }).catch(function (error) {
                console.log(error);
                $('#loading').hide();
            });

            return false;

        });

        function startStop(e) {

            //Sevkiyat kontrol
            if($('#consignments').val() == '' || $('#consignments').val() == null){
                sweetAlert('@lang('station.caution')', '@lang('station.choose_consignment')', 'warning', "@lang('station.ok')");
                return false;
            }
            readerMode = 'consignment';

            var type = $(e).attr('data-default');
            if(type == 'start') {

                startReader(readerId);

            }else{
                consignment.allClose();

                stopReader(readerId);

            }
        }
        // Table basma Zara
        function insertRow(packageId, model = '', size  = ''){

            $(".check").prop('checked', false);

            // sizeMap.forEach(function (size, key) {
            //     if(size != '' && size != 0){
            //         $('#consignmentDetails tbody').append('<tr>'
            //             +'<td>'+key+'</td><td>'+size+'</td>'
            //             +'</tr>');
            //     }
            // });

            var row = '<tr id='+ packageId +'>'
                +'<td style="text-align: center;">' +
                    '<div class="custom-control custom-checkbox">' +
                        '<input type="checkbox" class="custom-control-input check" onchange="checkClick(this);" checked id="customCheck'+ packageId +'"' +
                            'value="'+ packageId +'">' +
                        '<label class="custom-control-label" for="customCheck'+ packageId +'">&nbsp;</label>' +
                    '</div>' +
                '</td>' +
                '<td>@lang('station.package') '+ packageId +'</td>' +
                '<td>'+'0'+'</td>' +
                '<td onclick="modelEdit('+ packageId +');">'+ model +'</td>' +
                '<td onclick="sizeEdit('+ packageId +');">'+ size +'</td>' +
                '</tr>';

            $('#consignmentList tbody').prepend(row);

        }
        //Sevkiyat düzenleme sayfası
        function editConsignment(){

            // Sevkiyat kontrol
            if($('#consignments').val() == '' || $('#consignments').val() == null){
                sweetAlert('@lang('station.caution')', '@lang('station.choose_consignment')', 'warning', "@lang('station.ok')");
                return false;
            }

            getPage('editConsignment', { id : $('#consignments').val() })
        }
        //Paket birleştirme
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
                allVals.push($(this).attr('data-id'));
            });

            if(allVals.length <= 1){
                sweetAlert('@lang('station.caution')', '@lang('station.select_package')', 'warning', "@lang('station.ok')");
                return false;
            }

            $("#startStop").prop("disabled", true);

            Swal.fire({
                title : '@lang('station.are_you_sure')',
                text : "@lang('station.combine_caution')",
                icon : 'warning',
                allowOutsideClick : false,
                showCancelButton : true,
                confirmButtonColor : '#3085d6',
                cancelButtonColor : '#d33',
                cancelButtonText : '@lang('station.cancel')',
                confirmButtonText : '@lang('station.yes_combine')'
            }).then((result) => {
                if (result.value) {
                    swal.fire({
                        allowOutsideClick : false,
                        icon : 'warning',
                        title : '@lang('station.wait')',
                        onOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    axios({
                        url : stationAjaxUrl,
                        method : 'post',
                        data : {
                            process : 'combinePackages',
                            consignmentId : $('#consignments').val(),
                            packages : allVals,
                        }
                    }).then(function (response) {

                        insertFromDbPackage(response.data.list);

                        axios({
                            url : stationAjaxUrl,
                            method : 'post',
                            data : {
                                process : 'getItems',
                                consignmentId : $('#consignments').val(),
                                ids : response.data.ids
                            }
                        }).then(function (response) {

                            window.location.href =  window.location.href;
                            window.location.reload();
                            // insertFromDbItem(response.data.list);

                            // sweetAlert(
                            //     '@lang('station.successful')',
                            //     '@lang('station.selected_package_combined')',
                            //     'success',
                            //     "@lang('station.ok')"
                            // );
                            // $('#selectedQuantity').text(0);
                            // $("#checkAll, .check").prop('checked', false);

                        }).catch(function (error) {
                            sweetAlert(
                                '@lang('station.failed')',
                                '@lang('station.error_text')',
                                'error',
                                "@lang('station.ok')"
                            );
                            console.log(error);
                        });

                    }).catch(function (error) {
                        sweetAlert(
                            '@lang('station.failed')',
                            '@lang('station.error_text')',
                            'error',
                            "@lang('station.ok')"
                        );
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
                allVals.push($(this).attr('data-id'));
            });

            if(allVals.length <= 0){
                sweetAlert('@lang('station.caution')', '@lang('station.select_package')', 'warning', "@lang('station.ok')");
                return false;
            }

            $("#startStop").prop("disabled", true);

            Swal.fire({
                title : '@lang('station.are_you_sure')',
                text : "@lang('station.delete_caution')",
                icon : 'warning',
                allowOutsideClick : false,
                showCancelButton : true,
                confirmButtonColor : '#3085d6',
                cancelButtonColor : '#d33',
                cancelButtonText : '@lang('station.cancel')',
                confirmButtonText : '@lang('station.yes_delete')'
            }).then((result) => {
                if (result.value) {
                    swal.fire({
                        allowOutsideClick : false,
                        icon : 'warning',
                        title : '@lang('station.wait')',
                        onOpen : () => {
                            Swal.showLoading();
                        }
                    });
                    axios({
                        url : stationAjaxUrl,
                        method : 'post',
                        data : {
                            process : 'deletePackages',
                            consignmentId : $('#consignments').val(),
                            packages : allVals,
                        }
                    }).then(function (response) {

                        insertFromDbPackage(response.data.list);

                        axios({
                            url : stationAjaxUrl,
                            method : 'post',
                            data : {
                                process : 'getItems',
                                consignmentId : $('#consignments').val(),
                                ids : response.data.ids
                            }
                        }).then(function (response) {

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
        //Paket Yazdırma
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

            if(allVals.length <= 0){
                sweetAlert('@lang('station.caution')', '@lang('station.select_package')', 'warning', "@lang('station.ok')");
                return false;
            }

            //eğer reader start ise durdur
            if(readerStatus == true){
                stopReader(readerId);
            }

            Swal.fire({
                title : '@lang('station.are_you_sure')',
                text : "@lang('station.print_caution')",
                icon : 'warning',
                allowOutsideClick : false,
                showCancelButton : true,
                confirmButtonColor : '#3085d6',
                cancelButtonColor : '#d33',
                cancelButtonText : '@lang('station.cancel')',
                confirmButtonText : '@lang('station.yes_print')'
            }).then((result) => {
                if (result.value) {

                    ld = new PrintData();
                    ld.printer = printerAddress;
                    ld.headMargin = false;
                    ld.footMargin = false;

                    ld.lines.push(new LineData("", true));
                    ld.lines.push(new LineData("", true));

                    br.printLinesPlus2(ld);

                    ld = new LabelData();
                    ld.printer = printerAddress;
                    ld.headMargin = false;
                    ld.footMargin = true;
                    ld.client = $('#consigneeName').text();
                    ld.boxNo = allVals.length > 1 ? allVals.length : allVals[0];
                    ld.numberOfBoxes = allVals.length;

                    $('#consignmentDetails tbody tr').each(function(){
                        ld.sizes.push(new SizeData($(this).find('td:nth-child(1)').text(), $(this).find('td:nth-child(2)').text()));
                    });

                    br.printLabelPlus2(ld);

                    ld = new PrintData();
                    ld.printer = printerAddress;
                    ld.headMargin = false;
                    ld.footMargin = false;

                    ld.lines.push(new LineData("", true));
                    ld.lines.push(new LineData(printLineCheck("Date: {{ date('Y/m/d H:i:s') }}"), true));
                    ld.lines.push(new LineData("", true));

                    br.printLinesPlus2(ld);

                    ld = new PrintData();
                    ld.printer = printerAddress;
                    ld.headMargin = true;
                    ld.footMargin = false;
                    ld.lines.push(new LineData("", true));
                    ld.lines.push(new LineData("", true));
                    ld.lines.push(new LineData(printLineCheck('PO:' + $('#consignments option:selected').text()), true));

                    br.printLinesPlus2(ld);

                }
            })
        }
        //Otomatik Yazdırma
        function autoPrint() {

            var allVals = [];
            $(".check:checked").each(function() {
                allVals.push($(this).val());
            });

            if(allVals.length > 0){
                var printer = printerAddress;

                ld = new PrintData();
                ld.printer = printer;
                ld.headMargin = false;
                ld.footMargin = true;

                ld.lines.push(new LineData("", true));
                ld.lines.push(new LineData("", true));

                br.printLinesPlus2(ld);

                ld = new LabelData();
                ld.printer = printer;
                ld.headMargin = false;
                ld.footMargin = false;
                ld.client = $('#consigneeName').text();
                ld.boxNo = allVals.length > 1 ? allVals.length : allVals[0];
                ld.numberOfBoxes = allVals.length;

                $('#consignmentDetails tbody tr').each(function(){
                    ld.sizes.push(new SizeData($(this).find('td:nth-child(1)').text(), $(this).find('td:nth-child(2)').text()));
                });

                br.printLabelPlus2(ld);

                ld = new PrintData();
                ld.printer = printer;
                ld.headMargin = false;
                ld.footMargin = false;

                ld.lines.push(new LineData("", true));
                ld.lines.push(new LineData(printLineCheck("Date: {{ date('Y/m/d H:i:s') }}"), true));
                ld.lines.push(new LineData("", true));

                br.printLinesPlus2(ld);

                ld = new PrintData();
                ld.printer = printer;
                ld.headMargin = true;
                ld.footMargin = false;
                ld.lines.push(new LineData("", true));
                ld.lines.push(new LineData("", true));
                ld.lines.push(new LineData(printLineCheck('PO:' + $('#consignments option:selected').text()), true));

                br.printLinesPlus2(ld);
            }

        }
        //Satır yazdırma
        function printLineCheck(text) {

            return text.substring(0, 24);
        }
        //Paket Bulma
        function findPackage() {

            // Eğer reader start ise durdur
            if(readerStatus == true){
                stopReader(readerId);
            }

            // Sevkiyat kontrol
            if($('#consignments').val() == '' || $('#consignments').val() == null){
                sweetAlert('@lang('station.caution')', '@lang('station.choose_consignment')', 'warning', "@lang('station.ok')");
                return false;
            }

            Swal.fire({
                title : '@lang('station.are_you_sure')',
                text : '@lang('station.find_caution')',
                icon : 'warning',
                allowOutsideClick : false,
                showCancelButton : true,
                confirmButtonColor : '#3085d6',
                cancelButtonColor : '#d33',
                cancelButtonText : '@lang('station.cancel')',
                confirmButtonText : '@lang('station.yes_find')',
            }).then((result) => {
                if (result.value) {
                    swal.fire({
                        allowOutsideClick: false,
                        icon : 'warning',
                        title : '@lang('station.wait')',
                        onOpen : () => {
                            Swal.showLoading();
                        }
                    });

                    //eğer reader start ise durdur
                    if(readerStatus == true && readerMode == 'consignment'){
                        stopReader(readerId);
                    }

                    readerMode = 'find';
                    buffer.epcs = new Set();
                    startReader(readerId);

                    setTimeout(function () {

                        stopReader(readerId);

                        if(buffer.epcs.size  > 0){

                            axios({
                                url : stationAjaxUrl,
                                method : 'post',
                                data : {
                                    process : 'findPackages',
                                    consignmentId : $('#consignments').val(),
                                    epc : Array.from(buffer.epcs)
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
                                        icon : "success",
                                        title : "@lang('station.package') " + response.data.package.package_no,
                                        html : html,
                                        confirmButtonText : '@lang('station.ok')'
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
        //Sevkiyat kapatma
        @if(auth()->user()->company->consignment_close == true)

        function closeConsignment() {

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
                title : '@lang('station.are_you_sure')',
                text : "@lang('station.consignment_caution')",
                icon : 'warning',
                allowOutsideClick : false,
                showCancelButton : true,
                confirmButtonColor : '#3085d6',
                cancelButtonColor : '#d33',
                cancelButtonText : '@lang('station.cancel')',
                confirmButtonText : '@lang('station.yes_close')'
            }).then((result) => {
                if (result.dismiss != 'cancel' && result.value) {
                    swal.fire({
                        allowOutsideClick : false,
                        icon : 'warning',
                        title : '@lang('station.wait')',
                        onOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    axios({
                        url : stationAjaxUrl,
                        method : 'post',
                        data : {
                            process : 'closeConsignment',
                            consignmentId : $('#consignments').val(),
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
        //Model düzenleme
        function modelEdit(packageNo) {

            //eğer reader start ise durdur
            if(readerStatus == true){
                stopReader(readerId);
            }

            var val = $('#consignmentList tbody tr#' + packageNo + ' td')[3].innerText;

            Swal.fire({
                position : 'top',
                title : '@lang('station.model_name')',
                input : 'text',
                inputValue : val,
                showCancelButton : true,
                confirmButtonColor : '#3085d6',
                cancelButtonColor : '#d33',
                cancelButtonText : '@lang('station.cancel')',
                confirmButtonText : '{{ title_case(trans('station.save')) }}',
                showLoaderOnConfirm : true,
                preConfirm: (model) => {
                    return axios({
                        url : stationAjaxUrl,
                        method : 'post',
                        data : {
                            process : 'modelEdit',
                            consignmentId : $('#consignments').val(),
                            packageNo : packageNo,
                            model : model
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
                    $('#consignmentList tbody tr#' + packageNo + ' td')[3].innerText = result.value;
                }
            });
        }
        // size düzenleme
        function sizeEdit(packageNo) {

            //eğer reader start ise durdur
            if(readerStatus == true){
                stopReader(readerId);
            }

            var val = $('#consignmentList tbody tr#' + packageNo + ' td')[4].innerText;

            Swal.fire({
                position : 'top',
                title : '@lang('station.size')',
                input : 'text',
                inputValue : val,
                showCancelButton : true,
                confirmButtonColor : '#3085d6',
                cancelButtonColor : '#d33',
                cancelButtonText : '@lang('station.cancel')',
                confirmButtonText : '{{ title_case(trans('station.save')) }}',
                showLoaderOnConfirm : true,
                preConfirm: (size) => {
                    return axios({
                        url : stationAjaxUrl,
                        method : 'post',
                        data : {
                            process : 'sizeEdit',
                            consignmentId : $('#consignments').val(),
                            packageNo : packageNo,
                            size : size
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
                allowOutsideClick : () => !Swal.isLoading()
            }).then((result) => {
                if (result.value) {
                    $('#consignmentList tbody tr#' + packageNo + ' td')[4].innerText = result.value;
                }
            });
        }
        // model düzenleme
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
                position : 'top',
                title : '@lang('station.model_name')',
                input : 'text',
                showCancelButton : true,
                confirmButtonColor : '#3085d6',
                cancelButtonColor : '#d33',
                cancelButtonText : '@lang('station.cancel')',
                confirmButtonText : '{{ title_case(trans('station.save')) }}',
                showLoaderOnConfirm : true,
                preConfirm : (model) => {
                    return axios({
                        url : stationAjaxUrl,
                        method : 'post',
                        data : {
                            process : 'modelAllEdit',
                            consignmentId : $('#consignments').val(),
                            packages : allVals,
                            model : model
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
                allowOutsideClick : () => !Swal.isLoading()
            }).then((result) => {
                if (result.value) {
                    allVals.forEach(function(item){
                        $('#consignmentList tbody tr#' + item + ' td')[3].innerText = result.value;
                    });
                }
            });
        }
        // size  düzenleme toplu
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
                position : 'top',
                title : '@lang('station.size')',
                input : 'text',
                showCancelButton : true,
                confirmButtonColor : '#3085d6',
                cancelButtonColor : '#d33',
                cancelButtonText : '@lang('station.cancel')',
                confirmButtonText : '{{ title_case(trans('station.save')) }}',
                showLoaderOnConfirm : true,
                preConfirm : (size) => {
                    return axios({
                        url : stationAjaxUrl,
                        method : 'post',
                        data : {
                            process : 'sizeAllEdit',
                            consignmentId : $('#consignments').val(),
                            packages : allVals,
                            size : size
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
                allowOutsideClick : () => !Swal.isLoading()
            }).then((result) => {
                if (result.value) {
                    allVals.forEach(function(item){
                        $('#consignmentList tbody tr#' + item + ' td')[4].innerText = result.value;
                    });
                    window.location.reload();
                }
            });
        }
        //model açma
        function modalOpen() {
            $('#readModal .read .read-header').html('@lang('station.package_read')');
            $('#readModal .read .read-header').removeClass('read-success');
            $('#packageTotal').text('0');
            $('#readModal .read .read-media').html("<img src=\"/station/img/box-animation.gif\">\n" +
                "                                    <br>\n" +
                "                                    <img src=\"/station/img/load.gif\">");
            $('#readModal').modal('show');
        }
        // modal(popup) kapatma
        function modalClose() {

            $('#readModal .read .read-header').html('@lang('station.package_closed')');
            $('#readModal .read .read-header').addClass('read-success');
            $('#readModal .read .read-media').html("<img src=\"/station/img/check-animation.gif\">");

            setTimeout(function () {
                $('#readModal').modal('hide');
            }, 500)
        }
        // modal(popup) hatalı ise
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
