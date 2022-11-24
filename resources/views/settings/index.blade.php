@extends('layouts.main')

@section('content')

<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

    <!-- begin:: Subheader -->
    <div class="kt-subheader kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">
                @lang('portal.settings')
                </h3>
                <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                <div class="kt-subheader__group" id="kt_subheader_search">
                    <span class="kt-subheader__desc" id="kt_subheader_total">@lang('portal.settings_list')</span>
                </div>
            </div>
            <div class="kt-subheader__toolbar">
                <a href="{{ route('settings.create') }}" class="btn btn-label-brand btn-bold"><i class="la la-plus"></i> @lang('portal.add_new_setting')</a>
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
                            <h3 class="kt-portlet__head-title">Ayar Listesi <small> aşağıdaki alanları doldurunuz</small></h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <a href="javascript:history.go(-1);" class="btn btn-clean kt-margin-r-10">
                                <i class="la la-arrow-left"></i>
                                <span class="kt-hidden-mobile">Geri</span>
                            </a>
                            <div class="btn-group">
                                <button type="button" class="btn btn-brand" onclick="document.getElementById('settings_form').submit();">
                                    <i class="la la-check"></i>
                                    <span class="kt-hidden-mobile">Değişiklikleri Kaydet</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <ul class="nav nav-tabs  nav-tabs-line nav-tabs-line-2x nav-tabs-line-success" role="tablist">
                            @foreach(config('settings.group_key') as $key => $value)
                                <li class="nav-item"><a href="#{{ $key }}" data-toggle="tab" class="nav-link @if ($loop->first) active @endif" role="tab">{{ $value }}</a></li>
                            @endforeach
                        </ul>
                        <form action="{{ route('settings.updateAll') }}" id="settings_form" enctype="multipart/form-data" method="post">
                            @csrf
                            <div class="tab-content">
                                @foreach(config('settings.group_key') as $opt_key => $opt_value)
                                    <div class="tab-pane @if ($loop->first) active @endif" id="{{ $opt_key }}" role="tabpanel">
                                        @foreach ($settings[$opt_key] as $key => $value)
                                            <div class="form-group">
                                                <label for="{{$value->key}}">{{$value->title}}</label>
                                                @if ($value->locale == 1)
                                                    @php $arr_tmp = json_decode($value->value, TRUE) @endphp
                                                    @foreach ($glb_locales as $key2 => $value2)
                                                        <div class="input-group mb-1">
                                                            @if ($value->area_type == 'input')
                                                                <input type="text" name="{{$value->key.'['.$value2->abbr.']'}}" id="{{ $value->key.'_'.$value2->abbr }}" class="form-control {{ ($value->required) ? 'required' : '' }}" value="{{ isset($arr_tmp[ $value2->abbr ]) ? $arr_tmp[ $value2->abbr ] : '' }}" placeholder="{{$value->description}} {{ $value2->title }}" />
                                                            @endif
                                                            @if ($value->area_type == 'textarea')
                                                                <textarea  name="{{ $value->key.'['.$value2->abbr.']' }}" id="{{ $value->key.'_'.$value2->abbr }}" cols="30" rows="3" class="form-control {{ ($value->required) ? 'required' : '' }}" placeholder="{{$value->description}} {{ $value2->title }}">{{ isset($arr_tmp[ $value2->abbr ]) ? $arr_tmp[ $value2->abbr ] : '' }}</textarea>
                                                            @endif
                                                            @if ($value->area_type == 'file')
                                                                @if(isset($arr_tmp[ $value2->abbr ]))
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text">
                                                                            @php $slip = explode('.',$arr_tmp[ $value2->abbr ]); $ex = end($slip); @endphp
                                                                            @if(in_array($ex, config('settings.file_type_image')))
                                                                                <img src="{{ $arr_tmp[ $value2->abbr ]  == '' ? asset('images/logo.png') : asset('upload/images/') . '/' .$arr_tmp[ $value2->abbr ] }}" height="15" alt="">
                                                                            @else
                                                                                <a href="{{ $arr_tmp[ $value2->abbr ] == '' ? 'javascript:;' : asset('upload/files/') . '/' .$arr_tmp[ $value2->abbr ] }}"><i class="fa fa-eye"></i> Göster </a>
                                                                            @endif
                                                                        </span>
                                                                    </div>
                                                                @endif
                                                                <div class="custom-file">
                                                                    <input type="file" class="custom-file-input" name="FILE[{{$value->key}}][{{$value2->abbr}}]" id="{{ $value->key.'_'.$value2->abbr }}">
                                                                    <label class="custom-file-label" for="{{ $value->key.'_'.$value2->abbr }}">Dosya Seçiniz. ( {{ $value2->title }} )</label>
                                                                </div>
                                                            @endif
                                                            <span class="input-group-append">
                                                                <a href="{{ route('settings.destroy', $value->id) }}" title="Sil" data-method="delete" data-token="{{csrf_token()}}" data-confirm="Kaydı silmek istediğinize emin misiniz?" class="btn btn-danger btn-icon"><i class="la la-close"></i></a>
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="input-group">
                                                        @if ($value->area_type == 'input')
                                                            <input type="text" name="{{$value->key}}" id="{{$value->key}}" value="{{$value->value}}" class="form-control {{($value->required) ? 'required' : ''}}" placeholder="{{$value->description}}" />
                                                        @endif
                                                        @if ($value->area_type == 'textarea')
                                                            <textarea name="{{$value->key}}" id="{{$value->key}}" cols="30" rows="3" class="form-control {{ ($value->required) ? 'required' : '' }}" placeholder="{{$value->description}}">{{$value->value}}</textarea>
                                                        @endif
                                                        @if ($value->area_type == 'file')
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text">
                                                                    @php $slip = explode('.',$value->value ); $ex = end($slip); @endphp
                                                                    @if(in_array($ex, config('settings.file_type_image')))
                                                                        <img src="{{ $value->value  == '' ? asset('images/logo.png') : asset('upload/images/') . '/' .$value->value }}" width="15" alt="">
                                                                    @else
                                                                        <a href="{{ $value->value  == '' ? 'javascript:;' : asset('upload/files/') . '/' .$value->value }}">{{$value->value}}</a>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="custom-file">
                                                                <input type="file" class="custom-file-input" name="FILE[{{$value->key}}]" id="{{$value->key}}">
                                                                <label class="custom-file-label" for="{{$value->key}}">Dosya Seçiniz.</label>
                                                            </div>
                                                        @endif
                                                        <span class="input-group-append">
                                                            <a href="{{ route('settings.destroy', $value->id) }}" title="Sil" data-method="delete" data-token="{{csrf_token()}}" data-confirm="Kaydı silmek istediğinize emin misiniz?" class="btn btn-danger btn-icon"><i class="la la-close"></i></a>
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
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
