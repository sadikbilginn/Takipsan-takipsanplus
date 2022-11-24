@extends('layouts.main')

@section('content')

<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

    <!-- begin:: Subheader -->
    <div class="kt-subheader kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">
                    @lang('portal.readTypes')
                </h3>
                <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                <div class="kt-subheader__group" id="kt_subheader_search">
                    <span class="kt-subheader__desc" id="kt_subheader_total">@lang('portal.add_new_readType')</span>
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
                <div class="kt-portlet kt-portlet--last kt-portlet--head-lg kt-portlet--responsive-mobile" id="kt_page_portlet">
                    <div class="kt-portlet__head kt-portlet__head--lg">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">@lang('portal.add_new_readType') <small> @lang('portal.form_text')</small></h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <a href="javascript:history.go(-1);" class="btn btn-clean kt-margin-r-10">
                                <i class="la la-arrow-left"></i>
                                <span class="kt-hidden-mobile">@lang('portal.back')</span>
                            </a>
                            <div class="btn-group">
                                <button type="button" class="btn btn-brand" onclick="document.getElementById('read_type_form').submit();">
                                    <i class="la la-check"></i>
                                    <span class="kt-hidden-mobile">@lang('portal.save')</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <form class="kt-form" id="read_type_form" action="{{ route('read-type.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-xl-2"></div>
                                <div class="col-xl-8">
                                    <div class="kt-section kt-section--first">
                                        <div class="kt-section__body">
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.read_name')</label>
                                                <div class="col-9">
                                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
                                                    @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.read_name') (EN)</label>
                                                <div class="col-9">
                                                    <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" value="{{ old('name_en') }}">
                                                    @error('name_en')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">Reader</label>
                                                <div class="col-9">
                                                    <select name="reader" class="form-control @error('reader') is-invalid @enderror">
                                                        <option value="" selected>@lang('portal.choose')</option>
                                                        @foreach(config('settings.readers') as $key => $value)
                                                            <option value="{{ $key }}" {{ old('reader') == $key ? 'selected' : '' }}> {{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('reader')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">Reader Mode</label>
                                                <div class="col-9">
                                                    <select name="reader_mode" id="reader_mode" class="form-control @error('reader_mode') is-invalid @enderror">
                                                        <option value="" selected>@lang('portal.choose')</option>
                                                        @foreach(config('settings.reader_mode') as $key => $value)
                                                            <option value="{{ $key }}" {{ old('reader_mode') == $key ? 'selected' : '' }}> {{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('reader_mode')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">Est. Population</label>
                                                <div class="col-9">
                                                    <input type="number" name="estimated_population" id="estimated_population" class="form-control @error('estimated_population') is-invalid @enderror" min="0"  value="{{ old('estimated_population', 0) }}">
                                                    @error('estimated_population')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">Search Mode</label>
                                                <div class="col-9">
                                                    <select name="search_mode" id="search_mode" class="form-control @error('search_mode') is-invalid @enderror">
                                                        <option value="" selected>@lang('portal.choose')</option>
                                                        @foreach(config('settings.search_mode') as $key => $value)
                                                            <option value="{{ $key }}" {{ old('search_mode') == $key ? 'selected' : '' }}> {{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('search_mode')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">Session</label>
                                                <div class="col-9">
                                                    <select name="session" id="session" class="form-control @error('session') is-invalid @enderror">
                                                        <option value="" selected>@lang('portal.choose')</option>
                                                        @foreach(config('settings.reader_session') as $key => $value)
                                                            <option value="{{ $key }}" {{ old('session') == $key ? 'selected' : '' }}> {{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('session')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.text_settings')</label>
                                                <div class="col-9">
                                                    <textarea name="string_set" id="string_set" cols="30" rows="10" class="form-control @error('name') is-invalid @enderror">{{ old('string_set') }}</textarea>
                                                    @error('string_set')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.status')</label>
                                                <div class="col-9">
                                                    <span class="kt-switch kt-switch--lg kt-switch--outline kt-switch--icon kt-switch--success  @error('status') is-invalid @enderror">
                                                        <label>
                                                            <input type="checkbox"  {{ old('status') == 'on' ? 'checked="checked"' : '' }} name="status" id="status">
                                                            <span></span>
                                                        </label>
                                                     </span>
                                                    @error('status')
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


@endsection
