@extends('layouts.main')

@section('content')

<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

    <!-- begin:: Subheader -->
    <div class="kt-subheader kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <button class="kt-subheader__mobile-toggle kt-subheader__mobile-toggle--left" id="kt_subheader_mobile_toggle"><span></span></button>
                <h3 class="kt-subheader__title">@lang('portal.devices')</h3>
                <span class="kt-subheader__separator kt-hidden"></span>
                <div class="kt-subheader__breadcrumbs">
                    <a href="{{ url('/') }}" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
                    <span class="kt-subheader__breadcrumbs-separator"></span>
                    <a href="{{ route('company.index') }}" class="kt-subheader__breadcrumbs-link">@lang('portal.companies')</a>
                    <span class="kt-subheader__breadcrumbs-separator"></span>
                    <a href="{{ route('company.index', $device->company->id) }}" class="kt-subheader__breadcrumbs-link">{{ $device->company->name }}</a>
                    <span class="kt-subheader__breadcrumbs-separator"></span>
                    <a href="#" class="kt-subheader__breadcrumbs-link">{{ config('settings.devices.' . $device->device_type . '.name') }}</a>
                </div>
            </div>
            <div class="kt-subheader__toolbar">
                <a href="javascript:history.go(-1);" class="btn btn-default btn-bold"><i class="la la-angle-left"></i> @lang('portal.back') </a>
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
                            <h3 class="kt-portlet__head-title">{{ config('settings.devices.' . $device->device_type . '.name') }}<small> cihaz bilgileri</small></h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <a href="javascript:history.go(-1);" class="btn btn-clean kt-margin-r-10">
                                <i class="la la-arrow-left"></i>
                                <span class="kt-hidden-mobile">@lang('portal.back')</span>
                            </a>
                            <div class="btn-group">
                                <button type="button" class="btn btn-brand" onclick="window.location='{{ route('company.device.edit', $device->id) }}';">
                                    <i class="la la-edit"></i>
                                    <span class="kt-hidden-mobile">@lang('portal.edit')</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="row">
                            <div class="col-xl-2"></div>
                            <div class="col-xl-8">
                                <div class="kt-section kt-section--first">
                                    <div class="kt-section__body">
                                        <div class="form-group row">
                                            <label class="col-3">@lang('portal.device_type')</label>
                                            <div class="col-9">
                                                {{ config('settings.devices.' . $device->device_type . '.name') }}
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-3">@lang('portal.status')</label>
                                            <div class="col-9">
                                                {!! $device->status == 1 ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Pasif</span>'; !!}
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-3">@lang('portal.image')</label>
                                            <div class="col-9">
                                                <img id="logo_prev" src="{{ config('settings.devices.' . $device->device_type . '.logo')}}" width="100" alt="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
        $(function() {
            $('input[type=file]').on('change', function (event) {
                $('#logo_prev').attr('src', URL.createObjectURL(event.target.files[0]));
            });
        });
    </script>
@endsection
