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
        <form action="#" method="post" id="consigmentMsStore" enctype="multipart/form-data">
            @csrf
            <div class="form-row">
                <div class="po_number form-group col-md-6">
                    <label for="po_no">@lang('station.po_number')</label>
                    <input type="text" name="po_no" class="form-control" id="po_no" value="{{ old('po_no') }}">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="form-group col-md-6">
                    <label for="po_no">@lang('station.hanging_product')</label>
                    <select id="hanging_product" name="hanging_product" class="form-control">
                        <option value="Hayır">@lang('station.no')</option>
                        <option value="Evet">@lang('station.yes')</option>
                    </select>
                </div>
                <?php /*
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
                </div> */ ?>
            </div>
            <div class="form row">
                <?php /*
                <div class="form-group col-md-12">
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
                    <label for="consignee_id">
                        @lang('station.upload_list_pdf') :
                    </label>
                    <span style="width:100%; display:block;">
                    <label for="db_list" class="btn btn-secondary" style="font-size:26px; color:#fff">
                        @lang('station.file_upload_btn')
                    </label>
                    <span  id="file-chosen">
                        @lang('station.file_path')
                    </span>
                    <input type="file" id="db_list" name="db_list" hidden>
                    </span>
                </div>

                <div class="form-group col-md-6">
                    <label for="consignee_id" style="margin-top: 50px;margin-left: 30px;">
                        @lang('station.upload_list_pdf') :
                    </label>
                    <input type="file" id="db_list" name="db_list">
                </div> */ ?>
            </div>
            <!-- <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="load_type">Load Type</label>
                    <select>
                        <option>Please select...</option>
                        <option value="Solid">Solid</option>
                        <option value="Assortment">Assortment</option>
                        <option value="Solid Last">Solid</option>
                    </select>
                </div>
                <div class="form-group col-md-6"></div>
            </div> -->
            <div class="form-row">
                <div class="col-md-6"></div>
                <div class="form-group col-md-6" id="consignmentSubmitDiv">
                    <button type="button" class="btn btn-block btn-primary submit" id="consignmentSubmitBtn">
                        @lang('station.save')
                    </button>
                    <div id="loading_" style="display:none;">
                        <img src="{{url('/station/img/loading.gif')}}" style="margin-top:-133px;margin-bottom:-152px;" />
                    </div>
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
        //$('#country_code').select2();

        // const actualBtn = document.getElementById('db_list');
        // const fileChosen = document.getElementById('file-chosen');
        // actualBtn.addEventListener('change', function(){
        //     fileChosen.textContent = this.files[0].name
        // });

        $('#consigmentMsStore').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });

    });

    $(".close-page").on('click', function (){
        window.localStorage.removeItem('etiket');
    });

    $("#consignmentSubmitBtn").on('click', function (){

        var btn = $(this);
        btn.hide();
        $('#loading_').show();
        //$('#consignmentSubmitDiv').empty();
        $('.invalid-feedback').text('');
        $('.form-control').removeClass('is-invalid');
        $sticker = window.localStorage.getItem('etiket');
        var form = document.querySelector('#consigmentMsStore');
        var data = new FormData(form);
        data.append('process', 'consigmentMsStore');
        data.append('sticker', $sticker);
        console.log(data['process']);
        //db işlemleri
        axios({
            url : stationAjaxUrl,
            method : 'post',
            data : data
        }).then(function (response) {

            if(response.data.status == false){
                errorPrint(response.data.errors);
                $('#loading_').hide();
                $('#pageModal').modal('hide');
                sweetAlert('@lang('station.caution')', '@lang('station.po_warning')', 'warning', "@lang('station.ok')");
            }

            btn.attr('disabled', false);

            if(response.data.status == 'ok'){

                console.log(response);

                localStorage.setItem('consignmentId', response.data.consignmentId);
                localStorage.setItem('hangingProduct', response.data.hanging_product);
                window.location.href = response.data.url;
            }

        }).catch(function (error) {
            //alert('CATCH');
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

    // $("#consignmentSubmitBtn").on('click', function (){
    //     var btn = $(this);
    //     debugger;
    //     btn.attr('disabled', true);
    //     $('.invalid-feedback').text('');
    //     $('.form-control').removeClass('is-invalid');
    //     var form = document.querySelector('#consignmentStore');
    //     var data = new FormData(form);
    //     data.append('process', 'consignmentStore');

    //     //db işlemleri
    //     axios({
    //         url   : stationAjaxUrl,
    //         method: 'post',
    //         data  : data
    //     }).then(function (response) {
    //         if(response.data.status == false){
    //             errorPrint(response.data.errors);
    //         }
    //         btn.attr('disabled', false);
    //         if(response.data.status == 'ok'){
    //             window.location.href = response.data.url;
    //         }
    //     }).catch(function (error) {
    //         btn.attr('disabled', false);
    //         console.log(error);
    //     });
    // });

</script>
