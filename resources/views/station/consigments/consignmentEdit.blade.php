<div class="row consignment">
    <div class="col-12 text-center">
        <span class="form-title">@lang('station.edit_consignment')</span>
        <span class="close-page">
            <button type="button" class="btn btn-circle close" data-dismiss="modal" aria-label="Close">
                <span class="icon-delete">X</span>
            </button>
        </span>
    </div>
    <div class="col-12"><hr class="clearfix"></div>
    <div class="col-12">
        <form action="#" method="post" id="updateConsignment">
            @csrf
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="po_no">@lang('station.po_number')</label>
                    <input 
                        type="text" 
                        name="po_no" 
                        class="form-control" 
                        id="po_no" 
                        disabled 
                        value="{{ old('po_no', $consignment->order->po_no) }}"
                    >
                    <div class="invalid-feedback"></div>
                </div>
                @if (isset($consignment->order->name))
                <div class="form-group col-md-6 d-none">
                    <label for="name">@lang('station.model_name')</label>
                    <input type="text" name="name" class="form-control" id="name" value="{{ old('name', $consignment->order->name) }}">
                    <div class="invalid-feedback"></div>
                </div>
                @endif
            </div>
            <div class="form-row">
                @if (isset($consignment->country_code) && $consignment->country_code != "")
                <div class="form-group col-md-6 country_code">
                    <label for="consignee_id">@lang('station.country')</label>
                    <select id="country_code" name="country_code" class="form-control">
                        @foreach($country_list as $value)
                        <option 
                            value="{{ $value->country_list_name }}" 
                            {{
                                $value->country_list_name == old(
                                    'consigment->country_code', $consignment->country_code
                                ) ? 
                                    'selected' : ''
                            }}
                        >
                            {{ $value->country_list_name }}
                        </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                @endif
                @if (isset($consignment->item_count) && $consignment->item_count > 0)
                <div class="form-group col-md-6">
                    <label for="item_count">@lang('station.product_quantity')</label>
                    <input 
                        type="number" 
                        name="item_count" 
                        class="form-control" 
                        id="item_count" 
                        min="1" 
                        value="{{ old('item_count', $consignment->item_count) }}"
                    >
                    <div class="invalid-feedback"></div>
                </div>
                @endif
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
                            value="{{ old('delivery_date', $consignment->delivery_date) }}" 
                            placeholder="yyyy-mm-dd"
                        >
                        <span class="add-on"><i class="icon-th"></i></span>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="form-row">
                {{--
                <div class="form-group col-md-6">
                    <label for="plate_no">@lang('station.plate_no')</label>
                    <input 
                        type="text" 
                        name="plate_no" 
                        class="form-control" 
                        id="plate_no" 
                        value="{{ old('plate_no', $consignment->plate_no) }}"
                    >
                    <div class="invalid-feedback"></div>
                </div>
                --}}
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
        
        //$('#country_code').select2();

    });
    
    $("#consignmentSubmitBtn").on('click', function (){
        
        var btn = $(this);
        //debugger;
        btn.attr('disabled', true);
        $('.invalid-feedback').text('');
        $('.form-control').removeClass('is-invalid');
        var form = document.querySelector('#updateConsignment');
        var data = new FormData(form);
        data.append('process', 'updateConsignment');
        data.append('id', '{{ $consignment->id }}');
        data.append('consignee_id', localStorage.getItem('etiket'));
        //db işlemleri
        axios({
            url : stationAjaxUrl,
            method : 'post',
            data : data
        }).then(function (response) {
            
            btn.attr('disabled', false);
            if(response.data.status == false){
                if(response.data.errors){
                    errorPrint(response.data.errors);
                }else{
                    sweetAlert('@lang('station.failed')', '@lang('station.error_text')', 'error', "@lang('station.ok')");
                }
            }
            
            if(response.data.status == 'ok'){
                //$('#consignments').trigger('change');
                // ajax olarak listeleme yapılana kadar bu sekilde resetlenecek.
                //$("#consignments").select2("val", "");
                //location.reload();
                //var selected = $('#consignments').find('option:selected');
                //alert(selected.data('itemcount'));
                $('#pageModal').modal('hide');
                $('#itemCount').text($("#item_count").val());
                $('#deliveryDate').text($("#delivery_date").val());
                //$('#consigneeName').text($('#consignee_id').find('option:selected').text());
                setTimeout(function (){
                    location.reload();
                    $('#pageModal .modal-body').html("");
                },1000);
                
                sweetAlert(
                    '@lang('station.successful')', 
                    '@lang('station.consignment_updated')',
                    'success', "@lang('station.ok')"
                );
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
