@extends('layouts.main')

@section('content')

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

<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

    <!-- begin:: Subheader -->
    <div class="kt-subheader kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">
                    @lang('portal.companies')
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
                                <button type="button" class="btn btn-brand" onclick="document.getElementById('company_form').submit();">
                                    <i class="la la-check"></i>
                                    <span class="kt-hidden-mobile">@lang('portal.save_changes')</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <form class="kt-form" id="company_form" action="{{ route('company.update', $company->id) }}" method="post" enctype="multipart/form-data">
                            @csrf 
                            {{ method_field('PATCH') }}
                            <div class="row">
                            <div class="col-xl-2"></div>
                                <div class="col-xl-8">
                                    <div class="kt-section kt-section--first">
                                        <div class="kt-section__body">
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.company_name')</label>
                                                <div class="col-9">
                                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $company->name) }}">
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            {{--<div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.company_title')</label>
                                                <div class="col-9">
                                                    <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $company->title) }}">
                                                    @error('title')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>--}}
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.phone')</label>
                                                <div class="col-9">
                                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $company->phone) }}">
                                                    @error('phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.email')</label>
                                                <div class="col-9">
                                                    <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $company->email) }}">
                                                    @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            {{--<div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.address')</label>
                                                <div class="col-9">
                                                    <input type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address', $company->address) }}">
                                                    @error('address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.coordinate')</label>
                                                <div class="col-9">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <input type="text" class="form-control @error('latitude') is-invalid @enderror" name="latitude" placeholder="Latitude" value="{{ old('latitude', $company->latitude) }}">
                                                            @error('latitude')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-6">
                                                            <input type="text" class="form-control @error('longitude') is-invalid @enderror" name="longitude" placeholder="Longitude" value="{{ old('longitude', $company->longitude) }}">
                                                            @error('longitude')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>--}}
                                            {{--<div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.status')</label>
                                                <div class="col-9">
                                                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                                                        <option value="" selected>@lang('portal.choose')</option>
                                                        @foreach(config('settings.form_static.status') as $key => $value)
                                                            <option value="{{ $key }}" {{ old('status', $company->status) == $key ? 'selected' : '' }}>@lang($value)</option>
                                                        @endforeach
                                                    </select>
                                                    @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>--}}
                                            {{--<div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.auth_close_consignment')</label>
                                                <div class="col-3">
                                                    <span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--success">
                                                        <label>
                                                            <input type="checkbox" name="consignment_close" {{ $company->consignment_close == true ? 'checked': '' }}>
                                                            <span></span>
                                                        </label>
                                                    </span>
                                                </div>
                                            </div>--}}

                                            {{--<div class="form-group row">
                                                    <label class="col-3 col-form-label">@lang('portal.role')</label>
                                                    <div class="col-9">
                                                        <select 
                                                            name="roles" 
                                                            id="roles" 
                                                            class="
                                                                form-control 
                                                                select 
                                                                @error('roles') is-invalid @enderror
                                                            "
                                                        >
                                                            @foreach($company->userRole as $val)
                                                            <option value="{{ $val->id }}"
                                                            
                                                            {{ 
                                                                    
                                                                    in_array($val->id, explode(',',$company->userRoleId)) ? 
                                                                        'selected="selected"' : ''
                                                            }}
                                                            
                                                            >{{ $val->title }} </option>
                                                            @endforeach
                                                        </select>
                                                        @error('roles')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                            </div>
                                             
                                            <div class="form-group row">
                                                    <label class="col-3 col-form-label">Kime Bağlı</label>
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
                                                            @foreach($company->company as $value)
                                                            <option value="{{ $value->id }}"
                                                            
                                                            {{ 
                                                                    in_array($value->id, explode(',',$company->main_company_id)) ? 
                                                                        'selected="selected"' : ''
                                                            }}
                                                            
                                                            >{{ $value->name }} </option>
                                                            @endforeach
                                                        </select>
                                                        @error('company_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            --}}
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.image')</label>
                                                <div class="col-9">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input @error('logo') is-invalid @enderror" name="logo" id="logo">
                                                        @error('logo')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        <label class="custom-file-label" for="logo">@lang('portal.choose_file')...</label>
                                                    </div>

                                                    <div class="mt-2">
                                                        <img id="logo_prev" src="{{ config('settings.media.companies.full_path') . $company->logo }}" width="100" alt="">
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
        });
    </script>
@endsection
