@extends('layouts.main')

@section('content')

<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

    <!-- begin:: Subheader -->
    <div class="kt-subheader kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <button class="kt-subheader__mobile-toggle kt-subheader__mobile-toggle--left" id="kt_subheader_mobile_toggle"><span></span></button>
                <h3 class="kt-subheader__title">@lang('portal.devices')</h3>
                <span class="kt-subheader__separator kt-hidden"></span>
                <div class="kt-subheader__breadcrumbs">
                    <a href="{{ url('/') }}" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
                    <span class="kt-subheader__breadcrumbs-separator"></span>
                    <a href="{{ route('company.index') }}" class="kt-subheader__breadcrumbs-link">@lang('portal.companies')</a>
                    <span class="kt-subheader__breadcrumbs-separator"></span>
                    <a href="{{ route('company.show', $company->id) }}" class="kt-subheader__breadcrumbs-link">{{ $company->name }}</a>
                    <span class="kt-subheader__breadcrumbs-separator"></span>
                    <a href="#" class="kt-subheader__breadcrumbs-link">@lang('portal.device_list')</a>
                </div>
            </div>
            <div class="kt-subheader__toolbar">
                <a href="javascript:history.go(-1);" class="btn btn-default btn-bold"><i class="la la-angle-left"></i> @lang('portal.back') </a>
            </div>
        </div>
    </div>
    <!-- end:: Subheader -->

    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                        <i class="kt-font-brand flaticon2-laptop"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">
                        @lang('portal.device_list')
                    </h3>
                </div>
                {{-- 
                @if(roleCheck(config('settings.roles.admin')) || roleCheck(config('settings.roles.partner')))
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">&nbsp;
                            <a href="{{ route('company.device.create', $company->id) }}" class="btn btn-brand btn-elevate btn-icon-sm">
                                <i class="la la-plus"></i>
                                @lang('portal.add_new_device')
                            </a>
                        </div>
                    </div>
                </div>
                @endif
                --}}
            </div>
            <div class="kt-portlet__body">
                <!--begin: Datatable -->
                <table class="table table-striped- table-bordered table-hover table-checkable" id="deviceList">
                    <thead>
                    <tr>
                        <th width="100">@lang('portal.image')</th>
                        <th>@lang('portal.device_name')</th>
                        <th>@lang('portal.device_type')</th>
                        <th width="100">@lang('portal.status')</th>
                        <th width="100">@lang('portal.actions')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($company->devices as $key => $value)
                        <tr>
                            <td align="center">
                                <img src="{{ config('settings.devices.' . $value->device_type . '.logo')}}" class="m-img-rounded kt-marginless" alt="" width="75">
                            </td>
                            <td> {{ $value->name }}</td>
                            <td> {{ config('settings.devices.' . $value->device_type . '.name')}}</td>
                            <td align="center">{!! $value->status == 1 ? '<span class="badge badge-success">'.trans(config('settings.form_static.status.'.$value->status)).'</span>' : ($value->status == 2 ? '<span class="badge badge-warning">Onay Bekliyor</span>' : '<span class="badge badge-danger">'.trans(config('settings.form_static.status.'.$value->status)).'</span>') !!}</td>
                            <td align="center">
                               <span class="dropdown">
                                    <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                                      <i class="la la-cogs"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        {{--<a class="dropdown-item" href="{{ route('company.device.edit', $value->id) }}"><i class="la la-edit"></i> @lang('portal.edit')</a>--}}
                                        @if($value->status==1) 
                                            <a class="dropdown-item" href="{{ route('company.device.devicePassive', $value->id) }}" data-token="{{csrf_token()}}" data-confirm="@lang('portal.are_you_sure_deactivate_device')"><i class="flaticon-exclamation"></i> @lang('portal.deactivate')</a>
                                        @else
                                            <a class="dropdown-item" href="{{ route('company.device.deviceActive', $value->id) }}"><i class="flaticon-exclamation"></i> @lang('portal.activate')</a>
                                        @endif
                                        <a class="dropdown-item" href="{{ route('company.device.destroy', $value->id) }}" data-method="delete" data-token="{{csrf_token()}}" data-confirm="@lang('portal.delete_text')"><i class="la la-trash"></i> @lang('portal.delete')</a>
                                    </div>
                                </span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <!--end: Datatable -->
            </div>
        </div>
    </div>
</div>

@endsection

@section('css')
    <link href="/assets/vendors/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />

@endsection

@section('js')
    <script src="/assets/vendors/custom/datatables/datatables.bundle.js" type="text/javascript"></script>
    <script type="text/javascript">
        $( document ).ready(function() {
            $('#deviceList').DataTable({
                "order": [],
                "aoColumnDefs": [
                    { 'orderable': false, 'aTargets': [0,3,4] }
                ],
                "pageLength": 50,
                @if(app()->getLocale() == 'tr')
                "language": {
                    "url": "{{ asset('/assets/vendors/custom/datatables/locale/tr.json') }}"
                },
                @endif
            });
        });
    </script>

@endsection
