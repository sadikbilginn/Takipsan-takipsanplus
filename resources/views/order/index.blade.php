@extends('layouts.main')

@section('content')

    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

        <!-- begin:: Subheader -->
        <div class="kt-subheader kt-grid__item" id="kt_subheader">
            <div class="kt-container  kt-container--fluid ">
                <div class="kt-subheader__main">
                    <h3 class="kt-subheader__title">
                        @lang('portal.orders')
                    </h3>
                    <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                    <div class="kt-subheader__group" id="kt_subheader_search">
                        <span class="kt-subheader__desc" id="kt_subheader_total">@lang('portal.list')</span>
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
                        <i class="kt-font-brand flaticon2-shopping-cart"></i>
                    </span>
                        <h3 class="kt-portlet__head-title">
                            @lang('portal.order_list')
                        </h3>
                    </div>
                    <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-wrapper">
                            <div class="kt-portlet__head-actions">&nbsp;
                                <a href="{{ route('order.create') }}" class="btn btn-brand btn-elevate btn-icon-sm">
                                    <i class="la la-plus"></i>
                                    @lang('portal.add_new_order')
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    <!--begin: Datatable -->
                    <table class="table table-striped- table-bordered table-hover table-checkable" id="orderList">
                        <thead>
                        <tr>
                            <th width="25" style="text-align: center;">#</th>
                            <th>@lang('portal.order_code')</th>
                            <th>@lang('portal.po_no')</th>
                            <th>@lang('portal.model_name')</th>
                            <th>@lang('portal.consignee_name')</th>
                            <th>@lang('portal.create_date')</th>
                            <th width="75">@lang('portal.status')</th>
                            <th width="75">@lang('portal.actions')</th>
                        </tr>
                        </thead>
                    </table>
                    <!--end: Datatable -->
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addConsignment" tabindex="-1" role="dialog" aria-labelledby="addConsignmentCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addConsignmentLongTitle">@lang('portal.new_consignment') - <span id="order_code"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="consignment_form">
                        <input type="hidden" name="process" value="saveConsignment">
                        <input type="hidden" name="order_id" value="">
                        <div class="form-group row">
                            <label class="col-3 col-form-label">@lang('portal.company')</label>
                            <div class="col-9">
                                <select name="company_id" id="company_id" class="form-control @error('company_id') is-invalid @enderror">
                                    <option value="" selected>@lang('portal.choose')</option>
                                    @foreach($companies as $key => $value)
                                        <option value="{{ $value->id }}" {{ $value->id == old('company_id') ? 'selected' : '' }}>{{ $value->name }}</option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        {{--
                            <div class="form-group row">
                            <label class="col-3 col-form-label">@lang('portal.plate')</label>
                            <div class="col-9">
                                <input type="text" class="form-control @error('plate_no') is-invalid @enderror" name="plate_no" value="{{ old('plate_no') }}">
                                @error('plate_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        --}}
                        <div class="form-group row">
                            <label class="col-3 col-form-label">@lang('portal.piece')</label>
                            <div class="col-9">
                                <input type="number" class="form-control @error('item_count') is-invalid @enderror" name="item_count" min="10" value="{{ old('item_count') }}">
                                @error('item_count')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-3 col-form-label">@lang('portal.delivery_date')</label>
                            <div class="col-9">
                                <input type="date" class="form-control @error('delivery_date') is-invalid @enderror" name="delivery_date" value="{{ old('delivery_date') }}">
                                @error('delivery_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('portal.close')</button>
                    <button type="button" class="btn btn-primary" onclick="saveConsignment();return false;">@lang('portal.add')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link href="/assets/vendors/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
    <style>
        td.details-control {
            background: url('/assets/media/icons/details_open.png') no-repeat center center;
            cursor: pointer;
        }
        tr.shown td.details-control {
            background: url('/assets/media/icons/details_close.png') no-repeat center center;
        }
    </style>
@endsection

@section('js')
    <script src="/assets/vendors/custom/datatables/datatables.bundle.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var table = $('#orderList').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('order.datatable') }}",
                order: [],
                columns: [
                    {

                        "className":      'details-control',
                        "orderable":      false,
                        "data":           null,
                        "defaultContent": '',
                    },
                    {data: 'order_code', name:'order_code'},
                    {data: 'po_no', name:'po_no'},
                    {data: 'name',name:'name'},
                    {data: 'consignee_id', name: 'consignee_id'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'status', name: 'status', className: 'text-center'},
                    {data: 'action', name: 'action', orderable: false, searchable: false, responsivePriority: -1, className: 'text-center'},
                ],
                @if(app()->getLocale() == 'tr')
                language: {
                    "url": "{{ asset('/assets/vendors/custom/datatables/locale/tr.json') }}"
                },
                @endif
                order: [],
                initComplete: function () {
                    $.getScript("/assets/js/delete.js");
                }
            });

            $('#orderList tbody').on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                var row = table.row( tr );

                if ( row.child.isShown() ) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open this row
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ route('order.datatable.details') }}",
                        data: {_token: '{{csrf_token()}}', id: row.data().id},
                        beforeSend: function () {},
                        success: function (data) {
                            row.child(format(data)).show();
                            tr.addClass('shown');
                        },
                        complete: function() {
                            $.getScript("/assets/js/delete.js");
                        }
                    });
                }
            } );
        });

        function format ( d ) {
            if(d.length > 0){
                var tb = '<table class="table table-striped- table-bordered table-hover table-checkable">\n' +
                '              <thead>\n' +
                '                    <tr>\n' +
                '                      <th width="75">@lang('portal.po_no')</th>\n' +
                '                      <th width="150">@lang('portal.company')</th>\n' +
                '                      <th width="120">@lang('portal.piece') / @lang('portal.read')</th>\n' +
                '                      <th width="120">@lang('portal.delivery_date')</th>\n' +
                '                      <th width="120">@lang('portal.create_date')</th>\n' +
                '                      <th width="75">@lang('portal.status')</th>\n' +
                '                      <th width="75">@lang('portal.actions')</th>\n' +
                '                    </tr>\n' +
                '                  </thead>';
                tb += d;
                tb+='</table>';
            }else{
                tb = 'Bu sipariş için bir sevkiyat kaydı bulunamadı.';
            }
            return tb;

        }

        function addConsignment(e){
            var btn = $(e);

            $('#order_code').text(btn.attr('data-order-code'));
            $('input[name=order_id]').val(btn.attr('data-id'));
            console.log('yeap');
        }

        function saveConsignment(){

            $.ajax({
                url: "{{ route('ajax') }}",
                method: 'post',
                data: $('#consignment_form').serialize(),
                beforeSend: function(xhr, opts) {},
                success: function(result){
                    swal.fire({
                        title: result.title,
                        text: result.text,
                        type: result.status,
                        confirmButtonText: "@lang('portal.ok')",
                        cancelButtonText: "@lang('portal.cancel')",
                        buttonsStyling: false,
                        confirmButtonClass: 'btn btn-primary',
                        cancelButtonClass: 'btn btn-light'
                    });

                    if(result.status == 'success'){
                        window.location.reload();
                    }
                }
            });
        }

    </script>
@endsection
