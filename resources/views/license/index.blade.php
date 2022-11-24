@extends('layouts.main')

@section('content')

<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

    <!-- begin:: Subheader -->
    <div class="kt-subheader kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">
                    Lisanslar
                </h3>
                <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                <div class="kt-subheader__group" id="kt_subheader_search">
                    <span class="kt-subheader__desc" id="kt_subheader_total">Lisans Listesi</span>
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
                    <h3 class="kt-portlet__head-title">
                    @if (Route::currentRouteName() == 'license.noLicense')    
                        Lisansı Olmayanlar Listesi
                    @else
                        Lisansı Olanlar Listesi
                    @endif
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                            @if (Route::currentRouteName() == 'license.noLicense')
                                <a href="{{ route('license.notLicensedExcel') }}" class="btn btn-secondary btn-elevate btn-icon-sm">
                                    Excel Çıktısı
                                </a>
                                <a href="license" class="btn btn-success btn-elevate btn-icon-sm">
                                    Lisansı Olanlar
                                </a>
                            @else
                                <a href="{{ route('license.licensedExcel') }}" class="btn btn-info btn-elevate btn-icon-sm">
                                    Excel Çıktısı
                                </a>
                                <a href="no-license" class="btn btn-danger btn-elevate btn-icon-sm">
                                    Lisansı Olmayanlar
                                </a>
                            @endif
                            <a href="{{ route('license.create') }}" class="btn btn-brand btn-elevate btn-icon-sm">
                                <i class="la la-plus"></i>
                                Yeni Lisans Ekle
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <!--begin: Datatable -->
                <table class="table table-striped- table-bordered table-hover table-checkable" id="licenseList">
                    <thead>
                    <tr>
                        <th>Firma</th>
                        <!--<th>Alt Firma</th>-->
                        <th>Başlangıç Tarihi</th>
                        <th>Bitiş Tarihi</th>
                        <th>Durum</th>
                        <th width="100">İşlemler</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($licenses as $key => $value)
                        <tr>
                            <td>{{ $value['company'] }}</td>
                            {{-- <td>{{ $value['manufacturer'] }}</td>--}}
                            <td>{{ $value['start_date'] }}</td>
                            <td>{{ $value['finish_date'] }}</td>
                            <td align="center">
                                {!! $value['status'] == true 
                                    ? '<span class="badge badge-success">Aktif</span>' 
                                    : '<span class="badge badge-danger">Pasif</span>' !!}
                            </td>
                            <td align="center">
                                {{-- @if($value['user'] == "-")
                                    -
                                @else --}}
                                    <span class="dropdown">
                                        <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                                            <i class="la la-cogs"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="{{ route('license.edit', $value['id']) }}"><i class="la la-edit"></i> Düzenle</a>
                                            <a class="dropdown-item" href="{{ route('license.destroy', $value['id']) }}" data-method="delete" data-token="{{csrf_token()}}" data-confirm="Kaydı silmek istediğinize emin misiniz?"><i class="la la-trash"></i> Sil</a>
                                        </div>
                                    </span>
                                {{-- @endif --}}
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
            $('#licenseList').DataTable({
                "order": [],
                "aoColumnDefs": [
                    { 'orderable': false, 'aTargets': [7] }
                ],
                "pageLength": 50,
                "language": {
                    "url": "{{ asset('/assets/vendors/custom/datatables/locale/tr.json') }}"
                }
            });
        });
    </script>

@endsection
