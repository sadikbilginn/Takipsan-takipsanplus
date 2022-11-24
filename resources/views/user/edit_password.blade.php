@extends('layouts.main')
    @section('content')
    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
        <!-- begin:: Subheader -->
        <div class="kt-subheader kt-grid__item" id="kt_subheader">
            <div class="kt-container  kt-container--fluid ">
                <div class="kt-subheader__main">
                    <h3 class="kt-subheader__title">
                        @lang('portal.profile')
                    </h3>
                    <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                    <div class="kt-subheader__group" id="kt_subheader_search">
                        <span class="kt-subheader__desc" id="kt_subheader_total">
                            @lang('portal.profile_information')
                        </span>
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
                                    @lang('portal.edit')<small> @lang('portal.form_text')</small>
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
                                        onclick="document.getElementById('profile_form').submit();"
                                    >
                                        <i class="la la-check"></i>
                                        <span class="kt-hidden-mobile">@lang('portal.save_changes')</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <form 
                                class="kt-form" 
                                id="profile_form" 
                                action="{{ route('profile.update_password', $user->id) }}" 
                                method="POST"
                            >
                                @csrf
                                {{ method_field('PATCH') }}
                                <div class="row">
                                    <div class="col-xl-2"></div>
                                    <div class="col-xl-8">
                                        <div class="kt-section kt-section--first">
                                            <div class="kt-section__body">
                                            <div class="form-group row">
                                                    <label class="col-3 col-form-label">@lang('portal.old_password')</label>
                                                    <div class="col-9">
                                                        <input 
                                                            type="password" 
                                                            class="
                                                                form-control 
                                                                @error('old_password') is-invalid @enderror" 
                                                                name="old_password"
                                                                required 
                                                            >
                                                        @error('old_password')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label">@lang('portal.new_password')</label>
                                                    <div class="col-9">
                                                        <input 
                                                            type="password" 
                                                            class="
                                                                form-control 
                                                                @error('password') is-invalid @enderror" 
                                                                name="password" 
                                                                value="{{ old('password') }}" 
                                                                required 
                                                                autocomplete="new-password"
                                                            >
                                                        @error('password')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label">
                                                        @lang('portal.confirm_password')
                                                    </label>
                                                    <div class="col-9">
                                                        <input 
                                                            type="password" 
                                                            class="
                                                                form-control 
                                                                @error('password_confirmation') is-invalid @enderror
                                                            " 
                                                            name="password_confirmation" 
                                                            value="{{ old('password_confirmation') }}" 
                                                            required 
                                                            autocomplete="new-password"
                                                        >
                                                        @error('password_confirmation')
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
    
    @section('css')
    @endsection
    
    @section('js')
    @endsection
