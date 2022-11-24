
<form action="#" method="post" id="consigmentDcStore">
    @csrf
    <input type="hidden" name="shipment_list_text" id="shipment_list_text" value="">
    <div class="form-group row">
        <div class="col-12">Okuma Ekranı Tam Anlamıyla Entegre Edilemedi. Form Gönderme İşlemi Gerçekleştirilememektedir.</div>
    </div>
    <div class="form-group row">
        <label class="col-3 col-form-label" for="po_no">@lang('station.order_number')</label>
        <div class="col-9">
            <input 
                type="text" 
                name="order_number" 
                class="form-control" 
                id="order_number" 
                value="{{ old('order_number') }}"
            >
        </div>
    </div>
    <div class="form-group row">
        <label class="col-3 col-form-label" for="quantity">@lang('station.product_quantity')</label>
        <div class="col-9">
            <input 
                type="number" 
                name="quantity" 
                class="form-control" 
                id="quantity" 
                value="{{ old('quantity') }}"
            >
        </div>
    </div>
    <div class="form-group row">
        <label class="col-3 col-form-label" for="delivery_date">@lang('station.delivery_date')</label>
        <div class="col-9">
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
    </div>
    <div class="form-group row">
        <label class="col-3 col-form-label" for="csv_list">@lang('portal.shipment_list')</label>
        <div class="col-9">
            <select name="csv_list" class="form-control" id="csv_list">
                <option value="">@lang('station.choose')</option>
            </select>
        </div>
    </div>
</form>

<script src="/station/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/station/js/bootstrap-datepicker.tr.min.js" type="text/javascript"></script>

<script type="text/javascript">

    $(function () {

        $('#dp3').datepicker({
            format: 'yyyy-mm-dd',
            language: "{{ app()->getLocale() }}",
            autoclose: true
        });

        listCsvFile();

        $("#consignmentSubmitBtn").on('click', function (){

            var btn = $(this);
            btn.hide();
            var form = document.querySelector('#consigmentDcStore');
            var data = new FormData(form);
            data.append('process', 'consigmentDcStore');
            consignee_id = {{$consignee}}
            data.append('sticker', consignee_id);
            axios({
                url : "{{ route('consignment.store') }}",
                method : 'post',
                data : data

            }).then(function (response) {

                if(response.data.status == false){
                    errorPrint(response.data.errors);
                }

                btn.attr('disabled', false);

                if(response.data.status == 'ok'){
                    window.location.href = response.data.url;
                }

            }).catch(function (error) {
                btn.attr('disabled', false);
                console.log(error);
            });

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

    function errorPrint(errors){
        $.each( errors, function( key, value ) {
            $("#" + key).addClass('is-invalid');
            $("#" + key).parent("div").find('.invalid-feedback').text(value);
        });
    }

</script>