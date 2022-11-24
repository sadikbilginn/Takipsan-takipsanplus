@extends('layouts.main')

@section('content')

    @php
        $lang = app()->getLocale();
        $title = "title_".$lang;
        $major = $lang == 'tr' ? 'Önemli' : 'Major';
        $emergency = $lang == 'tr' ? 'Acil' : 'Emergency';

    @endphp

    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
        <!-- begin:: Subheader -->
        <div class="kt-subheader kt-grid__item" id="kt_subheader">
            <div class="kt-container  kt-container--fluid ">
                <div class="kt-subheader__main">
                    <h3 class="kt-subheader__title">@lang('portal.supportAdd')</h3>
                    <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                    <div class="kt-subheader__group" id="kt_subheader_search">
                        <span class="kt-subheader__desc" id="kt_subheader_total">@lang('portal.add')</span>
                    </div>
                </div>
                <div class="kt-subheader__toolbar"></div>
            </div>
        </div>
        <!-- end:: Subheader -->
        <div class="kt-container  kt-container--fluid kt-grid__item kt-grid__item--fluid">
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
                                    @lang('portal.add') <small> @lang('portal.form_text')</small>
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
                                        onclick="document.getElementById('support_form').submit();"
                                    >
                                        <i class="la la-check"></i>
                                        <span class="kt-hidden-mobile">@lang('portal.save')</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <form
                                class="kt-form"
                                id="ms_size_form"
                                action="{{ route('support.store') }}"
                                method="post"
                            >
                                @csrf
                                <div class="row">
                                    <div class="col-xl-2"></div>
                                    <div class="col-xl-8">
                                        <div class="kt-section kt-section--first">
                                            <div class="kt-section__body">

                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label">
                                                        @lang('portal.consignment')
                                                    </label>
                                                    <div class="col-9">
                                                        <select
                                                            name="company"
                                                            id="company"
                                                            class="
                                                                form-control
                                                                select2
                                                                @error('company') is-invalid @enderror
                                                            "
                                                            style="width: 100%"
                                                        >
                                                            <option value="" selected>@lang('portal.choose')</option>
                                                            @foreach($company as $value)
                                                                <option value="{{ $value->title }}">
                                                                    {{$value->title }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('company')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label">
                                                        @lang('portal.consignment')
                                                    </label>
                                                    <div class="col-9">
                                                        <select
                                                            name="consignment_id"
                                                            id="consignment_id"
                                                            class="
                                                                form-control
                                                                select2
                                                                @error('consignment_id') is-invalid @enderror
                                                            "
                                                            style="width: 100%"
                                                        >
                                                            <option value="" selected>@lang('portal.choose')</option>
                                                            @foreach($support as $value)
                                                                <option value="{{ $value->id }}">
                                                                    {{$value->$title }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('consignment_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label">
                                                        @lang('portal.consignment')
                                                    </label>
                                                    <div class="col-9">
                                                        <select
                                                            name="device"
                                                            id="device"
                                                            class="
                                                                form-control
                                                                select2
                                                                @error('device') is-invalid @enderror
                                                            "
                                                            style="width: 100%"
                                                        >
                                                            <option value="" selected>@lang('portal.choose')</option>
                                                            @foreach($device as $value)
                                                                <option value="{{ $value->id }}">
                                                                    {{$value->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('device')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label">
                                                        @lang('portal.consignment')
                                                    </label>
                                                    <div class="col-9">
                                                        <select
                                                            name="supportStatus"
                                                            id="supportStatus"
                                                            class="
                                                                form-control
                                                                select2
                                                                @error('supportStatus') is-invalid @enderror
                                                            "
                                                            style="width: 100%"
                                                        >
                                                            <option value="" selected>@lang('portal.choose')</option>
                                                            <option value="Normal">Normal</option>
                                                            <option value="{{$major}}">{{$major}}</option>
                                                            <option value="{{$emergency}}">{{$emergency}}</option>
                                                        </select>
                                                        @error('supportStatus')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label">@lang('portal.po_no')</label>
                                                    <div class="col-9">
                                                        <input
                                                            type="text"
                                                            class="form-control @error('order') is-invalid @enderror"
                                                            name="order"
                                                            value="{{ old('order') }}"
                                                        >
                                                        @error('order')
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
                </div>
            </div>
        </div>

    </div>
@endsection

@section('css')
    <link href="/assets/vendors/custom/jstree/jstree.bundle.css" rel="stylesheet" type="text/css" />
@endsection

@section('js')
    <script>

        $(function() {

            $('.select2').select2();

        });

    </script>
@endsection
