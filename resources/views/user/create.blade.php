@extends('layouts.main')
    @section('content')
    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
        <!-- begin:: Subheader -->
        <div class="kt-subheader kt-grid__item" id="kt_subheader">
            <div class="kt-container  kt-container--fluid ">
                <div class="kt-subheader__main">
                    <h3 class="kt-subheader__title">
                        @lang('portal.users')
                    </h3>
                    <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                    <div class="kt-subheader__group" id="kt_subheader_search">
                        <span class="kt-subheader__desc" id="kt_subheader_total">@lang('portal.add_new_user')</span>
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
                                    @lang('portal.add_new_user') <small> @lang('portal.form_text')</small>
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
                                        onclick="document.getElementById('user_form').submit();"
                                    >
                                        <i class="la la-check"></i>
                                        <span class="kt-hidden-mobile">@lang('portal.save')</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <form class="kt-form" id="user_form" action="{{ route('user.store') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-xl-2"></div>
                                    <div class="col-xl-8">
                                        <div class="kt-section kt-section--first">
                                            <div class="kt-section__body">
                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label">@lang('portal.username')</label>
                                                    <div class="col-9">
                                                        <input 
                                                            type="text" 
                                                            class="form-control @error('username') is-invalid @enderror" 
                                                            name="username" 
                                                            value="{{ old('username') }}"
                                                        >
                                                        @error('username')
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
                                                            name="name" 
                                                            value="{{ old('name') }}"
                                                        >
                                                        @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label">@lang('portal.email')</label>
                                                    <div class="col-9">
                                                        <input 
                                                            type="email" 
                                                            class="form-control @error('email') is-invalid @enderror" 
                                                            name="email" 
                                                            value="{{ old('email') }}"
                                                        >
                                                        @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label">Şifre</label>
                                                    <div class="col-9">
                                                        <input 
                                                            type="password" 
                                                            class="form-control @error('password') is-invalid @enderror" 
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
                                                    <label class="col-3 col-form-label">Şifre Tekrar</label>
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
                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label">@lang('portal.role')</label>
                                                    <div class="col-9">
                                                        <select 
                                                            name="roles[]" 
                                                            id="roles" 
                                                            multiple="multiple" 
                                                            class="
                                                                form-control 
                                                                select 
                                                                @error('roles') is-invalid @enderror
                                                            "
                                                        >
                                                            @foreach($roles as $val)
                                                            <option value="{{ $val->id }}">{{ $val->title }} </option>
                                                            @endforeach
                                                        </select>
                                                        @error('roles')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                @if(roleCheck(config('settings.roles.admin')))
                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label">
                                                        @lang('portal.custom_permissions')
                                                    </label>
                                                    <div class="col-9">
                                                        <select 
                                                            name="customPermission[]" 
                                                            id="customPermission" 
                                                            multiple="multiple" 
                                                            class="
                                                                form-control 
                                                                select 
                                                                @error('customPermission') is-invalid @enderror
                                                            "
                                                        >
                                                            @foreach($customPermission as $val)
                                                            <option value="{{ $val->id }}">{{ $val->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('customPermission')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                @endif
                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label">@lang('portal.company')</label>
                                                    <div class="col-9">
                                                        <select 
                                                            name="company_id" 
                                                            id="company_id" 
                                                            class="
                                                                form-control 
                                                                select2 
                                                                @error('company_id') is-invalid @enderror
                                                            " 
                                                            style="width: 100%"
                                                        >
                                                            <option value="" selected>@lang('portal.choose')</option>
                                                            @foreach($company as $value)
                                                            <option value="{{ $value->id }}">{{ $value->name }} </option>
                                                            @endforeach
                                                        </select>
                                                        @error('company_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                @if(roleCheck(config('settings.roles.admin')))
                                                <div class="form-group row">
                                                    <label class="col-3 col-form-label"d>@lang('portal.permissions')</label>
                                                    <div class="col-9">
                                                        <input type="hidden" id="permissions" name="permissions">
                                                        <div 
                                                            class="select_all btn btn-primary" 
                                                            style="
                                                                display:inline-block; 
                                                                margin:0 5px; 
                                                                cursor:pointer; 
                                                                float:right;
                                                            "
                                                        >
                                                            @lang('portal.selectAll')
                                                        </div>
                                                        <div 
                                                            class="deselect_all btn btn-primary" 
                                                            style="
                                                                display:inline-block; 
                                                                margin:0 5px; 
                                                                cursor:pointer; 
                                                                float:right;
                                                            "
                                                        >
                                                            @lang('portal.deSelectAll')
                                                        </div>
                                                        <div id="kt_tree_3" class="tree-demo"></div>
                                                    </div>
                                                </div>
                                                @endif
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

            $('.select2').select2();
            $('.select').select2({
                minimumResultsForSearch: Infinity
            });
            
            @if(roleCheck(config('settings.roles.admin'))) 

            $('#kt_tree_3').bind("changed.jstree", function (e, data) {
                $("#permissions").val(data.selected.join(','));
            });
            
            $('#kt_tree_3').jstree({
                'plugins' : ["wholerow", "checkbox", "types"],
                'core' : {
                    "themes" : {
                        "responsive": false
                    },
                    'data' : [
                        @foreach($permissions as $key => $value){
                            "id" : "{{ Str::slug($key) }}",
                            "text" : "{{ __('takipsan.'.Str::slug($key)) }}",
                            "state" : {
                                "opened" : true
                            },
                            "children" : [
                                @foreach($value as $key2 => $value2){
                                    "id" : "{{ $value2->id }}",
                                    "text" : "{{ $value2->title }}",
                                    "icon" : "fa fa-file",
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

            @endif
            
            $('#roles').change(function(){
                let value = $(this).val();
                //1 admin
                if (value.indexOf("1") >= 0){
                    $('.select_all').click();
                }else{
                    $('.deselect_all').click();
                }
            });

            $('.select_all').click(function(){
                $("#kt_tree_3").jstree("check_all");
            });
            
            $('.deselect_all').click(function(){
                $('#kt_tree_3').jstree("deselect_all");
            });

        });

    </script>
    @endsection

