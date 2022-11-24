<div class="row settings">
    <div class="col-12 text-center mb-3">
        <span class="form-title">@lang('station.settings')</span>
        <span class="close-page">
            <button type="button" class="btn btn-circle close" data-dismiss="modal" aria-label="Close">
                <span class="icon-delete">X</span>
            </button>
        </span>
    </div>
    <div class="col-12 mb-3">
        <ul class="nav nav-tabs" id="settingTab" role="tablist">
            <li class="nav-item">
                <a
                    class="nav-link active"
                    id="home-tab"
                    data-toggle="tab"
                    href="#home"
                    role="tab"
                    aria-controls="home"
                    aria-selected="true"
                >
                    @lang('station.general')
                </a>
            </li>
            <li class="nav-item">
                <a
                    class="nav-link"
                    id="advanced-tab"
                    data-toggle="tab"
                    href="#advanced"
                    role="tab"
                    aria-controls="advanced"
                    aria-selected="false"
                >
                    @lang('station.advanced')
                </a>
            </li>
        </ul>
    </div>
    <div class="col-12">
        <form action="#" method="post" id="settingForm">
            @csrf
            <div class="tab-content" id="settingTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <div class="setting-box position-relative">
                                        <div class="col-12 title p-4">
                                            @lang('station.package_timeout')
                                            <span class="bubble1">
                                                <output id="package_close">{{ $device->package_timeout }}</output> sn
                                            </span>
                                        </div>
                                        <div class="form-group ">
                                            <div class="range">
                                                <input
                                                    type="range"
                                                    min="1"
                                                    max="6"
                                                    name="package_timeout"
                                                    value="{{ $device->package_timeout }}"
                                                    class="range-slider"
                                                    id="package_timeout"
                                                    oninput="package_close.value = package_timeout.value"
                                                >
                                                <div class="range-slider-ticks">
                                                    <p>1sn</p>
                                                    <p>2sn</p>
                                                    <p>3sn</p>
                                                    <p>4sn</p>
                                                    <p>5sn</p>
                                                    <p>6sn</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <div class="setting-box mb-4">
                                        <div class="row">
                                            <div class="col-7">
                                                <span class="title">@lang('station.auto_print')</span>
                                            </div>
                                            <div class="col-5">
                                                <div class="custom-switch">
                                                    <input
                                                        class="custom-switch-input"
                                                        id="auto_print"
                                                        type="checkbox"
                                                        name="auto_print"
                                                        {{ $device->auto_print == true ? 'checked="checked"' : '' }}
                                                    >
                                                    <label class="custom-switch-btn" for="auto_print"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="setting-box mb-4">
                                        <div class="row">
                                            <div class="col-7">
                                                <span class="title">@lang('station.auto_model_name')</span>
                                            </div>
                                            <div class="col-5">
                                                <div class="custom-switch">
                                                    <input
                                                        class="custom-switch-input"
                                                        id="auto_model_name"
                                                        type="checkbox"
                                                        name="auto_model_name"
                                                        {{ $device->auto_model_name == true ? 'checked="checked"' : '' }}
                                                    >
                                                    <label class="custom-switch-btn" for="auto_model_name"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="setting-box mb-4">
                                        <div class="row">
                                            <div class="col-7">
                                                <span class="title">@lang('station.auto_size_name')</span>
                                            </div>
                                            <div class="col-5">
                                                <div class="custom-switch">
                                                    <input
                                                        class="custom-switch-input"
                                                        id="auto_size_name"
                                                        type="checkbox"
                                                        name="auto_size_name"
                                                        {{ $device->auto_size_name == true ? 'checked="checked"' : '' }}
                                                    >
                                                    <label class="custom-switch-btn" for="auto_size_name"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="advanced" role="tabpanel" aria-labelledby="advanced-tab">
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <a
                                        href="file:///home/box2/takipsan-bridge/html/index.html"
                                        class="btn btn-primary btn-lg float-right"
                                    >
                                        @lang('station.link_degistir')
                                    </a>
                                    <a
                                        href="/xml_file_repo"
                                        id="xml_file_btn"
                                        class="btn btn-primary btn-lg float-right"
                                        style="margin-right:10px;"
                                    >
                                        @lang('station.xml_file_btn')
                                    </a>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <div class="setting-box">
                                        <div class="row">
                                            <div class="col-12 title p-4">
                                                @lang('station.read_mode')
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <select
                                                        name="read_type_id"
                                                        id="read_type_id"
                                                        class="form-control custom-select"
                                                        style="display: block!important;"
                                                    >
                                                        <option value="0">
                                                            @lang('station.custom_setting')
                                                        </option>
                                                        @foreach($read_types as $key => $value)
                                                        <option
                                                            value="{{ $value->id }}"
                                                            data-reader="{{ $value->reader }}"
                                                            {{
                                                                $value->id == old('read_type_id', $device->read_type_id) ?
                                                                    'selected' : ''
                                                            }}
                                                        >
                                                            {{
                                                                app()->getLocale() == 'tr' ?
                                                                    $value->name : $value->name_en
                                                            }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <div class="setting-box">
                                        <div class="row">
                                            <div class="col-12 title p-4">
                                                @lang('station.device_address')
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <input
                                                        type="text"
                                                        name="device_ip"
                                                        id="device_ip"
                                                        class="
                                                            form-control
                                                            form-control-lg
                                                            @error('device_ip') is-invalid @enderror
                                                        "
                                                        value="{{ old('device_ip', $device->ip_address) }}"
                                                    >
                                                    @error('device_ip')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <div class="setting-box">
                                        <div class="row">
                                            <div class="col-12 title p-4">
                                                @lang('station.barcode_port')
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <input
                                                        type="text"
                                                        name="barcode_ip_address"
                                                        id="barcode_ip_address"
                                                        class="
                                                            form-control
                                                            form-control-lg
                                                            @error('barcode_ip_address') is-invalid @enderror
                                                        "
                                                        value="{{ old('barcode_ip_address', $device->barcode_ip_address) }}"
                                                    >
                                                    @error('barcode_ip_address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <div class="setting-box">
                                        <div class="row">
                                            <div class="col-12 title p-4">
                                                @lang('station.barcode_status')
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <select
                                                        name="barcode_status"
                                                        id="barcode_status"
                                                        class="form-control custom-select"
                                                    >
                                                        <option
                                                            value="Kapalı"
                                                            {{ old('barcode_status', $device->barcode_status) == 'Kapalı' ? 'selected' : '' }}
                                                        >@lang('station.closed')</option>
                                                        <option
                                                            value="Açık"
                                                            {{ old('barcode_status', $device->barcode_status) == 'Açık' ? 'selected' : '' }}
                                                        >@lang('station.open')</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-3">

                                    <div class="setting-box">
                                        <div class="row">
                                            <div class="col-12 title p-4">
                                                @lang('station.bridgeCloseTime')
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <input
                                                        type="text"
                                                        name="bridgeCloseTime"
                                                        id="bridgeCloseTime"
                                                        class="
                                                            form-control
                                                            form-control-lg
                                                            @error('bridgeCloseTime') is-invalid @enderror
                                                        "
                                                        value="{{ old('bridgeCloseTime', $device->bridgeCloseTime) }}"
                                                    >
                                                    @error('bridgeCloseTime')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-lg-12 mb-3">
                                    <div class="setting-box position-relative">
                                        <div class="row">
                                            <div class="col-12 title p-4">@lang('station.antenna_settings')</div>
                                            <div class="col-6 mb-3">
                                                <div class="row">
                                                    <div class="col-7">
                                                        <span class="title">@lang('station.common_power')</span>
                                                    </div>
                                                    <div class="col-5">
                                                        <div class="custom-switch">
                                                            <input
                                                                class="custom-switch-input"
                                                                id="common_power"
                                                                type="checkbox"
                                                                name="common_power"
                                                                {{ $device->common_power == true ? 'checked="checked"' : '' }}
                                                            >
                                                            <label class="custom-switch-btn" for="common_power"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 antenna {{ $device->common_power == false ? 'd-none' : ''}}">
                                                <div class="form-group">
                                                    <label for="antenna1">@lang('station.power')</label>
                                                    <span class="bubble">
                                                        <output id="common_antenna_output">
                                                            {{
                                                                $device->common_power == true && json_decode($device->antennas) ?
                                                                    json_decode($device->antennas)->read : '0'
                                                            }}
                                                        </output>
                                                        dBm
                                                    </span>
                                                    <div class="range">
                                                        <input
                                                            type="range"
                                                            name="antenna"
                                                            min="1"
                                                            max="30"
                                                            value="{{
                                                                $device->common_power == true && json_decode($device->antennas) ?
                                                                    json_decode($device->antennas)->read : '0'
                                                            }}"
                                                            class="range-slider"
                                                            id="common_antenna_read"
                                                            oninput="common_antenna_output.value = common_antenna_read.value"
                                                            {{ $device->common_power == false ? 'disabled' : '' }}
                                                        >
                                                        <div class="range-slider-ticks">
                                                            @for($i=1; $i<=30; $i++)
                                                            <p>{{ $i }}</p>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 antennas {{ $device->common_power == true ? 'd-none' : ''}}">
                                                <div class="row">
                                                    @for($k=1; $k<=4; $k++)
                                                    <div class="col-10 mb-2 antenna-d-{{$k}}">
                                                        <span class="bubble2">
                                                            <output id="antenna_read_{{$k}}_output">
                                                                {{
                                                                    $device->common_power == false && isset(json_decode($device->antennas)->$k) ?
                                                                        json_decode($device->antennas)->$k->read : '0'
                                                                }}
                                                            </output>
                                                            dBm
                                                        </span>
                                                        <div class="form-group">
                                                            <div class="range">
                                                                <input
                                                                    type="range"
                                                                    name="antennas[{{$k}}]"
                                                                    id="antenna_read_{{$k}}"
                                                                    min="1"
                                                                    max="30"
                                                                    value="{{
                                                                        $device->common_power == false && isset(json_decode($device->antennas)->$k) ?
                                                                            json_decode($device->antennas)->$k->read : '0'
                                                                    }}"
                                                                    class="range-slider"
                                                                    oninput="antenna_read_{{$k}}_output.value = antenna_read_{{$k}}.value"
                                                                    {{
                                                                        $device->common_power == false && isset(json_decode($device->antennas)->$k) ?
                                                                            '' : 'disabled'
                                                                    }}
                                                                    {{ $device->common_power == true ? 'disabled' : '' }}
                                                                >
                                                                <div class="range-slider-ticks">
                                                                    @for($i=1; $i<=30; $i++)
                                                                    <p>{{ $i }}</p>
                                                                    @endfor
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-2 mb-2 antenna-d-{{$k}}">
                                                        <label for="antenna1">@lang('station.antenna') {{$k}} </label>
                                                        <div class="custom-switch">
                                                            <input
                                                                class="custom-switch-input"
                                                                type="checkbox"
                                                                name="antennasStatus[{{$k}}]"
                                                                id="antenna_{{$k}}"
                                                                {{
                                                                    $device->common_power == false && isset(json_decode($device->antennas)->$k) ?
                                                                        'checked="checked"' : ''
                                                                }}
                                                                {{ $device->common_power == true ? 'disabled' : '' }}
                                                            >
                                                            <label class="custom-switch-btn" for="antenna_{{$k}}"></label>
                                                        </div>
                                                    </div>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3 advanced d-none">
                                    <div class="setting-box">
                                        <div class="row">
                                            <div class="col-12 title p-4">
                                                @lang('station.rfid_reader')
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <select name="reader" id="reader" class="form-control custom-select">
                                                        <option value="" selected>Seçiniz</option>
                                                        @foreach(config('settings.readers') as $key => $value)
                                                        <option
                                                            value="{{ $key }}"
                                                            {{ old('reader', $device->reader) == $key ? 'selected' : '' }}
                                                        >
                                                            {{ $value }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3 advanced d-none">
                                    <div class="setting-box">
                                        <div class="row">
                                            <div class="col-12 title p-4">
                                                Reader Mode
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <select
                                                        name="reader_mode"
                                                        id="reader_mode"
                                                        class="
                                                            form-control
                                                            custom-select
                                                            @error('reader_mode') is-invalid @enderror
                                                        "
                                                    >
                                                        <option value="" selected>Seçiniz</option>
                                                        @foreach(config('settings.reader_mode') as $key => $value)
                                                        <option
                                                            value="{{ $key }}"
                                                            {{ old('reader_mode', $device->reader_mode) == $key ? 'selected' : '' }}
                                                        >
                                                            {{ $value }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3 advanced d-none">
                                    <div class="setting-box">
                                        <div class="row">
                                            <div class="col-12 title p-4">
                                                Search Mode
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <select
                                                        name="search_mode"
                                                        id="search_mode"
                                                        class="
                                                            form-control
                                                            custom-select
                                                            @error('search_mode') is-invalid @enderror
                                                        "
                                                    >
                                                        <option value="" selected>Seçiniz</option>
                                                        @foreach(config('settings.search_mode') as $key => $value)
                                                        <option
                                                            value="{{ $key }}"
                                                            {{ old('search_mode', $device->search_mode) == $key ? 'selected' : '' }}
                                                        >
                                                            {{ $value }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3 advanced d-none">
                                    <div class="setting-box">
                                        <div class="row">
                                            <div class="col-12 title p-4">
                                                Session
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <select
                                                        name="session"
                                                        id="session"
                                                        class="
                                                            form-control
                                                            custom-select
                                                            @error('session') is-invalid @enderror
                                                        "
                                                    >
                                                        <option value="" selected>Seçiniz</option>
                                                        @foreach(config('settings.reader_session') as $key => $value)
                                                        <option
                                                            value="{{ $key }}"
                                                            {{ old('session', $device->session) == $key ? 'selected' : '' }}
                                                        >
                                                            {{ $value }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-3 advanced d-none">
                                    <div class="setting-box">
                                        <div class="row">
                                            <div class="col-12 title p-4">
                                                Est. Population
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <input
                                                        type="text"
                                                        name="estimated_population"
                                                        id="estimated_population"
                                                        class="
                                                            form-control
                                                            form-control-lg
                                                            @error('estimated_population') is-invalid @enderror
                                                        "
                                                        value="{{
                                                            old('estimated_population', $device->estimated_population)
                                                        }}"
                                                    >
                                                    @error('estimated_population')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-3 advanced d-none">
                                    <div class="setting-box">
                                        <div class="row">
                                            <div class="col-12 title p-4">
                                                Text @lang('station.settings')
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <textarea
                                                        name="string_set"
                                                        id="string_set"
                                                        cols="30"
                                                        rows="5"
                                                        class="form-control @error('name') is-invalid @enderror"
                                                    >{{ old('string_set', $device->string_set) }}</textarea>
                                                    @error('string_set')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 offset-lg-4">
                <button type="button" class="btn btn-block btn-primary submit" id="settingSubmitBtn">
                    @lang('station.save')
                </button>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">

    $(function () {

        disconnectReader(readerId);

        //maksimum anten sayısını belirler
        for (var i = 1 ; i <= 4 ; i++){
            if(br.readers[readerId].features.antennaCount < i){
                $('[id^=antenna_'+i+']').prop('disabled', true);
                $('.antenna-d-'+i).addClass('d-none');
            }
        }

        //sadece reader a uygun ayar setlerini gösteriyoruz
        // if(readType && readType != 0){
        //     if(br.readers[readerId].features.brand == 'Impinj'){
        //         $('#read_type_id option[data-reader="thingmagic"]').remove();
        //     }else{
        //         $('#read_type_id option[data-reader="impinj"]').remove();
        //     }
        // }

        //reader üzerinden desteklenen modları çeker
        if(br.readers[readerId].features.readModes.length > 0){
            $('#reader_mode').find('option').remove();
            $('#reader_mode').append('<option value="" selected>Seçiniz</option>');
            for (var i = 0 ; i <= br.readers[readerId].features.readModes.length - 1; i++) {
                $('#reader_mode').append(
                    '<option '+
                    (br.readers[readerId].features.readModes[i]==br.readers[readerId].settings.readerMode?'selected':'')+
                    ' value="'+br.readers[readerId].features.readModes[i]+
                    '"> '+
                        br.readers[readerId].features.readModes[i] +
                    '</option>'
                );
            }
        }

        //reader üzerinden desteklenen arama modlarını çeker
        if(br.readers[readerId].features.searchModes.length > 0){
            $('#search_mode').find('option').remove();
            $('#search_mode').append('<option value="" selected>Seçiniz</option>');
            for (var i = 0 ; i <= br.readers[readerId].features.searchModes.length - 1; i++) {
                $('#search_mode').append(
                    '<option value="'+
                        br.readers[readerId].features.searchModes[i]+
                    '"> '+
                        br.readers[readerId].features.searchModes[i] +
                    '</option>'
                );
            }
        }

        $("input[type=range]").mousemove(function (e) {
            var val = ($(this).val() - $(this).attr('min')) / ($(this).attr('max') - $(this).attr('min'));
            var percent = val * 100;
            $(this).css(
                'background-image',
                '-webkit-gradient(linear, left top, right top, ' +
                'color-stop(' + percent + '%, #3288FC), ' +
                'color-stop(' + percent + '%, #AFBED3)' +
                ')'
            );
        });

        if($('#read_type_id').val() == 0){
            $('.advanced').removeClass('d-none');
        }else{
            $('.advanced').addClass('d-none');
        }

        $("#read_type_id").on('change', function () {
            if($(this).val() == 0){
                $('.advanced').removeClass('d-none');
            }else{
                $('.advanced').addClass('d-none');
            }
        });

        $("#read_type_id").change();
		

        @if($device->common_power == false)
        $('#common_power').prop('checked', false);
        @else
        $('#common_power').prop('checked', true);
        @endif

        @if($device->auto_print == false)
        $('#auto_print').prop('checked', false);
        @else
        $('#auto_print').prop('checked', true);
        @endif

        const checkbox = document.getElementById('common_power');
        checkbox.addEventListener('change', (event) => {
            if (event.target.checked) {
                $('#common_antenna_read').prop('disabled', false);
                $('[id^=antenna_]').prop('disabled', true);
                $('.antenna').removeClass('d-none');
                $('.antennas').addClass('d-none');
            }else{
                $('#common_antenna_read').prop('disabled', true);
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
            }else{
                $('[id^=antenna_read_{{ $k }}]').prop('disabled', true);
            }
        });
        @endfor

    });

    $(".btn.btn-circle.close").on('click', function (){
        connectReader(readerId);
    });

    $("#settingSubmitBtn").on('click', function (){

        var btn = $(this);
        btn.attr('disabled', true);
        $('.invalid-feedback').text('');
        $('.form-control').removeClass('is-invalid');
        var form = document.querySelector('#settingForm');
        var data = new FormData(form);
        data.append('process', 'setSetting');

        //db işlemleri
        axios({
            url : stationAjaxUrl,
            method : 'post',
            data : data
        }).then(function (response) {
            btn.attr('disabled', false);
            if(response.data.status == false){
                if(response.data.errors){
                    errorPrint(response.data.errors);
                }else{
                    sweetAlert(
                        '@lang('station.failed')',
                        '@lang('station.error_text')',
                        'error',
                        "@lang('station.ok')"
                    );
                }
            }
            if(response.data.status == 'ok'){
                //setSettings();
                $('#pageModal').modal('hide');
                setTimeout(function (){
                    $('#pageModal .modal-body').html("");
                },1000);
                sweetAlert(
                    '@lang('station.successful')',
                    '@lang('station.settings_update')',
                    'success',
                    "@lang('station.ok')"
                );
            }
            window.location.reload();
        }).catch(function (error) {
            btn.attr('disabled', false);
            console.log(error);
        });
    });

    function setSettings(){
        //ip adresi değişince reader yeniden create edilmeli sebeple sayfayı yeniliyoruz
        if(readerIp != $('#device_ip').val() || readType != $('#read_type_id').val() || !bridgeStatus){
            window.location.reload();
        }

        package_close_time = $('#package_timeout').val();
        auto_print = $('#auto_print')[0].checked;
        auto_model_name = $('#auto_model_name')[0].checked;
        auto_size_name = $('#auto_size_name')[0].checked;
        readType = $('#read_type_id option:selected').val();
        reader = $('#read_type_id').val() == 0 ? $('#reader').val() : $('#read_type_id option:selected').attr('data-reader');
        //settingsStr
        br.readers[readerId].settings.settingsStr = $('#string_set').val();
        //readerMode
        br.readers[readerId].settings.readerMode = $('#reader_mode').val();
        //readerSession
        br.readers[readerId].settings.session = $('#session').val();
        //readerEstimatedPopulation
        br.readers[readerId].settings.tagPopulation = parseInt($('#estimated_population').val());
        // readerSearchMode
        br.readers[readerId].settings.searchMode = $('#search_mode').val();
        // antennas
        br.readers[readerId].settings.useCommonPowerSettings = $('#common_power')[0].checked;
        br.readers[readerId].settings.commonReadPower = parseFloat($('#common_antenna_read').val());
        br.readers[readerId].settings.commonWritePower = parseFloat($('#common_antenna_read').val());
        br.readers[readerId].settings.antennas=[];
        for (var i = 1 ; i <= br.readers[readerId].features.antennaCount ; i++){
            antenna = new Antenna();
            antenna.portNumber = i;
            antenna.isActive = $('#antenna_'+i)[0].checked;
            antenna.readPower = parseFloat($('#antenna_read_'+i).val());
            antenna.writePower = parseFloat($('#antenna_read_'+i).val());
            br.readers[readerId].settings.antennas.push(antenna);
        }
        connectReader(readerId);
    }

    function errorPrint(errors){
        $.each( errors, function( key, value ) {
            $("#" + key).addClass('is-invalid');
            $("#" + key).parent("div").find('.invalid-feedback').text(value);
        });
    }

    $("#xml_file_btn").on('click', function (){

        $(this).attr('disabled', true);
        $('#loading').show();

    });

</script>


