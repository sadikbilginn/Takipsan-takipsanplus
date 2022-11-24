@if(session('device.device_type') == 'box_station')
<div class="row top-bar">
    <div class="col-lg-5 col-xl-6">
        <a onclick="resetStorage();" class="logo" id="bridgeStatus">
            {{ config('settings.devices.'. session('device.device_type') .'.name') }}
        </a>
        <small id="bridgeReaderStatus">{{ config('station.version') }}</small> |
        <a href="javascript:logout();">@lang('station.logout')</a>
    </div>
    <div class="col-lg-7 col-xl-6">
        <div class="row">
            <div class="col-sm-4 col-lg-4 info">
                {{ auth()->user()->name }}
            </div>
            <div class="col-sm-4 col-lg-4 info">
                {{ auth()->user()->company ? auth()->user()->company->name : '-' }}
            </div>
            <div class="col-lg-2 info">
                <a href="/{{ app()->getLocale() == 'tr' ? 'en' : 'tr' }}" class="d-block">
                    {{ app()->getLocale() == 'tr' ? 'EN' : 'TR' }}
                </a>
            </div>
            <div class="col-lg-2 skin">
                <a href="javascript:;" class="change-theme d-block">
                    <img src="/station/img/sun.svg" alt="skin">
                </a>
            </div>
        </div>
    </div>
</div>
@else
<div class="row top-bar">
    <div class="col-lg-5 col-xl-6">
        <a onclick="resetStorage();" class="logo" id="bridgeStatus">
            {{ config('settings.devices.'. session('device.device_type') .'.name') }}
        </a>
        <small id="bridgeReaderStatus">{{ config('station.version') }}</small> |
        <a href="javascript:logout();">@lang('station.logout')</a>
    </div>
    <div class="col-lg-7 col-xl-6">
        <div class="row">
            <div class="col-sm-6 col-lg-3 info">
                {{ auth()->user()->name }}
            </div>
            <div class="col-sm-6 col-lg-3 info">
                {{ auth()->user()->company ? auth()->user()->company->name : '-' }}
            </div>
            <div class="col-lg-2 info">
                <a href="/{{ app()->getLocale() == 'tr' ? 'en' : 'tr' }}" class="d-block">
                    {{ app()->getLocale() == 'tr' ? 'EN' : 'TR' }}
                </a>
            </div>
            <div class="col-lg-2 info">
                <a href="file:///home/box2/takipsan-bridge/html/index.html">
                    <img src="/station/img/network/earth-globe.svg" id="networkIcon" alt="network">
                </a>
            </div>
            <div class="col-lg-2 skin">
                <a href="javascript:;" class="change-theme d-block">
                    <img src="/station/img/sun.svg" alt="skin">
                </a>
            </div>
        </div>
    </div>
</div>
@endif
