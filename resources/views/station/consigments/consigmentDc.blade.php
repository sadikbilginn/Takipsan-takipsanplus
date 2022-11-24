<div class="row consignment">
    <div class="col-12 text-center">
        <span class="form-title">@lang('station.add_consignment')</span>
        <span class="close-page">
            <button type="button" class="btn btn-circle close" data-dismiss="modal" aria-label="Close">
                <span class="icon-delete">X</span>
            </button>
        </span>
    </div>
    <div class="col-12"><hr class="clearfix"></div>
    <div class="col-12">
        <form action="#" method="post" id="consigmentDcStore">
            @csrf
            <input type="hidden" name="shipment_list_text" id="shipment_list_text" value="">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="order_number">@lang('station.order_number')</label>
                    <input
                        type="text"
                        name="order_number"
                        class="form-control"
                        id="order_number"
                        value="{{ old('order_number') }}"
                    >
                    <div class="invalid-feedback"></div>
                </div>
                <div class="form-group col-md-6">
                    <label for="quantity">@lang('station.product_quantity')</label>
                    <input
                        type="number"
                        name="quantity"
                        class="form-control"
                        id="quantity"
                        value="{{ old('quantity') }}"
                    >
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="delivery_date">@lang('station.delivery_date')</label>
                    <div
                        class="input-append date"
                        id="dp3"
                        data-date="{{ date('Y-m-d') }}"
                        data-date-format="yyyy-mm-dd"
                    >
                        <input
                            type="text"
                            readonly
                            size="16"
                            name="delivery_date"
                            class="form-control span2"
                            id="delivery_date"
                            value="{{ old('delivery_date') }}"
                            placeholder="yyyy-mm-dd"
                        >
                        <span class="add-on"><i class="icon-th"></i></span>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="csv_list">@lang('portal.shipment_list')</label>
                    <select name="csv_list" class="form-control" id="csv_list">
                        <option value="">@lang('station.choose')</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6"></div>
                <div class="form-group col-md-6">
                    <button type="button" class="btn btn-block btn-primary submit" id="consignmentSubmitBtn">
                        @lang('station.save')
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">

    $(function () {

        $('#dp3').datepicker({
            format: 'yyyy-mm-dd',
            language: "{{ app()->getLocale() }}",
            autoclose: true
        });

        listCsvFile();

        $('#consigmentMsStore').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });

    });

    $('#csv_list').on('change', function (){

        var val = $(this).val();

        if(val == 'refresh'){
            listCsvFile();
        }

        if(val != ''){
            readCsvFile(val);
        }

    });

    function readCsvFile(path){

        br.readCsvFilePromise(path).then(function (result) {

            console.log('csv_file_content:');
            console.log(result);
            $('#shipment_list_text').val(result.body);

        }).catch(function (err) {

            console.log("err readCsvFile >>> " + err);

        });

        console.log("csv file readed");

    }

    function listCsvFile(){

        console.log("csv file List");
        br.listCsvFilePromise().then(function (result) {

            console.log(result);

            if(JSON.parse(result.body).length > 0){
                $('#csv_list').html("");
                $('#csv_list').append('<option value="">@lang('station.choose')</option>');
                $.each(JSON.parse(result.body) ,function(index, value){
                    $('#csv_list').append($('<option>', {
                        value: value.path,
                        text: value.name
                    }));
                });
            }

            $('#csv_list').append('<option value="refresh">Yenile</option>');

        }).catch(function (err) {

            console.log("err listCsvFile >>> " + err);

        });
    }

    $("#consignmentSubmitBtn").on('click', function (){

        var btn = $(this);
        btn.attr('disabled', true);
        $('.invalid-feedback').text('');
        $('.form-control').removeClass('is-invalid');
        $sticker = window.localStorage.getItem('etiket');
        var form = document.querySelector('#consigmentDcStore');
        var data = new FormData(form);
        data.append('process', 'consigmentDcStore');
        data.append('sticker', $sticker);

        //db işlemleri
        axios({
            url : stationAjaxUrl,
            method : 'post',
            data : data
        }).then(function (response) {

            if(response.data.status == false){
                errorPrint(response.data.errors);
            }

            btn.attr('disabled', false);

            if(response.data.status == 'ok'){
                localStorage.setItem('consignmentId', response.data.consignmentId);
                window.location.href = response.data.url;
            }

        }).catch(function (error) {
            btn.attr('disabled', false);
            console.log(error);
        });

    });

    function errorPrint(errors){
        $.each( errors, function( key, value ) {
            $("#" + key).addClass('is-invalid');
            $("#" + key).parent("div").find('.invalid-feedback').text(value);
        });
    }

</script>
