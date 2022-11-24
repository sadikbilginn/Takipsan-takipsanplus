<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!-- begin::Head -->
<head>

    <!--begin::Base Path (base relative path for assets of this page) -->
    <base href="/">

    <!--end::Base Path -->
    <meta charset="utf-8" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ getSettings('title', app()->getLocale()) }}</title>
    <meta name="description" content="Takipsan">
    <meta name="owner" content="Takipsan">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!--begin::Fonts -->
    <link href="/assets/css/fonts.css" rel="stylesheet" />
    <!--end::Fonts -->

    <!--begin::Page Vendors Styles(used by this page) -->
    <link href="/assets/vendors/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
    <!--end::Page Vendors Styles -->

    <!--begin:: Global Mandatory Vendors -->
    <link href="/assets/vendors/general/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" type="text/css" />
    <!--end:: Global Mandatory Vendors -->

    <!--begin:: Global Optional Vendors -->

    <link href="/assets/vendors/general/select2/dist/css/select2.css" rel="stylesheet" type="text/css" />
    <link href="/assets/vendors/general/owl.carousel/dist/assets/owl.theme.default.css" rel="stylesheet" type="text/css" />
    <link href="/assets/vendors/general/animate.css/animate.css" rel="stylesheet" type="text/css" />
    <link href="/assets/vendors/general/morris.js/morris.css" rel="stylesheet" type="text/css" />
    <link href="/assets/vendors/general/sweetalert2/dist/sweetalert2.css" rel="stylesheet" type="text/css" />
    <link href="/assets/vendors/general/socicon/css/socicon.css" rel="stylesheet" type="text/css" />
    <link href="/assets/vendors/custom/vendors/line-awesome/css/line-awesome.css" rel="stylesheet" type="text/css" />
    <link href="/assets/vendors/custom/vendors/flaticon/flaticon.css" rel="stylesheet" type="text/css" />
    <link href="/assets/vendors/custom/vendors/flaticon2/flaticon.css" rel="stylesheet" type="text/css" />
    <link href="/assets/vendors/general/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
    <!--end:: Global Optional Vendors -->

    <!--begin::Global Theme Styles(used by all pages) -->
    <link href="/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />

    <!--end::Global Theme Styles -->

    <!--begin::Layout Skins(used by all pages) -->
    <link href="/assets/css/skins/header/base/light.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/skins/header/menu/light.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/skins/brand/dark.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/skins/aside/dark.css" rel="stylesheet" type="text/css" />

    @yield('css')

<!--end::Layout Skins -->
    <link rel="shortcut icon" href="/assets/media/logos/favicon.ico" />

</head>

<!-- end::Head -->

<!-- begin::Body -->
<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--fixed kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading fason">
<!-- begin:: Page -->

<!-- begin::Page loader -->
<div class="kt-page-loader kt-page-loader--logo">
    <img alt="Logo" src="/assets/media/logos/logo-mini-md.png"/>
    <div class="kt-spinner kt-spinner--success"></div>
</div>
<!-- end::Page Loader -->

<!-- begin:: Header Mobile -->
<div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed ">
    <div class="kt-header-mobile__logo">
        <a href="/">
            <img alt="Logo" src="/assets/media/logos/logo-light.png" />
        </a>
    </div>
    <div class="kt-header-mobile__toolbar">
        <button class="kt-header-mobile__toggler kt-header-mobile__toggler--left" id="kt_aside_mobile_toggler"><span></span></button>
        <!--<button class="kt-header-mobile__toggler" id="kt_header_mobile_toggler"><span></span></button>-->
        <button class="kt-header-mobile__topbar-toggler" id="kt_header_mobile_topbar_toggler"><i class="flaticon-more"></i></button>
    </div>
</div>

<!-- end:: Header Mobile -->
<div class="kt-grid kt-grid--hor kt-grid--root">
    <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">

        <!-- begin:: Aside -->
        <button class="kt-aside-close " id="kt_aside_close_btn"><i class="la la-close"></i></button>
        <div class="kt-aside  kt-aside--fixed  kt-grid__item kt-grid kt-grid--desktop kt-grid--hor-desktop" id="kt_aside">

            <!-- begin:: Aside -->
            <div class="kt-aside__brand kt-grid__item " id="kt_aside_brand">
                <div class="kt-aside__brand-logo">
                    <a href="/">
                        <img alt="Logo" src="/upload/images/{{ getSettings('company_logo') }}" />
                    </a>
                </div>
                <div class="kt-aside__brand-tools">
                    <button class="kt-aside__brand-aside-toggler" id="kt_aside_toggler">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon id="Shape" points="0 0 24 0 24 24 0 24" />
                                    <path d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z" id="Path-94" fill="#000000" fill-rule="nonzero" transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999) " />
                                    <path d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z" id="Path-94" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999) " />
                                </g>
                            </svg>
                        </span>
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon id="Shape" points="0 0 24 0 24 24 0 24" />
                                    <path d="M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z" id="Path-94" fill="#000000" fill-rule="nonzero" />
                                    <path d="M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z" id="Path-94" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999) " />
                                </g>
                            </svg>
                        </span>
                    </button>

                    <!--
			<button class="kt-aside__brand-aside-toggler kt-aside__brand-aside-toggler--left" id="kt_aside_toggler"><span></span></button>
			-->
                </div>
            </div>

            <!-- end:: Aside -->

            <!-- begin:: Aside Menu -->
            <div class="kt-aside-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_aside_menu_wrapper">
                <div id="kt_aside_menu" class="kt-aside-menu " data-ktmenu-vertical="1" data-ktmenu-scroll="1" data-ktmenu-dropdown-timeout="500">
                    <ul class="kt-menu__nav ">
                        <li class="kt-menu__item  {{ request()->is('/') ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
                            <a href="/" class="kt-menu__link "><span class="kt-menu__link-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <polygon id="Bound" points="0 0 24 0 24 24 0 24" />
                                            <path d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z" id="Shape" fill="#000000" fill-rule="nonzero" />
                                            <path d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z" id="Path" fill="#000000" opacity="0.3" />
                                        </g>
                                    </svg>
                                </span>
                                <span class="kt-menu__link-text">@lang('portal.dashboard')</span>
                            </a>
                        </li>

                        @if(isset($glb_menus))
                            @foreach($glb_menus as $key => $value)
                                @if(MenuRoleCheck($value->roles, session('user_roles')))
                                    @if(isset($value->children) && count($value->children) > 0)
                                        <li class="kt-menu__item  kt-menu__item--submenu {{ ChildMenuCheckUrl(collect($value)) ? 'kt-menu__item--open kt-menu__item--active' : '' }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                                            <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                                                <span class="kt-menu__link-icon">
                                                    <i class="{{ $value->icon }}"></i>
                                                </span>
                                                <span class="kt-menu__link-text">{{ app()->getLocale() == 'tr' ? $value->title : $value->title_en }}</span>
                                                <i class="kt-menu__ver-arrow la la-angle-right"></i>
                                            </a>
                                            <div class="kt-menu__submenu "><span class="kt-menu__arrow"></span>
                                                <ul class="kt-menu__subnav">
                                                    <li class="kt-menu__item kt-menu__item--parent" aria-haspopup="true"><span class="kt-menu__link"><span class="kt-menu__link-text">{{ $value->title }}</span></span></li>
                                                    @foreach($value->children as $key2 => $value2)
                                                        @if(MenuRoleCheck($value2->roles, session('user_roles')))
                                                            <li class="kt-menu__item {{ isset($value2->children) && count($value2->children) > 0 ? 'kt-menu__item--submenu' : '' }} {{ ChildMenuCheckUrl(collect($value2)) ? 'kt-menu__item--open kt-menu__item--active' : '' }}" aria-haspopup="true">
                                                                @if(isset($value2->children) && count($value2->children) > 0)
                                                                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                                                                        <i class="kt-menu__link-bullet {{ $value2->icon }}"><span></span></i>
                                                                        <span class="kt-menu__link-text">{{ app()->getLocale() == 'tr' ? $value2->title : $value2->title_en }}</span>
                                                                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                                                                    </a>
                                                                    <div class="kt-menu__submenu "><span class="kt-menu__arrow"></span>
                                                                        <ul class="kt-menu__subnav">
                                                                            @foreach($value2->children as $key3 => $value3)
                                                                                @if(MenuRoleCheck($value3->roles, session('user_roles')))
                                                                                    <li class="kt-menu__item {{ ChildMenuCheckUrl(collect($value3)) ? 'kt-menu__item--active' : '' }}" aria-haspopup="true"><a href="{{ url('/'.$value3->uri) }}" class="kt-menu__link "><i class="{{ $value3->icon }}"><span></span></i><span class="kt-menu__link-text">{{ app()->getLocale() == 'tr' ? $value3->title : $value3->title_en }}</span></a></li>
                                                                                @endif
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                @else
                                                                    <a href="{{ url('/'.$value2->uri) }}" class="kt-menu__link ">
                                                                        <i class="kt-menu__link-bullet {{ $value2->icon }}"><span></span></i>&nbsp;
                                                                        <span class="kt-menu__link-text">{{ app()->getLocale() == 'tr' ? $value2->title : $value2->title_en}}</span>
                                                                    </a>
                                                                @endif
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </li>
                                    @else
                                        {{--
                                            todo;
                                            destek talebini şimdilik gösterme
                                             kullanıcılar, siparişler menülerini de gösterme
                                        --}}
                                        @if ($value->uri != "support")
                                            <li class="kt-menu__item {{ request()->is($value->uri) ? 'kt-menu__item--active' : '' }} {{ ChildMenuCheckUrl(collect($value)) ? 'kt-menu__item--open kt-menu__item--active' : '' }}" aria-haspopup="true">
                                                <a href="{{ url('/'.$value->uri) }}" class="kt-menu__link ">
                                                    <span class="kt-menu__link-icon">
                                                        <i class="{{ $value->icon }}"></i>
                                                    </span>
                                                    <span class="kt-menu__link-text">{{ app()->getLocale() == 'tr' ? $value->title : $value->title_en }}</span>
                                                </a>
                                            </li>
                                        @endif
                                    @endif
                                @endif
                            @endforeach
                        @endif

                    </ul>
                </div>
            </div>

            <!-- end:: Aside Menu -->
        </div>

        <!-- end:: Aside -->
        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">

            <!-- begin:: Header -->
            <div id="kt_header" class="kt-header kt-grid__item  kt-header--fixed ">

                <!-- begin:: Header Menu -->
                <button class="kt-header-menu-wrapper-close" id="kt_header_menu_mobile_close_btn"><i class="la la-close"></i></button>
                <div class="kt-header-menu-wrapper" id="kt_header_menu_wrapper">
                    <div id="kt_header_menu" class="kt-header-menu kt-header-menu-mobile  kt-header-menu--layout-default ">
                        @if(session('main_company_id') > 0)
                            <a href="{{ route('station.index') }}" class="btn btn-warning btn-elevate btn-circle btn-icon mt-3"><i class="flaticon-technology-2"></i></a>
                        @endif
                    </div>
                </div>
                <!-- end:: Header Menu -->

                <!-- begin:: Header Topbar -->
                <div class="kt-header__topbar">

                    <!--begin: Language bar -->
                    <div class="kt-header__topbar-item kt-header__topbar-item--langs">
                        <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px,0px">
                            <span class="kt-header__topbar-icon">
                                <img class="" src="/assets/media/flags/{{ app()->getLocale() }}-flag.svg" alt="" />
                            </span>
                        </div>
                        <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround">
                            <ul class="kt-nav kt-margin-t-10 kt-margin-b-10">
                                @foreach($glb_locales as $key => $value)
                                    <li class="kt-nav__item {{ $value->abbr == app()->getLocale() ? 'kt-nav__item--active' : ''}}">
                                        <a href="{{ url($value->abbr) }}" class="kt-nav__link">
                                            <span class="kt-nav__link-icon"><img src="/assets/media/flags/{{ $value->abbr }}-flag.svg" alt="" /></span>
                                            <span class="kt-nav__link-text">{{ app()->getLocale() == 'tr' ? $value->title : $value->title_glb }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <!--end: Language bar -->

                    <!--begin: User Bar -->
                    <div class="kt-header__topbar-item kt-header__topbar-item--user">
                        <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="0px,0px">
                            <div class="kt-header__topbar-user">
                                <span class="kt-header__topbar-welcome kt-hidden-mobile">@lang('portal.hi'),</span>
                                <span class="kt-header__topbar-username kt-hidden-mobile">{{ auth()->user()->username }}</span>
                                <img class="kt-hidden" alt="Pic" src="/assets/media/users/300_25.jpg" />

                                <!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
                                <span class="kt-badge kt-badge--username kt-badge--unified-success kt-badge--lg kt-badge--rounded kt-badge--bold">{{ strtoupper(substr(auth()->user()->username, 0, 1)) }}</span>
                            </div>
                        </div>
                        <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-xl">

                            <!--begin: Head -->
                            <div class="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x" style="background-image: url(/assets/media/misc/bg-1.jpg)">
                                <div class="kt-user-card__avatar">
                                    <img class="kt-hidden" alt="Pic" src="/assets/media/users/300_25.jpg" />

                                    <!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
                                    <span class="kt-badge kt-badge--lg kt-badge--rounded kt-badge--bold kt-font-success">{{ strtoupper(substr(auth()->user()->username, 0, 1)) }}</span>
                                </div>
                                <div class="kt-user-card__name">
                                    {{ auth()->user()->main_company_id }}
                                </div>
                                <div class="kt-user-card__badge">
                                    <span class="btn btn-success btn-sm btn-bold btn-font-md">{{ auth()->user()->username }}</span>
                                </div>
                            </div>
                            <!--end: Head -->

                            <!--begin: Navigation -->
                            <div class="kt-notification">
                                <a href="{{ route('profile.show') }}" class="kt-notification__item">
                                    <div class="kt-notification__item-icon">
                                        <i class="flaticon2-calendar-3 kt-font-success"></i>
                                    </div>
                                    <div class="kt-notification__item-details">
                                        <div class="kt-notification__item-title kt-font-bold">
                                            @lang('portal.my_profile')
                                        </div>
                                        <div class="kt-notification__item-time">
                                            @lang('portal.my_profile_text')
                                        </div>
                                    </div>
                                </a>
                                <a href="{{ route('profile.edit_password') }}" class="kt-notification__item">
                                    <div class="kt-notification__item-icon">
                                        <i class="flaticon2-calendar-3 kt-font-success"></i>
                                    </div>
                                    <div class="kt-notification__item-details">
                                        <div class="kt-notification__item-title kt-font-bold">
                                            @lang('portal.update_password')
                                        </div>
                                    </div>
                                </a>
                                @if(roleCheck(config('settings.roles.admin')))
                                    <a href="{{ route('settings.index') }}" class="kt-notification__item">
                                        <div class="kt-notification__item-icon">
                                            <i class="flaticon2-settings kt-font-warning"></i>
                                        </div>
                                        <div class="kt-notification__item-details">
                                            <div class="kt-notification__item-title kt-font-bold">
                                                @lang('portal.settings')
                                            </div>
                                            <div class="kt-notification__item-time">
                                                @lang('portal.settings_text')
                                            </div>
                                        </div>
                                    </a>
                                @endif
                                <div class="kt-notification__custom kt-space-between">
                                    <a href="{{ route('logout') }}" class="btn btn-label btn-label-brand btn-sm btn-bold">@lang('portal.logout')</a>
                                </div>
                            </div>

                            <!--end: Navigation -->
                        </div>
                    </div>

                    <!--end: User Bar -->
                </div>

                <!-- end:: Header Topbar -->
            </div>

            <!-- end:: Header -->

            @yield('content')

            <!-- begin:: Footer -->
            <div class="kt-footer  kt-grid__item kt-grid kt-grid--desktop kt-grid--ver-desktop" id="kt_footer">
                <div class="kt-container  kt-container--fluid ">
                    <div class="kt-footer__copyright">
                        2019&nbsp;&copy;&nbsp;<a href="{{ config('settings.footer.company_link') }}" target="_blank" class="kt-link">{{ config('settings.footer.company') }}</a>
                    </div>
                    <div class="kt-footer__menu">
                        <a href="https://www.takipsan.com/about-us" target="_blank" class="kt-footer__menu-link kt-link">@lang('portal.about_us')</a>
                        <a href="https://www.takipsan.com/contact" target="_blank" class="kt-footer__menu-link kt-link">@lang('portal.contact_us')</a>
                    </div>
                </div>
            </div>
            <!-- end:: Footer -->
        </div>
    </div>
</div>

<!-- end:: Page -->

<!-- begin::Scrolltop -->
<div id="kt_scrolltop" class="kt-scrolltop">
    <i class="fa fa-arrow-up"></i>
</div>

<!-- end::Scrolltop -->

<!-- begin::Sticky Toolbar -->
@if(roleCheck(config('settings.roles.admin')))
    <ul class="kt-sticky-toolbar" style="margin-top: 30px;">
        <li class="kt-sticky-toolbar__item kt-sticky-toolbar__item--brand" data-toggle="kt-tooltip" title="@lang('portal.settings')" data-placement="left">
            <a href="{{ route('settings.index') }}"><i class="flaticon2-gear"></i></a>
        </li>
    </ul>
@endif
<!-- end::Sticky Toolbar -->

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
<script src="/assets/vendors/general/tooltip.js/dist/umd/tooltip.min.js" type="text/javascript"></script>
<script src="/assets/vendors/general/perfect-scrollbar/dist/perfect-scrollbar.js" type="text/javascript"></script>
<script src="/assets/vendors/general/sticky-js/dist/sticky.min.js" type="text/javascript"></script>
<!--end:: Global Mandatory Vendors -->

<!--begin:: Global Optional Vendors -->
<script src="/assets/vendors/general/block-ui/jquery.blockUI.js" type="text/javascript"></script>
<script src="/assets/vendors/general/select2/dist/js/select2.full.js" type="text/javascript"></script>
<script src="/assets/vendors/general/typeahead.js/dist/typeahead.bundle.js" type="text/javascript"></script>
<script src="/assets/vendors/general/handlebars/dist/handlebars.js" type="text/javascript"></script>
<script src="/assets/vendors/general/inputmask/dist/jquery.inputmask.bundle.js" type="text/javascript"></script>
<script src="/assets/vendors/general/owl.carousel/dist/owl.carousel.js" type="text/javascript"></script>
<script src="/assets/vendors/general/quill/dist/quill.js" type="text/javascript"></script>
<script src="/assets/vendors/general/@yaireo/tagify/dist/tagify.min.js" type="text/javascript"></script>
<script src="/assets/vendors/general/markdown/lib/markdown.js" type="text/javascript"></script>
<script src="/assets/vendors/general/bootstrap-markdown/js/bootstrap-markdown.js" type="text/javascript"></script>
<script src="/assets/vendors/custom/js/vendors/bootstrap-markdown.init.js" type="text/javascript"></script>
<script src="/assets/vendors/general/bootstrap-notify/bootstrap-notify.min.js" type="text/javascript"></script>
<script src="/assets/vendors/custom/js/vendors/bootstrap-notify.init.js" type="text/javascript"></script>
<script src="/assets/vendors/general/jquery-validation/dist/jquery.validate.js" type="text/javascript"></script>
<script src="/assets/vendors/general/jquery-validation/dist/additional-methods.js" type="text/javascript"></script>
<script src="/assets/vendors/general/raphael/raphael.js" type="text/javascript"></script>
<script src="/assets/vendors/general/morris.js/morris.js" type="text/javascript"></script>
<script src="/assets/vendors/general/chart.js/dist/Chart.bundle.js" type="text/javascript"></script>

<script src="/assets/vendors/general/waypoints/lib/jquery.waypoints.js" type="text/javascript"></script>
<script src="/assets/vendors/general/counterup/jquery.counterup.js" type="text/javascript"></script>
<script src="/assets/vendors/general/es6-promise-polyfill/promise.min.js" type="text/javascript"></script>
<script src="/assets/vendors/general/sweetalert2/dist/sweetalert2.min.js" type="text/javascript"></script>
<script src="/assets/vendors/custom/js/vendors/sweetalert2.init.js" type="text/javascript"></script>
<script src="/assets/vendors/general/jquery.repeater/src/jquery.input.js" type="text/javascript"></script>
<script src="/assets/vendors/general/jquery.repeater/src/repeater.js" type="text/javascript"></script>
<!--end:: Global Optional Vendors -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
<!--begin::Global Theme Bundle(used by all pages) -->
<script src="/assets/js/scripts.bundle.js" type="text/javascript"></script>
<!--end::Global Theme Bundle -->

<!--begin::Page Vendors(used by this page) -->
<script src="/assets/vendors/custom/gmaps/gmaps.js" type="text/javascript"></script>

<script src="/station/js/axios.min.js"></script>

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

<!--end::Page Vendors -->

<!--begin::Page Scripts(used by this page) -->
@yield('js')

<script src="/assets/js/delete.js"></script>
<!--end::Page Scripts -->
</body>

<!-- end::Body -->
</html>
