@extends('layouts.main')

@section('content')
<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
    <!-- begin:: Subheader -->
    <div class="kt-subheader kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">
                    @lang('portal.views')
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
                                    onclick="document.getElementById('view_form').submit();"
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
                            id="view_form" 
                            action="{{ route('view_screen.update', $view->id) }}" 
                            method="post"
                        >
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="row">
                                <div class="col-xl-2"></div>
                                <div class="col-xl-8">
                                    <div class="kt-section kt-section--first">
                                        <div class="kt-section__body">
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.viewname')</label>
                                                <div class="col-9">
                                                    <input 
                                                        type="text" 
                                                        class="form-control @error('name') is-invalid @enderror" 
                                                        name="name" 
                                                        value="{{ old('name', $view->name) }}"
                                                    >
                                                    @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.viewreading')</label>
                                                <div class="col-9">
                                                    <input 
                                                        type="text" 
                                                        class="form-control @error('reading') is-invalid @enderror" 
                                                        name="reading" 
                                                        value="{{ old('reading', $view->reading) }}"
                                                    >
                                                    @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.status') </label>
                                                <div class="col-9">
                                                    <select 
                                                        name="status" 
                                                        id="status" 
                                                        class="form-control select @error('status') is-invalid @enderror"
                                                    >
                                                        <option 
                                                            value="Default" 
                                                            {{ $view->status == 'Default' ? 'selected="selected"' : '' }}"
                                                        >
                                                            Default
                                                        </option>
                                                        <option 
                                                            value="Other" 
                                                            {{ $view->status == 'Other' ? 'selected="selected"' : '' }}"
                                                        >
                                                            Other
                                                        </option>
                                                    </select>
                                                    @error('roles')
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