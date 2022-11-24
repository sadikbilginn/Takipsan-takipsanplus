<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>Takipsan RFID</title>
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no, user-scalable=no"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="author" content="Mehmet Karabulut">
    <meta name="generator" content="Takipsan V2">
    <!-- <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0"/> -->
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!-- Bootstrap core CSS -->
    <link href="/station/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <!-- <link href="/station/js/bootstrap-select/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous"> -->

    <!-- Favicons -->
    <link rel="icon" href="/station/img/favicon.ico">

    <!-- Custom styles for this template -->
    <link href="/station/css/component-custom-switch.min.css" rel="stylesheet">
    <link href="/station/css/animate.css" rel="stylesheet">
    <link href="/station/css/custom.css?{{ time() }}" rel="stylesheet">
    <link href="/station/css/bootstrap-datepicker3.min.css" rel="stylesheet">
    <!-- <link href="/assets/vendors/general/select2/dist/css/select2.css" rel="stylesheet" type="text/css" /> -->

    <link rel="stylesheet" href="/station/js/bootstrap-select/dist/css/bootstrap-select.css">

    @yield('css')

    <script type="text/javascript">
        let stationAjaxUrl  = '{{ route('station.ajax') }}';
        let stationAjaxView = '{{ route('station.stationviewAjax')}}';
    </script>

    @yield('headScripts')

</head>
<body>
<div id="loading" class="blockUi" style="display: none;"></div>

<div class="container-fluid">
    @yield('content')
</div>

<script src="/station/js/jquery-3.5.1.min.js"></script>
<script src="/station/js/popper.min.js"></script>
<script src="/station/js/bootstrap.min.js"></script>
<!--<script src="/station/js/sweetalert2@9.js"></script> -->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/station/js/axios.min.js"></script>
<!-- <script src="/station/js/select2/dist/js/select2.full.js" type="text/javascript"></script> -->
<!-- <script src="/station/js/select2/dist/js/select2.full.js" type="text/javascript"></script> -->

<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->
<script src="/station/js/bootstrap-select/dist/js/bootstrap-select.js"></script>

@yield('js')

<script src="/assets/js/delete.js"></script>
<script type="text/javascript">
    $(document).ready(function(){

        var theme = localStorage.getItem('theme');
        var hanging = localStorage.getItem('hangingProduct');

        if(theme != null){
            $('body').addClass(theme);
            $('body .change-theme > img').attr('src', '/station/img/' + theme + '.svg');
        }

        $( window ).resize(function() {
            resize();
        });

        resize();

        var viewId = localStorage.getItem('gorunum');
        if (viewId == 3){

            $('.btn-print, .btn-combine, .btn-bil, .btn-delete, .btn-find').attr("style", "display: none !important");

            if (hanging != 1){

                $('.btn-bil').attr("style", "display: block !important");

            }
        }
        @if (session('readBarcode'))

            $('.btn-bil span').html(

                "UPC: {{session('readBarcode.upc')}} <br>"+
                "Barcode: {{session('readBarcode.barcode')}}"

            );

        @else

            $('.btn-bil span').html(

                "UPC: - <br> Barcode: -"

            );

        @endif

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


        //activePacketInf();
        $('.th-title').click();

    });

    function _activePacketInf(){

        var activePackageId = localStorage.getItem('activePackageId');
        axios({
            url   : stationAjaxUrl,
            method: 'post',
            data  : {
                process : 'packagesQuery',
                packageId : activePackageId
            }
        }).then(function (response) {

            console.log(JSON.stringify(response));

            if (viewId == 3 && response.data.status == 'ok'){

                $('.btn-bil span').html(
                    "Barcode: " + response.data.barcode + "<br>"+
                    "@lang('station.carton'): " + response.data.packageNo
                );

            }else{

                $('.btn-bil span').html(
                    "Barcode: - <br>"+
                    "@lang('station.carton'): " + 0
                );

            }

        }).catch(function (error) {
            console.log(error);
        });

        var viewId = localStorage.getItem('gorunum');
        if (viewId == 3){
            $('.btn-print, .btn-combine').attr("style", "display: none !important");
            //$('.btn-bil').attr("style", "display: block !important");
        }

    }

    function resize(){

        var sayfaYukseklik = $(window).height();
        if (sayfaYukseklik > 768){
            //topbar header yuksekligi 228
            $('.content').height(sayfaYukseklik - 228);
            //table boslugu 30
            $('.tables').height(sayfaYukseklik - 228 - 30);
            //consigment total yuksekligi 110
            $('.table-ss').height(sayfaYukseklik - 228 - 30 - 110);
            //table thead yuksekligi 132
            $('.content table.custom-table tbody').height(sayfaYukseklik - 228 - 30 - 110 - 132);
        }

        if (sayfaYukseklik > 731 && sayfaYukseklik <= 800){
            //topbar header yuksekligi
            $('.content').height(sayfaYukseklik - 180);
            //table boslugu
            $('.tables').height(sayfaYukseklik - 180 - 30);
            //consigment total yuksekligi
            $('.table-ss').height(sayfaYukseklik - 180 - 30 - 95);
            //table thead yuksekligi
            $('.content table.custom-table tbody').height(sayfaYukseklik - 180 - 30 - 95 - 120);
        }

        if (sayfaYukseklik <= 731){
            //topbar header yuksekligi
            $('.content').height(sayfaYukseklik - 228);
            //table boslugu
            $('.tables').height(sayfaYukseklik - 228 - 30);
            //consigment total yuksekligi
            $('.table-ss').height(sayfaYukseklik - 228 - 30 - 110);
            //table thead yuksekligi
            $('.content table.custom-table tbody').height(sayfaYukseklik - 228 - 30 - 110 - 132);
        }

    }

    function logout(){

        localStorage.removeItem('deviceId');

        window.location = "{{ route('station.logout') }}";
    }

    $('.change-theme').click(function () {

        if($('body').hasClass('sun')){
            $('body').removeClass('sun');
            $('body').addClass('moon');
            localStorage.setItem('theme', 'moon');
            $('body .change-theme > img').attr('src', '/station/img/moon.svg');

        }else{
            $('body').removeClass('moon');
            $('body').addClass('sun');
            localStorage.setItem('theme', 'sun');
            $('body .change-theme > img').attr('src', '/station/img/sun.svg');
        }

    });
</script>


@if (Session::has('flash_message'))
    <script type="application/javascript">
        $(document).ready(function(){
            Swal.fire({
                title: "{{Session::get('flash_message')[0]}}",
                text: "{{Session::get('flash_message')[1]}}",
                icon: "{{Session::get('flash_message')[2]}}",
                confirmButtonText: "@lang('station.ok')",
                cancelButtonText: "@lang('station.cancel')",
                confirmButtonClass: 'btn btn-primary',
                cancelButtonClass: 'btn btn-light'
            });
        });
    </script>
@endif

@if(1==2)
    <script type="text/javascript" defer>
        $(function() {
            $('body').append(
                '<span'+
                    'id="q"'+
                    'style="'+
                        'position:fixed;'+
                        'top:0px;'+
                        'right:0px;'+
                        'z-index:9999999;'+
                        'opacity: .5;'+
                        'padding: 2px 5px;'+
                        'background-color: #fff;'+
                        'box-shadow: 0 0 2px #666;'+
                        'font-family:Arial;'+
                        'font-weight:bold;'+
                    '"'+
                '>'+
                    $(window).width() + "*" + $(window).height()+
                '</span>'
            );
        });
        // Update resolution info
        $(window).on('load ready resize orientationchange', function() {
            $('#q').html($(window).width() + "*" + $(window).height());
        });
    </script>
@endif
</body>
</html>
