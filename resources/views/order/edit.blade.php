@extends('layouts.main')

@section('content')
    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

        <!-- begin:: Subheader -->
        <div class="kt-subheader kt-grid__item" id="kt_subheader">
            <div class="kt-container  kt-container--fluid ">
                <div class="kt-subheader__main">
                    <h3 class="kt-subheader__title">
                        @lang('portal.orders')
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
                    <div class="kt-portlet kt-portlet--last kt-portlet--head-lg kt-portlet--responsive-mobile"
                         id="kt_page_portlet">
                        <div class="kt-portlet__head kt-portlet__head--lg">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">@lang('portal.edit') <small> @lang('portal.form_text')</small></h3>
                            </div>
                            <div class="kt-portlet__head-toolbar">
                                <a href="javascript:history.go(-1);" class="btn btn-clean kt-margin-r-10">
                                    <i class="la la-arrow-left"></i>
                                    <span class="kt-hidden-mobile">@lang('portal.back')</span>
                                </a>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-brand" onclick="document.getElementById('order_form').submit();">
                                        <i class="la la-check"></i>
                                        <span class="kt-hidden-mobile">@lang('portal.save_changes')</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <form class="kt-form order_form" id="order_form" action="{{ route('order.update',$order->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                {{ method_field('PUT') }}
                                <div class="row">
                                    <div class="col-xl-2"></div>
                                    <div class="col-xl-8">
                                        <div class="kt-section kt-section--first">
                                            <div class="kt-section__body">
                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label">@lang('portal.po_no')</label>
                                                    <div class="col-9">
                                                        <input type="text" class="form-control @error('po_no') is-invalid @enderror" {{ $order->consignments ? 'disabled' : '' }} id="po_no" name="po_no" value="{{ old('po_no', $order->po_no) }}">
                                                        @error('po_no')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label">@lang('portal.model_name')</label>
                                                    <div class="col-9">
                                                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $order->name) }}">
                                                        @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label">@lang('portal.piece')</label>
                                                    <div class="col-9">
                                                        <input type="number" class="form-control @error('item_count') is-invalid @enderror" name="item_count" value="{{ old('item_count', $order->item_count) }}">
                                                        @error('item_count')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label">@lang('portal.consignee_name')</label>
                                                    <div class="col-9">
                                                        <select name="consignee_id" id="consignee_id" class="form-control @error('consignee_id') is-invalid @enderror">
                                                            <option value="" selected>@lang('portal.choose')</option>
                                                            @foreach($consignees as $key => $value)
                                                                <option value="{{ $value->id }}" {{ $value->id == old('consignee_id', $order->consignee_id) ? 'selected' : '' }}>{{ $value->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('consignee_id')
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

@section('js')

@endsection
