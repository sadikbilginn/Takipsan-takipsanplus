@extends('layouts.main')

@section('content')

<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

    <!-- begin:: Subheader -->
    <div class="kt-subheader kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">
                    Lisanslar
                </h3>
                <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                <div class="kt-subheader__group" id="kt_subheader_search">
                    <span class="kt-subheader__desc" id="kt_subheader_total">Yeni Lisans Ekle</span>
                </div>
            </div>
            <div class="kt-subheader__toolbar">
            </div>
        </div>
    </div>

    <!-- end:: Subheader -->

    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="row">
            <div class="col-lg-12">
                <!--begin::Portlet-->
                <div class="kt-portlet kt-portlet--last kt-portlet--head-lg kt-portlet--responsive-mobile" id="kt_page_portlet">
                    <div class="kt-portlet__head kt-portlet__head--lg">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">Yeni Lisans Ekle <small> aşağıdaki alanları doldurunuz</small></h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <a href="javascript:history.go(-1);" class="btn btn-clean kt-margin-r-10">
                                <i class="la la-arrow-left"></i>
                                <span class="kt-hidden-mobile">Geri</span>
                            </a>
                            <div class="btn-group">
                                <button type="button" class="btn btn-brand" onclick="document.getElementById('license_form').submit();">
                                    <i class="la la-check"></i>
                                    <span class="kt-hidden-mobile">Kaydet</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <form class="kt-form" id="license_form" action="{{ route('license.store') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-xl-2"></div>
                                <div class="col-xl-8">
                                    <div class="kt-section kt-section--first">
                                        <div class="kt-section__body">
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.companies')</label>
                                                <div class="col-9">
                                                    <select name="company_id" id="company_id" class="form-control select @error('company_id') is-invalid @enderror">
                                                        <option value="" selected>@lang('portal.choose')</option>
                                                        @if(isset($companies))
                                                            @foreach($companies as $key => $value)
                                                                <option value="{{ $value->id }}" {{ $value->id == old('company_id') ? 'selected' : '' }}> {{ $value->name }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    @error('company_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            {{--<div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.manufacturer')</label>
                                                <div class="col-9">
                                                    <select name="manufacturer_id" id="manufacturer_id" class="form-control select @error('manufacturer_id') is-invalid @enderror">
                                                        <option value="" selected>@lang('portal.choose')</option>
                                                    </select>
                                                    @error('manufacturer_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.license_type')</label>
                                                <div class="col-9">
                                                    <select name="license_type" id="license_type" class="form-control select @error('license_type') is-invalid @enderror">
                                                        <option value="" selected>@lang('portal.choose')</option>
                                                        <option value="Lite">Lite</option>
                                                        <option value="Pro">Pro</option>
                                                    </select>
                                                    @error('license_type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>--}}
                                            <!-- <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.user')</label>
                                                <div class="col-9">
                                                    <select name="user_id" id="user_id" class="form-control select @error('user_id') is-invalid @enderror">
                                                        <option value="" selected>@lang('portal.choose')</option>
                                                    </select>
                                                    @error('user_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div> -->
                                            
                                            {{-- Eski Yıl bazlı Lisans seçimi  --}}
                                            {{-- <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.license_period')</label>
                                                <div class="col-9">
                                                    <select name="license_period" class="form-control @error('license_period') is-invalid @enderror">
                                                        <option value="" selected>Seçiniz</option>
                                                        @for($i=1; $i<=10; $i++)
                                                            <option value="{{ $i }}" {{ old('license_period') == $i ? 'selected' : '' }}>{{ $i }} Yıl</option>
                                                        @endfor
                                                    </select>
                                                    @error('license_period')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div> --}}
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.license_start')</label>
                                                <div class="col-9">
                                                    <input type="date" class="form-control @error('license_start') is-invalid @enderror" name="start_at" value="{{ old('license_start') }}">
                                                    @error('start_at')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.license_finish')</label>
                                                <div class="col-9">
                                                    <input type="date" class="form-control @error('license_finish') is-invalid @enderror" name="finist_at" value="{{ old('license_finish') }}">
                                                    @error('finish_at')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
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

        $('#company_id').on('change', function (){
            var company    = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('ajax') }}",
                method: 'post',
                data: {
                    'process'           : 'manufacturerListCompany',
                    'company_id'        : company,
                },
                beforeSend: function(xhr, opts) {
                    $('#user_id').html('')
                },
                success: function(result){
                    if(result.status == 'success'){
                        $('#manufacturer_id').empty();
                        result = jQuery.parseJSON(result.data);
                        for(var k in result) {
                            $("#manufacturer_id").append(new Option(result[k].name, result[k].id));

                        }
                    }
                }
            });
        });

        // $('#manufacturer_id').on('change', function (){
        //     var company         = $("#company_id").val();
        //     var manufacturer    = $(this).val();
        //     $.ajaxSetup({
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         }
        //     });
        //     $.ajax({
        //         url: "{{ route('ajax') }}",
        //         method: 'post',
        //         data: {
        //             'process'           : 'userListManufacturer',
        //             'company_id'        : company,
        //             'manufacturer_id'   : manufacturer,
        //         },
        //         beforeSend: function(xhr, opts) {
        //             $('#user_id').html('')
        //         },
        //         success: function(result){

        //             if(result.status == 'success'){
        //                 result = jQuery.parseJSON(result.data);
        //                 for(var k in result) {
        //                     $("#user_id").append(new Option(result[k].name, result[k].id));

        //                 }
        //             }}
        //     });
        // });
        
    </script>

@endsection
