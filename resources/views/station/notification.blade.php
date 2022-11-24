<div class="notification">

    <div class="col-12 text-center">
        <span class="form-title">@lang('station.notifications')</span>
        <span class="close-page">
            <button type="button" class="btn btn-circle close" data-dismiss="modal" aria-label="Close"><span class="icon-delete">X</span></button>
        </span>
    </div>

    <div class="n-list">
        <div class="col-12 n-list-header">
            @lang('station.notifications')
        </div>
        @if(isset($notifications) && count($notifications) > 0)
            @foreach($notifications as $key  => $value)
                <div class="col-12 n-list-content">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" {{ $value->read_web == false ? 'checked' :'' }} id="notificationCheck{{ $key }}" value="{{ $value->id }}">
                        <label class="custom-control-label" for="notificationCheck{{ $key }}">{{ app()->getLocale() == 'tr' ? $value->message : $value->message_en }}</label>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12 n-list-content">
                @lang('station.no_record')
            </div>
        @endif

    </div>

</div>

<script type="text/javascript">

    $("input[type=checkbox]").change(function () {
        var notification = $(this);
        var notificationId = $(this).val();
        axios({
            url   : stationAjaxUrl,
            method: 'post',
            data  : {
                process         : 'notificationStatus',
                notificationId  : notificationId
            }
        }).then(function (response) {
            console.log(response.data);
        }).catch(function (error) {
            console.log(error);
        });
    });
</script>

