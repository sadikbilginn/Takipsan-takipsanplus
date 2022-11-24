@extends('layouts.main')

@section('content')

<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

    <!-- begin:: Subheader -->
    <div class="kt-subheader kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">
                    @if(roleCheck(config('settings.roles.admin')) || roleCheck(config('settings.roles.partner')))
                        @lang('portal.main_companies')
                    @elseif(roleCheck(config('settings.roles.anaUretici')))
                        @lang('portal.companies')
                    @endif
                </h3>
                <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                <div class="kt-subheader__group" id="kt_subheader_search">
                    <span class="kt-subheader__desc" id="kt_subheader_total">
                    @if(roleCheck(config('settings.roles.admin')) || roleCheck(config('settings.roles.partner')))
                        @lang('portal.main_companies_list')
                    @elseif(roleCheck(config('settings.roles.anaUretici')))
                        @lang('portal.company_list')
                    @endif
                    </span>
                </div>
            </div>
            <div class="kt-subheader__toolbar">
                <a href="{{ route('company.create') }}" class="btn btn-label-brand btn-bold">
                    <i class="kt-nav__link-icon flaticon2-plus"></i>
                    <span>@lang('portal.add_new_company')</span>
                </a>
            </div>
        </div>
    </div>
    <!-- end:: Subheader -->

    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <!--Begin::Section-->
        <div class="row"> 
            @foreach($companies as $key => $value)
                <div class="col-xl-4">
                <!--begin:: Portlet-->
                <div class="kt-portlet kt-portlet--height-fluid">
                    <div class="kt-portlet__body kt-portlet__body--fit">
                        <!--begin::Widget -->
                        <div class="kt-widget kt-widget--project-1">
                            <div style='padding-bottom:2px;' class="kt-widget__head">
                                <div class="kt-widget__label">
                                    <div class="kt-widget__media">
                                        <span class="kt-media kt-media--lg kt-media--circle">
                                        @if($value->logo)
                                            <img src="{{ config('settings.media.companies.full_path') . $value->logo }}" alt="image">
                                        @endif
                                        @if($value->logo == '')
                                            <img src="upload/images/companies/takipsan.jpg" alt="image">
                                        @endif
                                        </span>
                                    </div>
                                    <div style='font-size:12px;' class="kt-widget__info kt-margin-t-5">
                                        <a style='font-size:12px;' href="{{ route('company.show', $value->id) }}" class="kt-widget__title">
                                            {{ $value->name }}
                                            {!! $value->status == true ? '<i class="flaticon2-correct kt-font-success"></i>' : '<i class="flaticon2-correct kt-font-danger"></i>' !!} {!! $value->isPartner == 1 ? '<br>(Partner)' : '' !!}
                                        </a>
                                        <span style='font-size:12px;' class="kt-widget__desc">
                                            @if(roleCheck(config('settings.roles.admin')))
                                            {!! $value->isCreatedByPartner == 1 ? '(Partner tarafından eklendi)' : '' !!}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="kt-portlet__head-toolbar">
                                    <a href="#" class="btn btn-clean btn-sm btn-icon btn-icon-md" data-toggle="dropdown">
                                        <i class="flaticon-more-1"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right">
                                        <ul class="kt-nav">
                                            @if(roleCheck(config('settings.roles.anaUretici')))
                                            <li class="kt-nav__item">
                                                <a href="{{ route('company.device.index', $value->id) }}" class="kt-nav__link">
                                                    <i class="kt-nav__link-icon flaticon2-laptop"></i>
                                                    <span class="kt-nav__link-text">@lang('portal.devices')</span>
                                                </a>
                                            </li>
                                            <li class="kt-nav__item">
                                                <a href="{{ route('consignment.index', ['company' => $value->id]) }}" class="kt-nav__link">
                                                    <i class="kt-nav__link-icon flaticon2-delivery-truck"></i>
                                                    <span class="kt-nav__link-text">@lang('portal.consignments')</span>
                                                </a>
                                            </li>
                                            @endif
                                            <li class="kt-nav__item">
                                                <a href="{{ route('company.edit', $value->id) }}" class="kt-nav__link">
                                                    <i class="kt-nav__link-icon flaticon2-contract"></i>
                                                    <span class="kt-nav__link-text">@lang('portal.edit')</span>
                                                </a>
                                            </li>
                                            <li class="kt-nav__item">
                                                <a href="{{ route('company.destroy', $value->id) }}" class="kt-nav__link" data-method="delete" data-token="{{csrf_token()}}" data-confirm="@lang('portal.delete_text')">
                                                    <i class="kt-nav__link-icon flaticon2-trash"></i>
                                                    <span class="kt-nav__link-text">@lang('portal.delete')</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="kt-widget__body">
                                {{--
                                <div class="kt-widget__stats">
                                    <div class="kt-widget__item flex-fill">
                                        <span class="kt-widget__subtitel">@lang('portal.c_completion_rate')</span>
                                        <div class="kt-widget__progress d-flex  align-items-center">
                                            @php $comStatus = consignmentStatusPercent(count($value->consignments->where('status', 0)), count($value->consignments)); @endphp
                                            <div class="progress" style="height: 5px;width: 100%;">
                                                <div class="progress-bar {{ consignmentProgressBg($comStatus) }}" role="progressbar" style="width: {{ $comStatus }}%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span class="kt-widget__stat">
                                                {{ $comStatus }}%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                --}}
                                <div class="kt-widget__content">
                                    <div class="kt-widget__details">
                                        <span class="kt-widget__subtitle">@lang('portal.phone')</span>
                                        <span class="">{{ $value->phone }}</span>
                                    </div>
                                    <div class="kt-widget__details">
                                        <span class="kt-widget__subtitle">@lang('portal.email')</span>
                                        <span class="">{{ $value->email }}</span>
                                    </div>
                                    <div class="kt-widget__section" style="align:right">
                                        <span class="kt-widget__subtitle"><br></span>
                                        <span class="">
                                            @if(!roleCheck(config('settings.roles.anaUretici')))
                                                <a href="{{ route('company.subcompanyindex', ['company' => $value->id]) }}" class="btn btn-brand btn-xs btn-upper btn-bold">
                                                    <i class="flaticon-buildings"></i><span class="kt-widget__value"> {{ $value->subComCount }} @lang('portal.company')</span>
                                                </a>
                                            @else
                                                <a href="{{ route('company.show', $value->id) }}" class="btn btn-brand btn-xs btn-upper btn-bold"><i class="fa fa-search"></i> @lang('portal.details')</a>
                                            @endif
                                        </span>
                                    </div>
                                    {{--<div class="kt-widget__details">
                                        <span class="kt-widget__subtitle">@lang('portal.coordinate')</span>
                                        <span class="">{{ $value->latitude }} , {{ $value->longitude }}</span>
                                    </div>
                                    <div class="kt-widget__details">
                                        <span class="kt-widget__subtitle">@lang('portal.address')</span>
                                        <span class="">{{ $value->address }}</span>
                                    </div>--}}
                                </div>
                            </div>
                            <!--<div class="kt-widget__footer">
                                <div class="kt-widget__wrapper">
                                    <div class="kt-widget__section">
                                        <a class="kt-widget__blog" href="{{ route('company.device.index', $value->id) }}">
                                            <i class="flaticon2-laptop"></i>
                                            <span class="kt-widget__value"> {{ count($value->devices) }} @lang('portal.device')</span>
                                        </a>
                                        {{-- 
                                        <a class="kt-widget__blog" href="{{ route('consignment.index', ['company' => $value->id]) }}">
                                            <i class="flaticon2-delivery-truck"></i>
                                            <span class="kt-widget__value"> {{ count($value->consignments) }} @lang('portal.consignment')</span>
                                        </a>
                                        
                                        @if(!roleCheck(config('settings.roles.anaUretici')))
                                        <a class="kt-widget__blog" href="{{ route('company.subcompanyindex', ['company' => $value->id]) }}">
                                            <i class="flaticon-buildings"></i>
                                            <span class="kt-widget__value"> {{ $value->subComCount }} @lang('portal.company')</span>
                                        </a>
                                        @endif
                                        --}}
                                    </div>
                                    
                                </div>
                            </div>-->
                        </div>
                        <!--end::Widget -->
                    </div>
                </div>
                <!--end:: Portlet-->
            </div>
            @endforeach
        </div>
        <!--End::Section-->

    </div>
</div>

@endsection

@section('css')

@endsection

@section('js')

@endsection
