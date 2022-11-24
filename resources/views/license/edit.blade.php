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
                    <span class="kt-subheader__desc" id="kt_subheader_total">Lisans Düzenle</span>
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
                            <h3 class="kt-portlet__head-title">Lisans Düzenle <small> aşağıdaki alanları doldurunuz</small></h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <a href="javascript:history.go(-1);" class="btn btn-clean kt-margin-r-10">
                                <i class="la la-arrow-left"></i>
                                <span class="kt-hidden-mobile">Geri</span>
                            </a>
                            <div class="btn-group">
                                <button type="button" class="btn btn-brand" onclick="document.getElementById('license_form').submit();">
                                    <i class="la la-check"></i>
                                    <span class="kt-hidden-mobile">Değişiklikleri Kaydet</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <form class="kt-form" id="license_form" action="{{ route('license.update', $license->id) }}" method="post">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="row">
                                <div class="col-xl-2"></div>
                                <div class="col-xl-8">
                                    <div class="kt-section kt-section--first">
                                        <div class="kt-section__body">
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">Lisans Sahibi</label>
                                                <div class="col-9">
                                                    <select name="company_id" id="company_id" class="form-control select @error('company_id') is-invalid @enderror">
                                                        <option value="{{ $company->id }}"> {{ $company->name }}</option>
                                                        {{-- <option value="" selected>@lang('portal.choose')</option>
                                                        @if(isset($companies))
                                                            @foreach($companies as $key => $value)
                                                                <option value="{{ $value->id }}" {{ $value->id == old('company_id', $license->company_id) ? 'selected' : '' }}> {{ $value->name }}</option>
                                                            @endforeach
                                                        @endif --}}
                                                    </select>
                                                    @error('company_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <!--<div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.manufacturer')</label>
                                                <div class="col-9">
                                                    <select name="manufacturer_id" id="manufacturer_id" class="form-control select @error('manufacturer_id') is-invalid @enderror">
                                                        @if(isset($manufacturer))
                                                            <option value="{{ $manufacturer->id }}"> {{ $manufacturer->name }}</option>
                                                        @endif
                                                    </select>
                                                    @error('manufacturer_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>-->
                                            <!--<div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.license_type')</label>
                                                <div class="col-9">
                                                    <select name="license_type" id="license_type" class="form-control select @error('license_type') is-invalid @enderror">
                                                            <option value="Lite" @if($license->license_type == 'Lite') selected @endif>Lite</option>
                                                            <option value="Pro" @if($license->license_type == 'Pro') selected @endif>Pro</option>
                                                    </select>
                                                    @error('license_type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>-->
                                            {{-- <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.user')</label>
                                                <div class="col-9">
                                                    <select name="user_id" id="user_id" class="form-control select @error('user_id') is-invalid @enderror">
                                                        @if(isset($user))
                                                            <option value="{{ $user->id }}"> {{ $user->name }}</option>
                                                        @endif
                                                    </select>
                                                    @error('user_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div> --}}
                                            {{-- <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.license_period') / {{ $license->finish_date }}</label>
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
                                                    <input type="date" class="form-control @error('start_at') is-invalid @enderror" name="start_at" value="{{ $license->start_at }}">
                                                    @error('start_at')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.license_finish')</label>
                                                <div class="col-9">
                                                    <input type="date" class="form-control @error('finish_at') is-invalid @enderror" name="finish_at" value="{{ $license->finish_at }}">
                                                    @error('finish_at')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">Durumu</label>
                                                <div class="col-9">
                                                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                                                        <option value="" selected>Seçiniz</option>
                                                        <option value="1" {{ old('status', $license->status)  == 1 ? 'selected' : '' }}>Aktif</option>
                                                        <option value="0" {{ old('status', $license->status) == 0 ? 'selected' : '' }}>Pasif</option>
                                                    </select>
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
