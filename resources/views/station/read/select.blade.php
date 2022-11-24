<div class="col-lg-5" style="padding:0 30px;">
    <div class="select-wrapper">
        <select
            name="consignments"
            id="consignments"
            class="selectpicker form-control"
            data-tap-disabled="true"
            data-live-search="true"
            data-live-search-style="contains"
            data-show-subtext="true"
        >
            <option value="" selected>@lang('station.choose_consignment')</option>
            @foreach($consignments as $key => $value)
            <option
                value = "{{ $value->id }}"
                data-order = "{{ $value->order->id }}"
                data-itemcount = "{{ $value->item_count }}"
                data-consignee = "{{ $value->consignee ? $value->consignee->name : '-' }}"
                data-view = "{{ $value->consignee_id}}"
                data-consigneeview = "{{$value->consignee->viewid}}"
                data-selectId = "{{ $value->id }}"
                data-deliverydate = "{{ date('d.m.Y', strtotime($value->delivery_date)) }}"
                data-createdate = "{{ $value->created_at }}"
                data-model = "{{ $value->order->name }}"
                data-hanging = "{{ $value->hanging_product  }}"
                {{ isset($selectId) && $selectId == $value->id ? 'selected' : '' }}
            >
                {{ $value->name }}
            </option>
            @endforeach
        </select>
    </div>
</div>
