@extends('layouts.main')

@section('content')

<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

    <!-- begin:: Subheader -->
    <div class="kt-subheader kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">
                    @lang('portal.firms_to_ship')
                </h3>
                <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                <div class="kt-subheader__group" id="kt_subheader_search">
                    <span class="kt-subheader__desc" id="kt_subheader_total">@lang('portal.add_new_consignee')</span>
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
                            <h3 class="kt-portlet__head-title">@lang('portal.add_new_consignee') <small> @lang('portal.form_text')</small></h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <a href="javascript:history.go(-1);" class="btn btn-clean kt-margin-r-10">
                                <i class="la la-arrow-left"></i>
                                <span class="kt-hidden-mobile">@lang('portal.back')</span>
                            </a>
                            <div class="btn-group">
                                <button type="button" class="btn btn-brand" onclick="document.getElementById('consignee_form').submit();">
                                    <i class="la la-check"></i>
                                    <span class="kt-hidden-mobile">@lang('portal.save')</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <form class="kt-form" id="consignee_form" action="{{ route('consignee.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-xl-2"></div>
                                <div class="col-xl-8">
                                    <div class="kt-section kt-section--first">
                                        <div class="kt-section__body">
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.consignee_name')</label>
                                                <div class="col-9">
                                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}">
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.phone')</label>
                                                <div class="col-3">
                                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}">
                                                    @error('phone')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.address')</label>
                                                <div class="col-9">
                                                    <input type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address') }}">
                                                    @error('address')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.auth_name')</label>
                                                <div class="col-9">
                                                    <input type="text" class="form-control @error('auth_name') is-invalid @enderror" name="auth_name" value="{{ old('auth_name') }}">
                                                    @error('auth_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.auth_phone')</label>
                                                <div class="col-3">
                                                    <input type="text" class="form-control @error('auth_phone') is-invalid @enderror" name="auth_phone" value="{{ old('auth_phone') }}">
                                                    @error('auth_phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.shipped_companies')</label>
                                                <div class="col-9">
                                                    <select name="companies[]" id="companies" multiple="multiple" class="form-control select @error('companies') is-invalid @enderror">
                                                        @foreach($companies as $key => $value)
                                                            <option value="{{ $value->id }}" {{ old('companies') && in_array($value->id, old('companies')) ? 'selected="selected"' : ''}}>{{ $value->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('companies')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.okuma_ekran')</label>
                                                <div class="col-9">
                                                    <select name="viewid" class="form-control @error('view') is-invalid @enderror">
                                                        <option value="viewid" selected>@lang('portal.choose')</option>
                                                        @foreach($viewScreen as $value)
                                                            <option value="{{ $value->id }}" >{{$value->name}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('statussayfa_gorunum')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            {{--
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.sayfa_gorunum')</label>
                                                <div class="col-9">
                                                    <select name="sayfa_gorunum" class="form-control @error('sayfa_gorunum') is-invalid @enderror">
                                                        <option value="" selected>@lang('portal.choose')</option>
                                                        @foreach(config('settings.form_static.sayfa_gorunum') as $key => $value)
                                                            <option value="{{ $key }}" {{ old('sayfa_gorunum') && old('statussayfa_gorunum') == $key ? 'selected' : '' }}>@lang($value)</option>
                                                        @endforeach
                                                    </select>
                                                    @error('statussayfa_gorunum')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div> --}}
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.status')</label>
                                                <div class="col-9">
                                                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                                                        <option value="" selected>@lang('portal.choose')</option>
                                                        @foreach(config('settings.form_static.status') as $key => $value)
                                                            <option value="{{ $key }}" {{ old('status') && old('status') == $key ? 'selected' : '' }}>@lang($value)</option>
                                                        @endforeach
                                                    </select>
                                                    @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.image')</label>
                                                <div class="col-9">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input @error('logo') is-invalid @enderror" name="logo" id="logo"  value="{{ old('logo') }}">
                                                        @error('logo')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        <label class="custom-file-label" for="logo">@lang('portal.choose_file')...</label>
                                                    </div>
                                                    <div class="mt-2">
                                                        <img id="logo_prev" src="" width="100" alt="">
                                                    </div>
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
        $(function() {
            $('input[type=file]').on('change', function (event) {
                $('#logo_prev').attr('src', URL.createObjectURL(event.target.files[0]));
            });
            $('.select').select2({
                minimumResultsForSearch: Infinity
            });
        });
    </script>
@endsection
