@extends('layouts.main')

@section('content')
    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
        <!-- begin:: Subheader -->
        <div class="kt-subheader kt-grid__item" id="kt_subheader">
            <div class="kt-container  kt-container--fluid ">
                <div class="kt-subheader__main">
                    <h3 class="kt-subheader__title">
                        @lang('portal.consignments')
                    </h3>
                    <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                    <div class="kt-subheader__group" id="kt_subheader_search">
                        <span class="kt-subheader__desc" id="kt_subheader_total">@lang('portal.edit')</span>
                    </div>
                </div>
                <div class="kt-subheader__toolbar"></div>
            </div>
        </div>
        <!-- end:: Subheader -->
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="row">
                <div class="col-lg-12">
                    <!--begin::Portlet-->
                    <div 
                        class="kt-portlet kt-portlet--last kt-portlet--head-lg kt-portlet--responsive-mobile" 
                        id="kt_page_portlet"
                    >
                        <div class="kt-portlet__head kt-portlet__head--lg">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    @lang('portal.edit') <small> @lang('portal.form_text')</small>
                                </h3>
                            </div>
                            <div class="kt-portlet__head-toolbar">
                                <a href="javascript:history.go(-1);" class="btn btn-clean kt-margin-r-10">
                                    <i class="la la-arrow-left"></i>
                                    <span class="kt-hidden-mobile">@lang('portal.back')</span>
                                </a>
                                <div class="btn-group">
                                    <button 
                                        type="button" 
                                        class="btn btn-brand" 
                                        id="consignmentSubmitBtn"
                                    >
                                        <i class="la la-check"></i>
                                        <span class="kt-hidden-mobile">@lang('portal.save_changes')</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <form action="#" method="post" class="kt-form" id="updateConsignment">
                                @csrf
                                <div class="row">
                                    <div class="col-xl-2"></div>
                                    <div class="col-xl-8">
                                        <div class="kt-section kt-section--first">
                                            <div class="kt-section__body">
                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label" for="po_no">
                                                        @lang('station.po_number')
                                                    </label>
                                                    <div class="col-9">
                                                        <input 
                                                            type="text" 
                                                            name="po_no" 
                                                            class="form-control" 
                                                            id="po_no" 
                                                            disabled 
                                                            value="{{ old('po_no', $consignment->order->po_no) }}"
                                                        >
                                                    </div>
                                                </div>
                                                {{--
                                                @if (isset($consignment->order->name))
                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label" for="name">
                                                        @lang('station.model_name')
                                                    </label>
                                                    <div class="col-9">
                                                        <input 
                                                            type="text" 
                                                            name="name" 
                                                            class="form-control" 
                                                            id="name" 
                                                            value="{{ old('name', $consignment->order->name) }}"
                                                        >
                                                    </div>
                                                </div>
                                                @endif --}}
                                                @if (isset($consignment->country_code) && $consignment->country_code != "")
                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label" for="consignee_id">
                                                        @lang('station.country')
                                                    </label>
                                                    <div class="col-9">
                                                        <select id="country_code" name="country_code" class="form-control">
                                                            @foreach($country_list as $value)
                                                            <option 
                                                                value="{{ $value->country_list_name }}" 
                                                                {{
                                                                    $value->country_list_name == 
                                                                        old(
                                                                            'consigment->country_code', 
                                                                            $consignment->country_code
                                                                        ) ? 
                                                                            'selected' : ''
                                                                }}
                                                            >
                                                                {{ $value->country_list_name }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                @endif
                                                @if (isset($consignment->item_count) && $consignment->item_count > 0)
                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label" for="item_count">
                                                        @lang('station.product_quantity')
                                                    </label>
                                                    <div class="col-9">
                                                        <input 
                                                            type="number" 
                                                            name="item_count" 
                                                            class="form-control" 
                                                            id="item_count" 
                                                            min="1" 
                                                            value="{{ old('item_count', $consignment->item_count) }}"
                                                        >
                                                    </div>
                                                </div>
                                                @endif
                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label">
                                                        @lang('portal.delivery_date')
                                                    </label>
                                                    <div class="col-9">
                                                        <input 
                                                            type="date" 
                                                            class="form-control @error('delivery_date') is-invalid @enderror" 
                                                            name="delivery_date" 
                                                            value="{{ old('delivery_date', $consignment->delivery_date) }}"
                                                        >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!--end::Portlet-->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $("#consignmentSubmitBtn").on('click', function (){
            
            var btn = $(this);
            btn.attr('disabled', true);
            var form = document.querySelector('#updateConsignment');
            var data = new FormData(form);
            data.append('process', 'updateConsignment');
            data.append('id', '{{ $consignment->id }}');
            axios({
                url   : "{{ route('consignment.store') }}",
                method: 'post',
                data  : data
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
                    window.location.href = response.data.url;
                }

            }).catch(function (error) {
                btn.attr('disabled', false);
            });

        });

        function errorPrint(errors){
            $.each( errors, function( key, value ) {
                $("#" + key).addClass('is-invalid');
                $("#" + key).parent("div").find('.invalid-feedback').text(value);
            });
        }
    </script>
@endsection