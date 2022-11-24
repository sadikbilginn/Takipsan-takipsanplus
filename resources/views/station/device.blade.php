@extends('station.layout.main')

@section('content')

    <form action="{{ route('station.device.check') }}" method="post">
        @csrf
        <div class="row device animated fadeInUp">

            <div class="col-12 text-center mb-3">
                <span class="form-title">@lang('station.select_device')</span>
            </div>

            <div class="col-6 offset-3">
                <div class="form-group">
                    <select name="device_id" id="device_id" class="form-control custom-select">
                        <option value="">@lang('station.choose')</option>
                        @foreach($devices as $key => $value)
                            <option value="{{ $value->id }}" {{ $value->status == 0 ? 'disabled' : '' }}>{{ $value->status == 0 ? $value->name . '-  license expired! ' : $value->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-4 offset-lg-4">
                <button type="submit" class="btn btn-block btn-primary submit">@lang('station.continue')</button>
            </div>
        </div>
    </form>

@endsection

@section('js')
    <script type="text/javascript">
        $("#device_id").on('change', function () {
            localStorage.setItem('deviceId', $(this).val());
        });
    </script>
@endsection
