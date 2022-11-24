@extends('station.layout.main')

@section('content')

    <div class="row">
        <div class="col-lg-2 col-xl-3">
        </div>
        <div class="col-lg-8 col-xl-6">
            <div class="login">
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <h1 class="logo">{{ config('station.title') }}</h1>
                        <h2 class="color-1">@lang('station.login')</h2>
                        <p>@lang('station.login_text')</p>
                    </div>
                </div>
                <form action="{{ route('station.login') }}" method="post">
                    @csrf
                    <input type="hidden" name="device_id" id="device_id" value="">
                    <div class="form-group">
                        <input type="text" name="email" class="form-control" id="username" placeholder="@lang('station.username')" value="{{ old('email') }}{{Cookie::get('email')}}">
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" class="form-control" id="password" placeholder="@lang('station.password')" value="{{Cookie::get('password')}}">
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="remember" class="custom-control-input" id="remember_me" {{ Cookie::get('remember') ? 'checked' : ''}}>
                            <label class="custom-control-label" for="remember_me">@lang('portal.remember_me')</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-block btn-primary">@lang('station.login')</button>
                </form>
            </div>
        </div>
        <div class="col-lg-2 col-xl-3">
            <div class="lang">
                <a href="/{{ app()->getLocale() == 'tr' ? 'en' : 'tr' }}">{{ app()->getLocale() == 'tr' ? 'EN' : 'TR' }}</a>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script type="text/javascript">
        $(function () {
            //localStorage.removeItem('deviceId');
            var device = localStorage.getItem('deviceId');
            if(device !== null){
                $('#device_id').val(device);
            }
        });
    </script>
@endsection
