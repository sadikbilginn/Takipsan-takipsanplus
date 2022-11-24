@extends('layouts.main')

@section('content')

<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

    <!-- begin:: Subheader -->
    <div class="kt-subheader kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">
                    @lang('portal.devices')
                </h3>
                <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                <div class="kt-subheader__group" id="kt_subheader_search">
                    <span class="kt-subheader__desc" id="kt_subheader_total">@lang('portal.add_new_device')</span>
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
                            <h3 class="kt-portlet__head-title">@lang('portal.add_new_device') <small> @lang('portal.form_text')</small></h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <a href="javascript:history.go(-1);" class="btn btn-clean kt-margin-r-10">
                                <i class="la la-arrow-left"></i>
                                <span class="kt-hidden-mobile">@lang('portal.back')</span>
                            </a>
                            <div class="btn-group">
                                <button type="button" class="btn btn-brand" onclick="document.getElementById('device_form').submit();">
                                    <i class="la la-check"></i>
                                    <span class="kt-hidden-mobile">@lang('portal.save')</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <form class="kt-form" id="device_form" action="{{ route('device.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="kt-section kt-section--first">
                                        <div class="kt-section__body">
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.company')</label>
                                                <div class="col-9">
                                                    <select name="company_id" class="form-control @error('company_id') is-invalid @enderror">
                                                        <option value="" selected>@lang('portal.choose')</option>
                                                        @foreach($companies as $key => $value)
                                                            <option value="{{ $value->id }}" {{ $value->id == old('company_id') ? 'selected' : '' }}>{{ $value->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('company_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.device_name')</label>
                                                <div class="col-9">
                                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
                                                    @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.device_type')</label>
                                                <div class="col-9">
                                                    <select name="device_type" class="form-control @error('device_type') is-invalid @enderror">
                                                        <option value="" selected>@lang('portal.choose')</option>
                                                        @foreach(config('settings.devices') as $key => $value)
                                                            <option value="{{ $key }}" {{ old('device_type') == $key ? 'selected' : '' }}> {{ $value['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('device_type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.reader')</label>
                                                <div class="col-9">
                                                    <select name="reader" class="form-control @error('reader') is-invalid @enderror">
                                                        <option value="" selected>@lang('portal.choose')</option>
                                                        @foreach(config('settings.readers') as $key => $value)
                                                            <option value="{{ $key }}" {{ old('reader') == $key ? 'selected' : '' }}> {{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('reader')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.reader_mode')</label>
                                                <div class="col-9">
                                                    <select name="reader_mode" id="reader_mode" class="form-control @error('reader_mode') is-invalid @enderror">
                                                        <option value="" selected>@lang('portal.choose')</option>
                                                        @foreach(config('settings.reader_mode') as $key => $value)
                                                            <option value="{{ $key }}" {{ old('reader_mode') == $key ? 'selected' : '' }}> {{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('reader_mode')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.est_population')</label>
                                                <div class="col-9">
                                                    <input type="number" name="estimated_population" id="estimated_population" class="form-control @error('estimated_population') is-invalid @enderror" min="0"  value="{{ old('estimated_population', 0) }}">
                                                    @error('estimated_population')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.search_mode')</label>
                                                <div class="col-9">
                                                    <select name="search_mode" id="search_mode" class="form-control @error('search_mode') is-invalid @enderror">
                                                        <option value="" selected>@lang('portal.choose')</option>
                                                        @foreach(config('settings.search_mode') as $key => $value)
                                                            <option value="{{ $key }}" {{ old('search_mode') == $key ? 'selected' : '' }}> {{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('search_mode')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.session')</label>
                                                <div class="col-9">
                                                    <select name="session" id="session" class="form-control @error('session') is-invalid @enderror">
                                                        <option value="" selected>@lang('portal.choose')</option>
                                                        @foreach(config('settings.reader_session') as $key => $value)
                                                            <option value="{{ $key }}" {{ old('session') == $key ? 'selected' : '' }}> {{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('session')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.text_settings')</label>
                                                <div class="col-9">
                                                    <textarea name="string_set" id="string_set" cols="30" rows="10" class="form-control @error('name') is-invalid @enderror">{{ old('string_set') }}</textarea>
                                                    @error('string_set')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.gpio_start')</label>
                                                <div class="col-9">
                                                    <input type="text" name="gpio_start" class="form-control @error('gpio_start') is-invalid @enderror" value="{{ old('gpio_start') }}">
                                                    @error('gpio_start')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.gpio_stop')</label>
                                                <div class="col-9">
                                                    <input type="text" name="gpio_stop" class="form-control @error('gpio_stop') is-invalid @enderror" value="{{ old('gpio_stop') }}">
                                                    @error('gpio_stop')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.gpio_error')</label>
                                                <div class="col-9">
                                                    <input type="text" name="gpio_error" class="form-control @error('gpio_error') is-invalid @enderror" value="{{ old('gpio_error') }}">
                                                    @error('gpio_error')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.printer_address')</label>
                                                <div class="col-9">
                                                    <input type="text" name="printer_address" class="form-control @error('printer_address') is-invalid @enderror" value="{{ old('printer_address') }}">
                                                    @error('printer_address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.reader_ip_address')</label>
                                                <div class="col-9">
                                                    <input type="text" name="device_ip" class="form-control @error('device_ip') is-invalid @enderror" value="{{ old('device_ip') }}">
                                                    @error('device_ip')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.package_close_time')</label>
                                                <div class="col-9">
                                                    <input type="text" name="package_timeout" class="form-control @error('package_timeout') is-invalid @enderror" value="{{ old('package_timeout') }}">
                                                    @error('package_timeout')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.auto_print')</label>
                                                <div class="col-9">
                                                    <span class="kt-switch kt-switch--lg kt-switch--outline kt-switch--icon kt-switch--success  @error('auto_print') is-invalid @enderror">
                                                        <label>
                                                            <input type="checkbox" {{ old('auto_print') == 'on' ? 'checked="checked"' : '' }} name="auto_print" id="auto_print">
                                                            <span></span>
                                                        </label>
                                                     </span>
                                                    @error('auto_print')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            {{-- 
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.status')</label>
                                                <div class="col-9">
                                                    <span class="kt-switch kt-switch--lg kt-switch--outline kt-switch--icon kt-switch--success  @error('status') is-invalid @enderror">
                                                        <label>
                                                            <input type="checkbox"  {{ old('status') == 'on' ? 'checked="checked"' : '' }} name="status" id="status">
                                                            <span></span>
                                                        </label>
                                                     </span>
                                                    @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            --}}
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.read_mode')</label>
                                                <div class="col-9">
                                                    <select name="read_type_id" id="read_type_id" class="form-control @error('read_type_id') is-invalid @enderror">
                                                        <option value="0">Manuel</option>
                                                        @foreach($read_types as $key => $value)
                                                            <option value="{{ $value->id }}" {{ $value->id == old('read_type_id') ? 'selected' : '' }}>{{ $value->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('read_type_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.common_power')</label>
                                                <div class="col-9">
                                                    <span class="kt-switch kt-switch--lg kt-switch--outline kt-switch--icon kt-switch--success  @error('common_power') is-invalid @enderror">
                                                        <label>
                                                            <input type="checkbox" checked="checked" name="common_power" id="common_power">
                                                            <span></span>
                                                        </label>
                                                     </span>
                                                    @error('common_power')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.antennas')</label>
                                                <div class="col-9">
                                                    <div class="row antenna">
                                                        <div class="col-6">
                                                            <select name="antenna[read]" id="common_antenna_read" class="form-control">
                                                                <option value="0">@lang('portal.read')</option>
                                                                @for($i=1; $i<=30; $i++)
                                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                        <div class="col-6">
                                                            <select name="antenna[write]" id="common_antenna_write" class="form-control">
                                                                <option value="0">@lang('portal.write')</option>
                                                                @for($i=1; $i<=30; $i++)
                                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="antennas d-none">
                                                        @for($k=1; $k<=4; $k++)
                                                            <div class="input-group mb-2">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">
                                                                        <label class="kt-checkbox kt-checkbox--single kt-checkbox--success">
                                                                            <input type="checkbox" checked="checked" name="antennas[{{$k}}]" disabled id="antenna_{{$k}}" >
                                                                            <span></span>
                                                                        </label>
                                                                    </span>
                                                                    <span class="input-group-text">@lang('portal.antenna') {{$k}}</span>
                                                                </div>
                                                                <select name="antennas[{{$k}}][read]" id="antenna_read_{{$k}}" disabled class="form-control">
                                                                    <option value="0">@lang('portal.read')</option>
                                                                    @for($i=1; $i<=30; $i++)
                                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                                    @endfor
                                                                </select>
                                                                <select name="antennas[{{$k}}][write]" id="antenna_write_{{$k}}" disabled class="form-control">
                                                                    <option value="0">@lang('portal.write')</option>
                                                                    @for($i=1; $i<=30; $i++)
                                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                                    @endfor
                                                                </select>
                                                            </div>
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="kt-section kt-section--first">
                                        <div class="kt-section__body">
                                            
                                            {{--<div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.manufacturer')</label>
                                                <div class="col-9">
                                                    <select name="manufacturer_id" id="manufacturer_id" class="form-control select @error('manufacturer_id') is-invalid @enderror">
                                                        <option value="" selected>@lang('portal.choose')</option>
                                                    </select>
                                                    @error('manufacturer_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.license_type')</label>
                                                <div class="col-9">
                                                    <select name="license_type" id="license_type" class="form-control select @error('license_type') is-invalid @enderror">
                                                        <option value="" selected>@lang('portal.choose')</option>
                                                        <option value="Lite">Lite</option>
                                                        <option value="Pro">Pro</option>
                                                    </select>
                                                    @error('license_type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>--}}
                                            <!-- <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.user')</label>
                                                <div class="col-9">
                                                    <select name="user_id" id="user_id" class="form-control select @error('user_id') is-invalid @enderror">
                                                        <option value="" selected>@lang('portal.choose')</option>
                                                    </select>
                                                    @error('user_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div> -->
                                            
                                            {{-- Eski Yıl bazlı Lisans seçimi  --}}
                                            {{-- <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.license_period')</label>
                                                <div class="col-9">
                                                    <select name="license_period" class="form-control @error('license_period') is-invalid @enderror">
                                                        <option value="" selected>Seçiniz</option>
                                                        @for($i=1; $i<=10; $i++)
                                                            <option value="{{ $i }}" {{ old('license_period') == $i ? 'selected' : '' }}>{{ $i }} Yıl</option>
                                                        @endfor
                                                    </select>
                                                    @error('license_period')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div> --}}
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.device_aim')</label>
                                                <div class="col-9">
                                                    <select name="device_aim" class="form-control @error('device_aim') is-invalid @enderror">
                                                        <option value="" selected>@lang('portal.choose')</option>
                                                        @foreach(config('settings.device_aims') as $key => $value)
                                                            <option value="{{ $key }}" {{ old('device_aims') && old('device_aims') == $key ? 'selected' : '' }}>@lang($value)</option>
                                                        @endforeach                                                       
                                                    </select>
                                                    @error('device_aim')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.license_start')</label>
                                                <div class="col-9">
                                                    <input type="date" class="form-control @error('start_at') is-invalid @enderror" name="start_at" value="{{ old('start_at', $start_at) }}">
                                                    @error('start_at')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.license_finish')</label>
                                                <div class="col-9">
                                                    <input type="date" class="form-control @error('finish_at') is-invalid @enderror" name="finish_at" value="{{ old('finish_at', $finish_at) }}">
                                                    @error('finish_at')
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

    <script type="text/javascript">

        $(function () {

            const checkbox = document.getElementById('common_power');
            checkbox.addEventListener('change', (event) => {
                if (event.target.checked) {
                    $('#common_antenna_read').prop('disabled', false);
                    $('#common_antenna_write').prop('disabled', false);
                    $('[id^=antenna_]').prop('disabled', true);
                    $('.antenna').removeClass('d-none');
                    $('.antennas').addClass('d-none');
                }else{
                    $('#common_antenna_read').prop('disabled', true);
                    $('#common_antenna_write').prop('disabled', true);
                    $('[id^=antenna_]').prop('disabled', false);
                    $('.antenna').addClass('d-none');
                    $('.antennas').removeClass('d-none');
                }
            });

            @for($k=1; $k<=4; $k++)
                const checkbox_antenna_{{ $k }} = document.getElementById('antenna_{{ $k }}');
                checkbox_antenna_{{ $k }}.addEventListener('change', (event) => {
                    if (event.target.checked) {
                        $('[id^=antenna_read_{{ $k }}]').prop('disabled', false);
                        $('[id^=antenna_write_{{ $k }}]').prop('disabled', false);
                    }else{
                        $('[id^=antenna_read_{{ $k }}]').prop('disabled', true);
                        $('[id^=antenna_write_{{ $k }}]').prop('disabled', true);
                    }
                });
            @endfor

        });
    </script>
@endsection
