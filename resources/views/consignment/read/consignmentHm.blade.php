<form action="#" method="post" id="consigmentHmStore">
    @csrf
    <div class="form-group row">
        <label class="col-3 col-form-label" for="country_code">@lang('station.country')</label>
        <div class="col-9">
            <select id="country_code" name="country_code" class="form-control select">
                @foreach($country_list as $value)
                <option value="{{ $value->country_list_name }}">{{ $value->country_list_name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-3 col-form-label" for="item_count">@lang('station.product_quantity')</label>
        <div class="col-9">
            <input 
                type="number" 
                name="item_count" 
                class="form-control" 
                id="item_count" 
                min="1" 
                value="0" 
                value="{{ old('item_count') }}"
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
        <label class="col-3 col-form-label" for="db_list">@lang('station.upload_list')</label>
        <div class="col-9">
            <input type="file" id="db_list" name="db_list" accept=".csv">
        </div>
    </div>
</form>

<script src="/station/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/station/js/bootstrap-datepicker.tr.min.js" type="text/javascript"></script>

<script type="text/javascript">
    
    $(function() {

        $('#dp3').datepicker({
            format: 'yyyy-mm-dd',
            language: "{{ app()->getLocale() }}",
            autoclose: true
        });

        $('.select').select2();

        $("#consignmentSubmitBtn").on('click', function (){

            var btn = $(this);
            btn.hide();
            var form = document.querySelector('#consigmentHmStore');
            var data = new FormData(form);
            data.append('process', 'consigmentHmStore');
            sticker = {{$consignee}};
            data.append('sticker', sticker);
            axios({
                url : "{{ route('consignment.store') }}",
                method : 'post',
                data : data
            }).then(function (response) {

                if(response.data.status == false){
                    errorPrint(response.data.errors);
                    btn.show();
                }

                btn.attr('disabled', false);

                if(response.data.status == 'ok'){
                    window.location.href = response.data.url;
                }

            }).catch(function (error) {
                alert('CATCH');
                // btn.attr('disabled', false);
                console.log('HATA:'+error);
                location.reload();
            });

        });

        function errorPrint(errors){
            $.each( errors, function( key, value ) {
                $("#" + key).addClass('is-invalid');
                $("#" + key).parent("div").find('.invalid-feedback').text(value);
            });
        }

    });

</script>