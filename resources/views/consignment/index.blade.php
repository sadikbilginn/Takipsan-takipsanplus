@extends('layouts.main')

@section('content')
 
<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

    <!-- begin:: Subheader -->
    <div class="kt-subheader kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">
                    @lang('portal.consignments')
                </h3>
                <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                <div class="kt-subheader__group" id="kt_subheader_search">
                    <span class="kt-subheader__desc" id="kt_subheader_total">@lang('portal.consignmet_list')</span>
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
                        <i class="kt-font-brand flaticon2-lorry"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">
                        @lang('portal.consignmet_list')
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    @if(roleCheck(config('settings.roles.uretici')))
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                            <a href="{{ route('consignment.create') }}" class="btn btn-brand btn-elevate btn-icon-sm">
                                <i class="la la-plus"></i>
                                @lang('portal.new_consignment')
                            </a>
                        </div>
                    </div>
                    @else
                    <span class="kt-widget__text">
                     @lang('portal.only_manufacturer_add_consignment')
                    </span>
                    @endif
                </div>
            </div>
            <div class="kt-portlet__body">
                <table class="table table-striped- table-bordered table-hover table-checkable" id="consignmentList">
                    <thead>
                        <tr> 
                            <th>@lang('portal.po_no')</th>
                            <th>@lang('portal.company')</th>
                            <th>@lang('portal.consignee_name')</th>
                            <th>@lang('portal.delivery_date')</th>
                            <th>@lang('portal.status')</th>
                            <th>@lang('portal.c_completion_rate')</th>
                            <th width="100">@lang('portal.actions')</th>
                        </tr>
                    </thead>
                </table>
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
           $(function(){

               var table = $('#consignmentList').DataTable({
                   responsive: true,
                   processing: true,
                   serverSide: true,
                   ajax: "{{ $consignmentDatatableLink }}",
                   order: [],
                   columns: [
                       {data: 'name', name: 'name'},
                       {data: 'company', name: 'company', orderable: false},
                       {data: 'consignee', name: 'consignee', orderable: false},
                       {data: 'delivery_date', name: 'delivery_date', className: 'text-center'},
                       {data: 'status', name: 'status', className: 'text-center'},
                       {data: 'item_count', name: 'item_count', orderable: false, searchable: false},
                       {data: 'action', name: 'action', orderable: false, searchable: false, responsivePriority: -1, className: 'text-center'}
                   ],
                   @if(app()->getLocale() == 'tr')
                   language: {
                       "url": "{{ asset('/assets/vendors/custom/datatables/locale/tr.json') }}"
                   },
                   @endif
                   initComplete: function () {
                       $.getScript("/assets/js/delete.js");
                   }
               });
           });
    </script>
@endsection
