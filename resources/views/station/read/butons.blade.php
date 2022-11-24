<div class="row btn-set">
    <div class="col-3">
        <a href="javascript:;" onclick="getPage('consignmentVote');" class="btn btn-plus btn-circle">
            <img src="/station/img/plus.svg" alt="plus">
        </a>
    </div>
    <div class="col-3">
        <a href="javascript:;" onclick="editConsignment(this);" class="btn btn-edit btn-circle">
            <img src="/station/img/edit.svg" alt="edit">
        </a>
    </div>
    @if(auth()->user()->company->consignment_close == true)
    <div class="col-3">
        <a href="javascript:;" class="btn btn-close btn-circle" onclick="closeConsignment(this);">
            <img src="/station/img/close.svg" alt="close">
        </a>
    </div>
    @endif
    <div class="col-3">
        <a href="javascript:;" onclick="getPage('notification');" class="btn btn-notification btn-circle">
            <img src="/station/img/notification.svg" id="notification-img" alt="notification">
        </a>
    </div>
</div>