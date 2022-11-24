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
                    <span class="kt-subheader__desc" id="kt_subheader_total">@lang('portal.device_list')</span>
                </div>
            </div>
            <div class="kt-subheader__toolbar"></div>
        </div>
    </div>

    <!-- end:: Subheader -->

    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                        <i class="kt-font-brand flaticon2-line-chart"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">
                        @lang('portal.device_list')
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                            <a href="{{ route('device.create') }}" class="btn btn-brand btn-elevate btn-icon-sm">
                                <i class="la la-plus"></i>
                                @lang('portal.add_new_device')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <!--begin: Datatable -->
                <table class="table table-striped- table-bordered table-hover table-checkable" id="deviceList">
                    <thead>
                    <tr>
                        <th width="100">@lang('portal.image')</th>
                        <th>@lang('portal.device_type')</th>
                        <th>@lang('portal.device_name')</th>
                        <th>@lang('portal.company_name')</th>
                        <th>@lang('portal.license_status')</th>
                        <th width="100">@lang('portal.status')</th>
                        <th width="100">@lang('portal.actions')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($devices as $key => $value)
                        <tr>
                            <td align="center">
                                <img src="{{ config('settings.devices.' . $value->device_type . '.logo')}}" class="m-img-rounded kt-marginless" alt="" width="75">
                            </td>
                            <td>{{ config('settings.devices.' . $value->device_type . '.name')}}</td>
                            <td>{{ $value->name }}</td>
                            <td>{{ $value->company->name }}</td>
                            <td>{{ $value->start_at.' - '.$value->finish_at }}</td>
                            <td align="center">{!! $value->status == 1 ? '<span class="badge badge-success">'.trans(config('settings.form_static.status.'.$value->status)).'</span>' : ($value->status == 2 ? '<span class="badge badge-warning">Onay Bekliyor</span>' : '<span class="badge badge-danger">'.trans(config('settings.form_static.status.'.$value->status)).'</span>') !!}</td>
                            <td align="center">
                               <span class="dropdown">
                                    <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                                      <i class="la la-cogs"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="{{ route('device.edit', $value->id) }}"><i class="la la-edit"></i> @lang('portal.edit')</a>
                                        <a class="dropdown-item" href="{{ route('device.destroy', $value->id) }}" data-method="delete" data-token="{{csrf_token()}}" data-confirm="@lang('portal.delete_text')"><i class="la la-trash"></i> @lang('portal.delete')</a>
                                        @if($value->status==1)
                                            <a class="dropdown-item" href="{{ route('device.devicePassive', $value->id) }}" data-token="{{csrf_token()}}" data-confirm="@lang('portal.are_you_sure_deactivate_device')"><i class="flaticon-exclamation"></i> @lang('portal.deactivate')</a>
                                        @endif
                                        @if($value->status==2 || $value->status==0)
                                            <a class="dropdown-item" href="{{ route('device.deviceActive', $value->id) }}"><i class="flaticon-exclamation"></i> @lang('portal.activate')</a>
                                        @endif
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
                    { 'orderable': false, 'aTargets': [0,4,5] }
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
