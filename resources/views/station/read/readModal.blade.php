<div class="modal bd-example-modal-xl" id="readModal" tabindex="-1" role="dialog" aria-labelledby="readModalTitle" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-body read">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="read-header">
                                @lang('station.package_read')
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="read-media">
                                <img src="/station/img/box-animation.gif" alt="">
                                <br>
                                <img src="/station/img/load.gif" alt="">
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="read-box">
                                <div>@lang('station.total_read')</div>
                                <div id="packageTotal">0</div>
                            </div>
                        </div>
                        <div class="col-12 hangingButtonContent" style="display:none">
                            <button
                                class="btn btn-start btn-block btn-stop  mt-3"
                                id="startStopHanging">
                                @lang('station.stop')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
