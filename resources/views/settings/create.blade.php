@extends('layouts.main')

@section('content')

<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

    <!-- begin:: Subheader -->
    <div class="kt-subheader kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">
                    Ayarlar
                </h3>
                <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                <div class="kt-subheader__group" id="kt_subheader_search">
                    <span class="kt-subheader__desc" id="kt_subheader_total">Yeni Ayar Ekle</span>
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
                            <h3 class="kt-portlet__head-title">Yeni Ayar Ekle <small> aşağıdaki alanları doldurunuz</small></h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <a href="javascript:history.go(-1);" class="btn btn-clean kt-margin-r-10">
                                <i class="la la-arrow-left"></i>
                                <span class="kt-hidden-mobile">Geri</span>
                            </a>
                            <div class="btn-group">
                                <button type="button" class="btn btn-brand" onclick="document.getElementById('settings_form').submit();">
                                    <i class="la la-check"></i>
                                    <span class="kt-hidden-mobile">Kaydet</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <form class="kt-form" id="settings_form" action="{{ route('settings.store') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-xl-2"></div>
                                <div class="col-xl-8">
                                    <div class="kt-section kt-section--first">
                                        <div class="kt-section__body">
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">Grup Başlığı</label>
                                                <div class="col-9">
                                                    <select name="group_key" class="form-control @error('group_key') is-invalid @enderror">
                                                        <option value="">Seçiniz</option>
                                                        @foreach(config('settings.group_key') as $key => $value)
                                                            <option value="{{ $key }}"  {{ old('group_key') == $key ? 'selected' : '' }}>{{ $value }} </option>
                                                        @endforeach
                                                    </select>
                                                    @error('group_key')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">Alan Tipi</label>
                                                <div class="col-9">
                                                    <select name="area_type" id="area_type" class="form-control @error('area_type') is-invalid @enderror">
                                                        <option value="">Seçiniz</option>
                                                        @foreach(config('settings.area_type') as $key => $value)
                                                            <option value="{{ $key }}"  {{ old('area_type') == $key ? 'selected' : '' }}>{{ $value }} </option>
                                                        @endforeach
                                                    </select>
                                                    @error('area_type')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">Zorunlu Alan?</label>
                                                <div class="col-9">
                                                    <div class="kt-radio-inline">
                                                        <label class="kt-radio">
                                                            <input type="radio" name="required" value="0" {{ old('required') == 0 ? 'checked' : '' }}> Hayır
                                                            <span></span>
                                                        </label>
                                                        <label class="kt-radio">
                                                            <input type="radio" name="required" value="1" {{ old('required') == 1 ? 'checked' : '' }}> Evet
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                    @error('required')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">Başlık</label>
                                                <div class="col-9">
                                                    <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}">
                                                    @error('title')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">Açıklama</label>
                                                <div class="col-9">
                                                    <input type="text" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ old('description') }}">
                                                    @error('description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">Dil Desteği</label>
                                                <div class="col-9">
                                                    <div class="kt-radio-inline">
                                                        <label class="kt-radio">
                                                            <input type="radio" name="locale" value="0" {{ old('locale') == 0 ? 'checked' : '' }}> Yok
                                                            <span></span>
                                                        </label>
                                                        <label class="kt-radio">
                                                            <input type="radio" name="locale" value="1" {{ old('locale') == 1 ? 'checked' : '' }}> Var
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                    @error('locale')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">Key Değeri</label>
                                                <div class="col-9">
                                                    <input type="text" class="form-control @error('key') is-invalid @enderror" name="key" value="{{ old('key') }}">
                                                    @error('key')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row value-static">
                                                <label class="col-3 col-form-label">Value Değeri</label>
                                                <div class="col-9">
                                                    <input type="text" class="form-control @error('value') is-invalid @enderror" name="value" value="{{ old('value') }}">
                                                    @error('value')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            @foreach ($glb_locales as $key2 => $value2)
                                                <div class="form-group row value-array d-none">
                                                    <label class="col-3 col-form-label">Value Değeri</label>
                                                    <div class="col-9">
                                                        <input type="text" class="form-control @error('value['.$value2->abbr.']') is-invalid @enderror" name="{{ 'value['.$value2->abbr.']'}}" disabled="disabled" value="{{ old('value['.$value2->abbr.']') }}" placeholder="{{ $value2->title }}">
                                                        @error('value['.$value2->abbr.']')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            @endforeach
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">Sıralama</label>
                                                <div class="col-9">
                                                    <input type="number" class="form-control @error('sort') is-invalid @enderror" name="sort" value="{{ old('sort') }}">
                                                    @error('sort')
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
        $('input[type=radio][name=locale]').change(function (e) {

            if($(this).val() == 1){

                $('.value-static').addClass('d-none');
                $('.value-array').removeClass('d-none');

                $('.value-array').find('input').attr('disabled', false);
                $('.value-static').find('input').attr('disabled', 'disabled');

            }else{

                $('.value-array').addClass('d-none');
                $('.value-static').removeClass('d-none');

                $('.value-static').find('input').attr('disabled', false);
                $('.value-array').find('input').attr('disabled', 'disabled');
            }

        });
    </script>
@endsection
