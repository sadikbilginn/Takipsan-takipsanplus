<form action="#" method="post" id="consigmentZaraStore">
    @csrf
    <div class="form-group row">
        <label class="col-3 col-form-label" for="po_no">@lang('station.po_number') </label>
        <div class="col-9">
            <input type="text" name="po_no" class="form-control" id="po_no" value="{{ old('po_no') }}">
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
        <label class="col-3 col-form-label" for="item_count">@lang('station.delivery_date')</label>
        <div class="col-9">
            <div class="input-append date" id="dp3" data-date="{{ date('Y-m-d') }}" data-date-format="yyyy-mm-dd">
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

        $("#consignmentSubmitBtn").on('click', function (){

            var btn = $(this);
            btn.attr('disabled', true);
            var form = document.querySelector('#consigmentZaraStore');
            var data = new FormData(form);
            data.append('process', 'consigmentZaraStore');
            consignee_id = {{$consignee}}
            data.append('consignee_id', consignee_id);
            axios({
                url : "{{ route('consignment.store') }}",
                method : 'post',
                data : data

            }).then(function (response) {
                
                if(response.data.status == false){
                    console.log('ok gelmedi');
                    errorPrint(response.data.errors);
                }
                
                btn.attr('disabled', false);
                
                if(response.data.status == 'ok'){
                    console.log('ok geldi');
                    window.location.href = response.data.url;
                }

            }).catch(function (error) {
                btn.attr('disabled', false);
                console.log(error);
            });

        });

    });

</script>