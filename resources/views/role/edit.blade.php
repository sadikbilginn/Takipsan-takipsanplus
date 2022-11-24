@extends('layouts.main')

@section('content')

<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

    <!-- begin:: Subheader -->
    <div class="kt-subheader kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">
                    @lang('portal.roles')
                </h3>
                <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                <div class="kt-subheader__group" id="kt_subheader_search">
                    <span class="kt-subheader__desc" id="kt_subheader_total">@lang('portal.edit_role')</span>
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
                            <h3 class="kt-portlet__head-title">@lang('portal.edit_role') <small> @lang('portal.form_text')</small></h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <a href="javascript:history.go(-1);" class="btn btn-clean kt-margin-r-10">
                                <i class="la la-arrow-left"></i>
                                <span class="kt-hidden-mobile">@lang('portal.back')</span>
                            </a>
                            <div class="btn-group">
                                <button type="button" class="btn btn-brand" onclick="document.getElementById('role_form').submit();">
                                    <i class="la la-check"></i>
                                    <span class="kt-hidden-mobile">@lang('portal.save_changes')</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <form class="kt-form" id="role_form" action="{{ route('role.update', $role->id) }}" method="post">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="row">
                                <div class="col-xl-2"></div>
                                <div class="col-xl-8">
                                    <div class="kt-section kt-section--first">
                                        <div class="kt-section__body">
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.name')</label>
                                                <div class="col-9">
                                                    <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $role->title) }}">
                                                    @error('title')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.custom_permissions')</label>
                                                <div class="col-9">
                                                    <select name="customPermission[]" id="customPermission" multiple="multiple" class="form-control select @error('customPermission') is-invalid @enderror">
                                                        @foreach($customPermission as $val)
                                                            <option value="{{ $val->id }}" {{ isset($selectedCustomPermission) && in_array($val->id, $selectedCustomPermission) ? 'selected="selected"' : ''}}>{{ $val->name }} </option>
                                                        @endforeach
                                                    </select>
                                                    @error('customPermission')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.permissions')</label>
                                                <div class="col-9">
                                                    <input type="hidden" id="permissions" name="permissions">
                                                    <div id="kt_tree_3" class="tree-demo"></div>
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

    <link href="/assets/vendors/custom/jstree/jstree.bundle.css" rel="stylesheet" type="text/css" />

@endsection

@section('js')
    <script src="/assets/vendors/custom/jstree/jstree.bundle.js" type="text/javascript"></script>
    <script>
        $(function() {
            $('#kt_tree_3').bind("changed.jstree", function (e, data) {
                $("#permissions").val(data.selected.join(','));
            });

            $('#kt_tree_3').jstree({
                'plugins': ["wholerow", "checkbox", "types"],
                'core': {
                    "themes" : {
                        "responsive": false
                    },
                    'data': [
                            @foreach($permissions as $key => $value)
                        {
                            "id": "{{ str_slug($key) }}",
                            "text": "{{ __('takipsan.'.Str::slug($key)) }}",
                            "state": {
                                "opened": true
                            },
                            "children": [
                                @foreach($value as $key2 => $value2)
                                    {
                                        "id": "{{ $value2->id }}",
                                        "text": "{{ $value2->title }}",
                                        "icon": "fa fa-file",
                                        @if(in_array($value2->id, $roleSelectPermission))
                                        'state' : {
                                            'selected' : true
                                        },
                                        @endif
                                    },
                                @endforeach
                            ]
                        },
                        @endforeach
                    ]
                },
                "types" : {
                    "default" : {
                        "icon" : "fa fa-folder kt-font-warning"
                    },
                    "file" : {
                        "icon" : "fa fa-file  kt-font-warning"
                    }
                },
            });
            $('#customPermission').select2();
        });
    </script>
@endsection
