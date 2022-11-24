<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!-- begin::Head -->
<head>
    <!--begin::Base Path (base relative path for assets of this page) -->
    <base href="{{ url()->current() }}">

    <!--end::Base Path -->
    <meta charset="utf-8" />
    <title>@lang('portal.login') - {{ getSettings('title', app()->getLocale()) }}</title>
    <meta name="description" content="Takipsan giriş sayfası lütfen giriş yapınız.">
    <meta name="owner" content="Takipsan">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!--begin::Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700|Roboto:300,400,500,600,700">
    <!--end::Fonts -->

    <!--begin::Page Custom Styles(used by this page) -->
    <link href="/assets/css/pages/login/login-3.css" rel="stylesheet" type="text/css" />

    <!--end::Page Custom Styles -->

    <!--begin:: Global Mandatory Vendors -->
    <link href="/assets/vendors/general/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" type="text/css" />
    <!--end:: Global Mandatory Vendors -->

    <link href="/assets/vendors/general/sweetalert2/dist/sweetalert2.css" rel="stylesheet" type="text/css" />

    <!--begin:: Global Optional Vendors -->
    <link href="/assets/vendors/general/animate.css/animate.css" rel="stylesheet" type="text/css" />
    <!--end:: Global Optional Vendors -->

    <!--begin::Global Theme Styles(used by all pages) -->
    <link href="/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <!--end::Global Theme Styles -->

    <link rel="shortcut icon" href="/assets/media/logos/favicon.ico" />
</head>

<!-- end::Head -->

<!-- begin::Body -->
<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--fixed kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading">

<!-- begin:: Page -->
<div class="kt-grid kt-grid--ver kt-grid--root">
    <div class="kt-grid kt-grid--hor kt-grid--root  kt-login kt-login--v3  @error('forgotusername') kt-login--forgot @else kt-login--signin @enderror" id="kt_login">
        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" style="background-image: url(/assets/media//bg/bg-3.jpg);">
            <div class="kt-grid__item kt-grid__item--fluid kt-login__wrapper">
                <div class="kt-login__container">
                    <div class="kt-login__logo">
                        <a href="/">
                            <img src="/assets/media/logos/logo-5.png">
                        </a>
                    </div>
                    <div class="kt-login__signin">
                        <div class="kt-login__head">
                            <h3 class="kt-login__title">@lang('portal.login_text')</h3>
                        </div>

                        @error('username')
                            <div class="kt-alert kt-alert--outline alert alert-danger alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror

                        @error('password')
                        <div class="kt-alert kt-alert--outline alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                            <span>{{ $message }}</span>
                        </div>
                        @enderror

                        <form class="kt-form" method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="input-group">
                                <input class="form-control" type="text" placeholder="@lang('portal.username')" name="username" id="username" value="{{ old('username') }}" required autocomplete="off">
                            </div>
                            <div class="input-group">
                                <input class="form-control" type="password" placeholder="@lang('portal.password')" name="password" required autocomplete="current-password">
                            </div>
                            <div class="row kt-login__extra">
                                <div class="col">
                                    <label class="kt-checkbox">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}> @lang('portal.remember_me')
                                        <span></span>
                                    </label>
                                </div>
                                <div class="col kt-align-right">
                                    <a href="javascript:;" id="kt_login_forgot" class="kt-login__link">@lang('portal.forgot_password')</a>
                                </div>
                            </div>
                            <div class="kt-login__actions">
                                <button type="submit" id="kt_login_signin_submit" class="btn btn-brand btn-elevate kt-login__btn-primary">@lang('portal.login')</button>
                            </div>
                        </form>
                    </div>
                    <div class="kt-login__forgot">

                        <div class="kt-login__head">
                            <h3 class="kt-login__title">@lang('portal.forgot_password')</h3>
                            <div class="kt-login__desc">@lang('portal.forgot_password_text')</div>
                        </div>

                        @error('forgotusername')
                        <div class="kt-alert kt-alert--outline alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                            <span>{{ $message }}</span>
                        </div>
                        @enderror
                        <form class="kt-form" method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="input-group">
                                <input class="form-control" type="text" placeholder="@lang('portal.username')" name="username" id="kt_email" autocomplete="off" required>
                            </div>
                            <div class="kt-login__actions">
                                <button id="kt_login_forgot_submit" class="btn btn-brand btn-elevate kt-login__btn-primary" type="submit">@lang('portal.reset_pass_link')</button>&nbsp;&nbsp;
                                <button id="kt_login_forgot_cancel" class="btn btn-light btn-elevate kt-login__btn-secondary">@lang('portal.cancel')</button>
                            </div>
                        </form>
                    </div>
                    <div class="text-center mt-5">
                        @foreach(session('glb_locales') as $key => $value)
                            <a href="{{ url($value->abbr) }}" class="d-inline-block mr-1">
                                <img src="/assets/media/flags/{{ $value->abbr }}-flag.svg" height="16" alt="" /> {{ app()->getLocale() == 'tr' ? $value->title : $value->title_glb }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- end:: Page -->

<!-- begin::Global Config(global config for global JS sciprts) -->
<script>
    var KTAppOptions = {
        "colors": {
            "state": {
                "brand": "#5d78ff",
                "dark": "#282a3c",
                "light": "#ffffff",
                "primary": "#5867dd",
                "success": "#34bfa3",
                "info": "#36a3f7",
                "warning": "#ffb822",
                "danger": "#fd3995"
            },
            "base": {
                "label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
                "shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]
            }
        }
    };
</script>

<!-- end::Global Config -->

<!--begin:: Global Mandatory Vendors -->
<script src="/assets/vendors/general/jquery/dist/jquery.js" type="text/javascript"></script>
<script src="/assets/vendors/general/popper.js/dist/umd/popper.js" type="text/javascript"></script>
<script src="/assets/vendors/general/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
<script src="/assets/vendors/general/js-cookie/src/js.cookie.js" type="text/javascript"></script>
<script src="/assets/vendors/general/moment/min/moment.min.js" type="text/javascript"></script>
<script src="/assets/vendors/general/tooltip.js/dist/umd/tooltip.min.js" type="text/javascript"></script>
<script src="/assets/vendors/general/perfect-scrollbar/dist/perfect-scrollbar.js" type="text/javascript"></script>
<script src="/assets/vendors/general/sticky-js/dist/sticky.min.js" type="text/javascript"></script>
<script src="/assets/vendors/general/wnumb/wNumb.js" type="text/javascript"></script>
<!--end:: Global Mandatory Vendors -->

<script src="/assets/vendors/general/sweetalert2/dist/sweetalert2.min.js" type="text/javascript"></script>
<script src="/assets/vendors/custom/js/vendors/sweetalert2.init.js" type="text/javascript"></script>

<!--begin::Global Theme Bundle(used by all pages) -->
<script src="/assets/js/scripts.bundle.js" type="text/javascript"></script>
<!--end::Global Theme Bundle -->

<!--begin::Page Scripts(used by this page) -->
<script src="/assets/js/pages/login/login-general.js" type="text/javascript"></script>
<!--end::Page Scripts -->

@if (Session::has('flash_message'))
    <script type="application/javascript">
        $(document).ready(function(){
            swal.fire({
                title: "{{Session::get('flash_message')[0]}}",
                text: "{{Session::get('flash_message')[1]}}",
                type: "{{Session::get('flash_message')[2]}}",
                confirmButtonText: "@lang('portal.ok')",
                cancelButtonText: "@lang('portal.cancel')",
                buttonsStyling: false,
                confirmButtonClass: 'btn btn-primary',
                cancelButtonClass: 'btn btn-light'
            });
        });
    </script>
@endif
</body>
<!-- end::Body -->
</html>
