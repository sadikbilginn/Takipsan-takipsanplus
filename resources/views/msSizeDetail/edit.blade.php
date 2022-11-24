@extends('layouts.main')
    @section('content')

    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

        <!-- begin:: Subheader -->
        <div class="kt-subheader kt-grid__item" id="kt_subheader">
            <div class="kt-container  kt-container--fluid ">
                <div class="kt-subheader__main">
                    <h3 class="kt-subheader__title">
                        @lang('portal.msSizeDetail')
                    </h3>
                    <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                    <div class="kt-subheader__group" id="kt_subheader_search">
                        <span class="kt-subheader__desc" id="kt_subheader_total">@lang('portal.add')</span>
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
                                        onclick="document.getElementById('ms_size_form').submit();"
                                    >
                                        <i class="la la-check"></i>
                                        <span class="kt-hidden-mobile">@lang('portal.save')</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <form class="kt-form" id="ms_size_form" action="{{ route('ms_size_detail.update', $consignmentsDetail->id) }}" method="post">
                                @csrf
                                {{ method_field('PATCH') }}
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
                                                            @foreach($consignments as $value)
                                                            <option
                                                                {{ $consignmentsDetail->consignment_id == $value->id ? 'selected="selected"' : '' }} 
                                                                value="{{ $value->id }}"
                                                            >
                                                                {{ $value->name }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                        @error('consignment_id')
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
                                                            value="{{ old('order', $consignmentsDetail->order) }}"
                                                        >
                                                        @error('order')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label">Season</label>
                                                    <div class="col-9">
                                                        <input 
                                                            type="text" 
                                                            class="form-control @error('season') is-invalid @enderror" 
                                                            name="season" 
                                                            value="{{ old('season', $consignmentsDetail->season) }}"
                                                        >
                                                        @error('season')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label">@lang('portal.name')</label>
                                                    <div class="col-9">
                                                        <input 
                                                            type="text" 
                                                            class="form-control @error('name') is-invalid @enderror" 
                                                            name="description" 
                                                            value="{{ old('season', $consignmentsDetail->description) }}"
                                                        >
                                                        @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label">Primary Size</label>
                                                    <div class="col-9">
                                                        <input 
                                                            type="text" 
                                                            class="form-control @error('primary_size') is-invalid @enderror" 
                                                            name="primary_size" 
                                                            value="{{ old('secondary_size') }}"
                                                        >
                                                        @error('primary_size')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label">Secondary Size</label>
                                                    <div class="col-9">
                                                        <input 
                                                            type="text" 
                                                            class="form-control @error('secondary_size') is-invalid @enderror" 
                                                            name="secondary_size" 
                                                            value="{{ old('secondary_size') }}"
                                                        >
                                                        @error('secondary_size')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label">UPC No</label>
                                                    <div class="col-9">
                                                        <input 
                                                            type="text" 
                                                            class="form-control @error('upc') is-invalid @enderror" 
                                                            name="upc" 
                                                            value="{{ old('upc', $consignmentsDetail->upc) }}"
                                                        >
                                                        @error('upc')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label">Story Description</label>
                                                    <div class="col-9">
                                                        <input 
                                                            type="text" 
                                                            class="form-control @error('story_description') is-invalid @enderror" 
                                                            name="story_description" 
                                                            value="{{ old('story_description', $consignmentsDetail->story_description) }}"
                                                        >
                                                        @error('story_description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label">Actual Selling Price</label>
                                                    <div class="col-9">
                                                        <input 
                                                            type="text" 
                                                            class="form-control @error('price') is-invalid @enderror" 
                                                            name="price" 
                                                            value="{{ old('price', $consignmentsDetail->price) }}"
                                                        >
                                                        @error('price')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label">Qty Req</label>
                                                    <div class="col-9">
                                                        <input 
                                                            type="text" 
                                                            class="form-control @error('qty_req') is-invalid @enderror" 
                                                            name="qty_req" 
                                                            value="{{ old('qty_req', $consignmentsDetail->qty_req) }}"
                                                        >
                                                        @error('qty_req')
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