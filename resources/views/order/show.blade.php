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
                    <span class="kt-subheader__desc" id="kt_subheader_total">{{ $order->po_no }}</span>
                </div>
            </div>
            <div class="kt-subheader__toolbar"></div>
        </div>
    </div>
    <!-- end:: Subheader -->

    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <!--Begin::Section-->
        <div class="row">
            <div class="col-xl-12">
                <div class="kt-portlet kt-portlet--height-fluid">
                    <div class="kt-portlet__body kt-portlet__body--fit">
                        <!--begin::Widget -->
                        <div class="kt-widget kt-widget--project-1">
                            <div class="kt-widget__head">
                                <div class="kt-widget__label">
                                    <div class="kt-widget__media">
                                            <span class="kt-media kt-media--lg kt-media--circle">
                                                <img src="{{ $order->consignee ? config('settings.media.consignees.full_path') . $order->consignee->logo : asset('assets/media/default.jpg') }}" style="border: 1px solid #aaa" alt="image">
                                            </span>
                                    </div>
                                    <div class="kt-widget__info kt-margin-t-5">
                                        <a href="#" class="kt-widget__title">
                                            {{ $order->po_no}}
                                            {!! $order->status == true ? '<i class="flaticon2-correct kt-font-success"></i>' : '<i class="flaticon2-correct kt-font-danger"></i>' !!}
                                        </a>
                                        <span class="kt-widget__desc">
                                            {{ $order->name }}
                                        </span>
                                    </div>
                                </div>
                                <div class="kt-portlet__head-toolbar">
                                    <button type="button" class="btn btn-label-success btn-sm btn-upper" onclick="javascript:history.go(-1);"><i class="la la-arrow-left"></i> @lang('portal.back')</button>
                                    <a href="#" class="btn btn-clean btn-sm btn-icon btn-icon-md" data-toggle="dropdown">
                                        <i class="flaticon-more-1"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right">
                                        <ul class="kt-nav">
                                            <li class="kt-nav__item">
                                                <a href="{{ route('order.edit', $order->id) }}" class="kt-nav__link">
                                                    <i class="kt-nav__link-icon flaticon2-contract"></i>
                                                    <span class="kt-nav__link-text">@lang('portal.edit')</span>
                                                </a>
                                            </li>
                                            <li class="kt-nav__item">
                                                <a href="{{ route('order.destroy', $order->id) }}" class="kt-nav__link" data-method="delete" data-token="{{csrf_token()}}" data-confirm="@lang('portal.delete_text')">
                                                    <i class="kt-nav__link-icon flaticon2-trash"></i>
                                                    <span class="kt-nav__link-text">@lang('portal.delete')</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="kt-widget__body">
                                <div class="kt-widget__stats">
                                    <div class="kt-widget__item">
                                        <span class="kt-widget__date">
                                            @lang('portal.create_date')
                                        </span>
                                        <div class="kt-widget__label">
                                            <span class="btn btn-label-brand btn-sm btn-bold btn-upper">{{ date('d.m.Y H:i', strtotime($order->created_at)) }}</span>
                                        </div>
                                    </div>
                                    <div class="kt-widget__item">
                                        <span class="kt-widget__date">
                                            @lang('portal.update_date')
                                        </span>
                                        <div class="kt-widget__label">
                                            <span class="btn btn-label-warning btn-sm btn-bold btn-upper">{{ date('d.m.Y H:i', strtotime($order->updated_at)) }}</span>
                                        </div>
                                    </div>

                                    @php $conStatus = consignmentStatusPercent($order->items_count, $order->item_count); @endphp
                                    <div class="kt-widget__item flex-fill">
                                        <span class="kt-widget__subtitel">@lang('portal.order_status')</span>
                                        <div class="kt-widget__progress d-flex  align-items-center">
                                            <div class="progress" style="height: 5px;width: 100%;">
                                                <div class="progress-bar {{ consignmentProgressBg($conStatus) }}" role="progressbar" style="width: {{ $conStatus }}%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span class="kt-widget__stat">
                                                {{ $conStatus }}%
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="kt-widget__content">
                                    <div class="kt-widget__details">
                                        <span class="kt-widget__subtitle">@lang('portal.po_no')</span>
                                        <span class="kt-widget__value">{{ $order->po_no }}</span>
                                    </div>
                                    <div class="kt-widget__details">
                                        <span class="kt-widget__subtitle">@lang('portal.order_code')</span>
                                        <span class="kt-widget__value">{{ $order->order_code }}</span>
                                    </div>
                                    <div class="kt-widget__details">
                                        <span class="kt-widget__subtitle">@lang('portal.model_name')</span>
                                        <span class="kt-widget__value">{{ $order->name }}</span>
                                    </div>
                                    <div class="kt-widget__details">
                                        <span class="kt-widget__subtitle">@lang('portal.number_orders')</span>
                                        <span class="kt-widget__value">{{ number_format($order->item_count) }}</span>
                                    </div>
                                    <div class="kt-widget__details">
                                        <span class="kt-widget__subtitle">@lang('portal.number_read')</span>
                                        <span class="kt-widget__value">{{ number_format($order->items_count) }} @lang('portal.piece')</span>
                                    </div>
                                    <div class="kt-widget__details">
                                        <span class="kt-widget__subtitle">@lang('portal.consignee_name')</span>
                                        <span class="kt-widget__value">{{$order->consignee ?  $order->consignee->name : '-' }}</span>
                                    </div>
                                    <div class="kt-widget__details">
                                        <span class="kt-widget__subtitle">@lang('portal.creator_user')</span>
                                        <span class="kt-widget__value"> {{  $order->created_user ? $order->created_user->name : '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Widget -->
                    </div>
                </div>
            </div>
            <div class="col-xl-12">
                <div class="kt-portlet kt-portlet--height-fluid">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                @lang('portal.consignmet_list')
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <!--begin: Datatable -->
                        <table class="table table-striped- table-bordered table-hover table-checkable" id="consignmentList">
                            <thead>
                            <tr>
                                <th width="25">#</th>
                                <th>@lang('portal.po_no')</th>
                                <th>@lang('portal.piece') / @lang('portal.read')</th>
                                <th>@lang('portal.company')</th>
                                <th>@lang('portal.consignee_name')</th>
                                <th>@lang('portal.delivery_date')</th>
                                <th>@lang('portal.status')</th>
                                <th width="100">@lang('portal.actions')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($order->consignments as $key => $value)
                                <tr>
                                    <td width="25">{{ $key + 1 }}</td>
                                    <td><a href="{{ route('consignment.show', $value->id) }}">{{ $value->name }}</a></td>
                                    <td><span class="badge badge-info">{{ $value->item_count }}</span>  >> <span class="badge badge-success"> {{ $value->items->count() }}</span></td>
                                    <td>{{ $value->company ? $value->company->name : '-' }}</td>
                                    <td>{{ $value->consignee ? $value->consignee->name : '-' }}</td>
                                    <td>{{ date('d.m.Y', strtotime($value->delivery_date)) }}</td>
                                    <td align="center">{!! $value->status == 1 ? '<span class="badge badge-success">Açık</span>' : '<span class="badge badge-danger">Kapalı</span>' !!}</td>
                                    <td align="center">
                                    <span class="dropdown">
                                        <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                                          <i class="la la-cogs"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="{{ route('consignment.show', $value->id) }}"><i class="la la-search"></i> @lang('portal.show')</a>
                                            <a class="dropdown-item" href="{{ route('consignment.status', $value->id) }}"><i class="la la-truck"></i> {{ $value->status == true ? trans('portal.close_consignmet') : trans('portal.open_consignmet') }}</a>
                                            <a class="dropdown-item" href="{{ route('consignment.edit', $value->id) }}"><i class="la la-edit"></i> @lang('portal.edit')</a>
                                            <a class="dropdown-item" href="{{ route('consignment.destroy', $value->id) }}" data-method="delete" data-token="{{ csrf_token() }}" data-confirm="@lang('portal.delete_text')"><i class="la la-trash"></i> @lang('portal.delete')</a>
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
        <!--End::Section-->
    </div>
</div>

@endsection

@section('js')

@endsection
