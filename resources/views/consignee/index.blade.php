@extends('layouts.main')

@section('content')
 
<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

    <!-- begin:: Subheader -->
    <div class="kt-subheader kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">
                    @lang('portal.firms_to_ship')
                </h3>
                <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                <div class="kt-subheader__group" id="kt_subheader_search">
                    <span class="kt-subheader__desc" id="kt_subheader_total">@lang('portal.list')</span>
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
                        @lang('portal.company_list')
                    </h3>
                </div>
                @if(!roleCheck(config('settings.roles.anaUretici')))
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                            <a href="{{ route('consignee.create') }}" class="btn btn-brand btn-elevate btn-icon-sm">
                                <i class="la la-plus"></i>
                                @lang('portal.add_new_consignee')
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="kt-portlet__body">
                <!--begin: Datatable -->
                <table class="table table-striped- table-bordered table-hover table-checkable" id="consigneeList">
                    <thead>
                    <tr>
                        <th width="100">@lang('portal.image')</th>
                        <th>@lang('portal.consignee_name')</th>
                        <th>@lang('portal.shipped_companies')</th>
                        <th width="100">@lang('portal.status')</th>
                        @if(!roleCheck(config('settings.roles.anaUretici')))
                        <th width="100">@lang('portal.actions')</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($consignees as $key => $value)
                       
                        <tr>
                            <td align="center">
                                <img src="{{ config('settings.media.consignees.full_path') . $value->logo }}" class="m-img-rounded kt-marginless" alt="{{ $value->name }}" width="75">
                            </td>
                            <td>{{ $value->name }}</td>
                            <td>
                                @if(isset($consignees->company_array))
                                @foreach($value->companies as $value2)
                                <span class="badge badge-info">{!! in_array($value2->id,$consignees->company_array) ? $value2->name : '' !!}</span>
                                @endforeach
                                @else
                                @foreach($value->companies as $value2)
                                <span class="badge badge-info">{{ $value2->name }}</span>
                                @endforeach
                                @endif
                            </td>
                            <td align="center">{!! $value->status == 1 ? '<span class="badge badge-success">'.trans(config('settings.form_static.status.'.$value->status)).'</span>' : '<span class="badge badge-danger">'.trans(config('settings.form_static.status.'.$value->status)).'</span>' !!}</td>
                            @if(!roleCheck(config('settings.roles.anaUretici')))
                            <td align="center">
                               <span class="dropdown">
                                    <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                                      <i class="la la-cogs"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="{{ route('consignee.edit', $value->id) }}"><i class="la la-edit"></i> @lang('portal.edit')</a>
                                        <a class="dropdown-item" href="{{ route('consignee.destroy', $value->id) }}" data-method="delete" data-token="{{csrf_token()}}" data-confirm="@lang('portal.delete_text')"><i class="la la-trash"></i> @lang('portal.delete')</a>
                                    </div>
                                </span>
                            </td>
                            @endif
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
            $('#consigneeList').DataTable({
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
                drawCallback: function () {
                    $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');
                },
                preDrawCallback: function() {
                    $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
                }
            });
        });
    </script>

@endsection
