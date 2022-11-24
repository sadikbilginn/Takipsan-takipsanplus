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
        <form action="#" method="post" id="consigmentZaraStore">
            @csrf
            <div class="form-row">
                <div class="po_number form-group col-md-12">
                    <label for="po_no">@lang('station.po_number')</label>
                    <input type="text" name="po_no" class="form-control" id="po_no" value="{{ old('po_no') }}">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="company_name form-group col-md-6 d-none">
                    <label for="company_name">@lang('station.company_name')</label>
                    <input
                        type="text"
                        name="company_name"
                        class="form-control"
                        id="company_name"
                        value="{{ old('company_name') }}"
                    >
                    <div class="invalid-feedback"></div>
                </div>
                {{--<div class="form-group col-md-6">
                    <label for="model_name">@lang('station.model_name')</label>
                    <input type="text" name="name" class="form-control" id="model_name" value="{{ old('model_name') }}">
                    <div class="invalid-feedback"></div>
                </div>--}}
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="item_count">@lang('station.product_quantity')</label>
                    <input
                        type="number"
                        name="item_count"
                        class="form-control"
                        id="item_count"
                        min="1"
                        value="0"
                        value="{{ old('item_count') }}"
                    >
                    <div class="invalid-feedback"></div>
                </div>
                <div class="form-group col-md-6">
                    <label for="delivery_date">@lang('station.delivery_date')</label>
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

        var other = localStorage.getItem('other');

        if (other === 'true'){
            $('.po_number').removeClass('col-md-12').addClass('col-md-6');
            $('.company_name').removeClass('d-none');
        }

        $('#dp3').datepicker({
            format: 'yyyy-mm-dd',
            language: "{{ app()->getLocale() }}",
            autoclose: true
        });

        $('#consigmentZaraStore').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });

    });

    $(".close-page").on('click', function (){
        window.localStorage.removeItem('etiket');
        window.localStorage.removeItem('other');
        //localStorage.setItem('other', response.data.other);
    });

    $("#consignmentSubmitBtn").on('click', function (){
        var btn = $(this);
        btn.attr('disabled', true);
        $('.invalid-feedback').text('');
        $('.form-control').removeClass('is-invalid');
        window.localStorage.removeItem('other');
        var form = document.querySelector('#consigmentZaraStore');
        var data = new FormData(form);
        data.append('process', 'consigmentZaraStore');
        consignee_id = localStorage.getItem('etiket');
        data.append('consignee_id', consignee_id);
        console.log(data['process']);
        //db işlemleri
        axios({
            url : stationAjaxUrl,
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
