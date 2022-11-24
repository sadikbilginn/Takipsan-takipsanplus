@extends('layouts.main')

@section('content')

<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

    <!-- begin:: Subheader -->
    <div class="kt-subheader kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">
                    @lang('portal.menus')
                </h3>
                <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                <div class="kt-subheader__group" id="kt_subheader_search">
                    <span class="kt-subheader__desc" id="kt_subheader_total">@lang('portal.edit_menu')</span>
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
                            <h3 class="kt-portlet__head-title">@lang('portal.edit_menu') <small> @lang('portal.form_text')</small></h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <a href="javascript:history.go(-1);" class="btn btn-clean kt-margin-r-10">
                                <i class="la la-arrow-left"></i>
                                <span class="kt-hidden-mobile">@lang('portal.back')</span>
                            </a>
                            <div class="btn-group">
                                <button type="button" class="btn btn-brand" onclick="document.getElementById('menu_form').submit();">
                                    <i class="la la-check"></i>
                                    <span class="kt-hidden-mobile">@lang('portal.save_changes')</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <form class="kt-form" id="menu_form" action="{{ route('menu.update', $menu->id) }}" method="post">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="row">
                                <div class="col-xl-2"></div>
                                <div class="col-xl-8">
                                    <div class="kt-section kt-section--first">
                                        <div class="kt-section__body">
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.top_menu')</label>
                                                <div class="col-9">
                                                    <select name="parent_id" id="parent_id" class="form-control select @error('parent_id') is-invalid @enderror">
                                                        <option value="0">@lang('portal.main_menu')</option>
                                                        @foreach($menus as $key => $value)
                                                            <option value="{{ $value->id }}" {{ old('parent_id', $menu->parent_id) == $value->id ? 'selected' : '' }}> {{ $value->title }} - {{ $value->title_en }} </option>
                                                            @if(isset($value->children) && count($value->children) > 0)
                                                                @foreach($value->children as $key2 => $value2)
                                                                    <option value="{{ $value2->id }}" {{ old('parent_id', $menu->parent_id) == $value2->id ? 'selected' : '' }}> {{ $value2->title }} - {{ $value2->title_en }} </option>
                                                                    @if(isset($value2->children) && count($value2->children) > 0)
                                                                        @foreach($value2->children as $key3 => $value3)
                                                                            <option value="{{ $value3->id }}" {{ old('parent_id', $menu->parent_id) == $value3->id ? 'selected' : '' }}> {{ $value3->title }} - {{ $value3->title_en }} </option>
                                                                        @endforeach
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    @error('parent_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.icon')</label>
                                                <div class="col-9">
                                                    <select name="icon" id="icon" data-placeholder="Seçiniz..." class="select-icons form-control @error('icon') is-invalid @enderror">
                                                        @foreach(Config::get('settings.fa_icon') as $key => $value)
                                                            <option value="{{ $key }}" data-icon="{{ $key }}"  {{ old('icon', $menu->icon) == $key ? 'selected' : '' }}>{{ $value }} </option>
                                                        @endforeach
                                                    </select>
                                                    @error('icon')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.menu_name')</label>
                                                <div class="col-9">
                                                    <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $menu->title) }}">
                                                    @error('title')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.menu_name') En</label>
                                                <div class="col-9">
                                                    <input type="text" class="form-control @error('title_en') is-invalid @enderror" name="title_en" value="{{ old('title_en', $menu->title_en) }}">
                                                    @error('title_en')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">Link</label>
                                                <div class="col-9">
                                                    <input type="text" class="form-control @error('link') is-invalid @enderror" name="link" value="{{ old('link', $menu->uri) }}">
                                                    @error('link')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.role')</label>
                                                <div class="col-9">
                                                    <select name="roles[]" id="roles" multiple="multiple" class="form-control select @error('roles') is-invalid @enderror">
                                                        @foreach($roles as $val)
                                                            <option value="{{ $val->id }}" {{ in_array($val->id, $menuSelectRoles)  ? 'selected' : '' }}>{{ $val->title }} </option>
                                                        @endforeach
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

@section('js')
    <script>
        $(function() {
            function iconFormat(icon) {
                var originalOption = icon.element;
                if (!icon.id) { return icon.text; }
                var $icon = "<i class='" + $(icon.element).data('icon') + "'></i> " + icon.text;

                return $icon;
            }

            $(".select-icons").select2({
                templateResult: iconFormat,
                templateSelection: iconFormat,
                escapeMarkup: function(m) { return m; }
            });

            $('.select').select2({
                minimumResultsForSearch: Infinity
            });
        });
    </script>
@endsection
