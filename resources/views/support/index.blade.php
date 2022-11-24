@extends('layouts.main')

@section('content')

    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

        <!-- begin:: Subheader -->
        <div class="kt-subheader kt-grid__item" id="kt_subheader">
            <div class="kt-container  kt-container--fluid ">
                <div class="kt-subheader__main">
                    <h3 class="kt-subheader__title">
                        @lang('portal.support')
                    </h3>
                    <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                    <div class="kt-subheader__group" id="kt_subheader_search">
                        <span class="kt-subheader__desc" id="kt_subheader_total">@lang('portal.list')</span>
                    </div>
                </div>
                <div class="kt-subheader__toolbar">
                </div>
            </div>
        </div>
        <!-- end:: Subheader -->

        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                        <i class="kt-font-brand flaticon-users"></i>
                    </span>
                        <h3 class="kt-portlet__head-title">
                            @lang('portal.support')
                        </h3>
                    </div>
                    <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-wrapper">
                            <div class="kt-portlet__head-actions">&nbsp;
                                <a href="{{ route('support.create') }}" class="btn btn-brand btn-elevate btn-icon-sm">
                                    <i class="la la-plus"></i>
                                    @lang('portal.add')
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    api üzerinden acılmıs olan ticketler listelenecek..
                    <!--begin: Datatable -->
                    {{-- */
                    <table class="table table-striped- table-bordered table-hover table-checkable" id="viewList">
                        <thead>
                        <tr>
                            <th>Season</th>
                            <th>@lang('portal.desc')</th>
                            <th>Upc</th>
                            <th>@lang('portal.create_date')</th>
                            <th>@lang('portal.update_date')</th>
                            <th width="100">@lang('portal.actions')</th>
                        </tr>
                        </thead>
                    </table> /* --}}
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
    {{-- */
    <script type="text/javascript">
        $(function() {
            $('#viewList').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('ms_size_detail.datatable') }}",
                columns: [
                    {data: 'season', name: 'season'},
                    {data: 'description', name: 'description'},
                    {data: 'upc', name: 'upc'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'updated_at', name: 'updated_at'},
                    {data: 'action', name: 'action', className: 'text-center', orderable: false, searchable: false, responsivePriority: -1}
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
    </script> /* --}}
@endsection
