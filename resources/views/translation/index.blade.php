@extends('layouts.main')

@section('content')

<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

    <!-- begin:: Subheader -->
    <div class="kt-subheader kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">
                    @lang('portal.translation')
                </h3>
                <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                <div class="kt-subheader__group" id="kt_subheader_search">
                    <span class="kt-subheader__desc" id="kt_subheader_total">@lang('portal.translation_list')</span>
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
                        <i class="kt-font-brand flaticon2-line-chart"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">
                        @lang('portal.translation_list')
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                            <div class="dropdown dropdown-inline">
                                <!-- <button type="button" class="btn btn-default btn-icon-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="la la-download"></i> @lang('portal.generate')
                                </button> -->
                                <div class="dropdown-menu dropdown-menu-right">
                                    <ul class="kt-nav">
                                        <li class="kt-nav__section kt-nav__section--first">
                                            <span class="kt-nav__section-text">@lang('portal.choose')</span>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="#" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la la-print"></i>
                                                <span class="kt-nav__link-text">Print</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="#" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la la-file-excel-o"></i>
                                                <span class="kt-nav__link-text">Excel</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="#" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la la-file-text-o"></i>
                                                <span class="kt-nav__link-text">CSV</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="#" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la la-file-pdf-o"></i>
                                                <span class="kt-nav__link-text">PDF</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            &nbsp;
                            <a href="{{ route('translation.create') }}" class="btn btn-brand btn-elevate btn-icon-sm">
                                <i class="la la-plus"></i>
                                @lang('portal.add_new_translation')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <!--begin: Datatable -->
                <table class="table table-striped- table-bordered table-hover table-checkable" id="translationList">
                    <thead>
                    <tr>
                        <th>Grup</th>
                        <th>Key</th>
                        @foreach($glb_locales as $key => $value)
                            <th>{{ $value->title }}</th>
                        @endforeach
                        <th width="100">@lang('portal.actions')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($translation as $key => $value)
                        <tr>
                            <td><code>{{ $value->group }}</code></td>
                            <td><code>{{ $value->key }}</code></td>
                            @php $val = (array) json_decode($value->value); @endphp
                            @foreach ($glb_locales as $key2 => $value2)
                                <td> {{ isset($val[$value2->abbr]) ? $val[$value2->abbr] : '-' }}</td>
                            @endforeach
                            <td align="center">
                               <span class="dropdown">
                                    <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                                      <i class="la la-cogs"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="{{ route('translation.edit', $value->id) }}"><i class="la la-edit"></i> @lang('portal.edit')</a>
                                        <a class="dropdown-item" href="{{ route('translation.destroy', $value->id) }}" data-method="delete" data-token="{{csrf_token()}}" data-confirm="@lang('portal.delete_text')"><i class="la la-trash"></i> @lang('portal.delete')</a>
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
            $('#translationList').DataTable({
                "dom": '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
                "order": [],
                "aoColumnDefs": [
                    { 'orderable': false, 'aTargets': [4] }
                ],
                "pageLength": 50,
                "language": {
                    "url": "{{ asset('/assets/vendors/custom/datatables/locale/tr.json') }}"
                },
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
