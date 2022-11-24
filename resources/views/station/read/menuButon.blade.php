<div class="col-lg-3">
    <button class="btn btn-start btn-block" onclick="startStop(this);" data-default="start" id="startStop">
        @lang('station.start')
    </button>
    <button class="btn btn-default btn-combine btn-block" onclick="combinePackage(this);">
        <img src="/station/img/spreadsheet.svg" alt="spreadsheet"> @lang('station.combine')
    </button>
    <button class="btn btn-default btn-bil btn-combine btn-block d-none">
        <img src="/station/img/spreadsheet.svg" alt="spreadsheet">
        <span style="font-size:16px;"></span>
    </button>
    <button class="btn btn-default btn-delete btn-block" onclick="deletePackage(this);">
        <img src="/station/img/bin.svg" alt="bin"> @lang('station.delete')
    </button>

    <button class="btn btn-default btn-print btn-block" onclick="print(this);">
        <img src="/station/img/printer.svg" alt="printer"> @lang('station.print')
    </button>

    <button class="btn btn-default btn-find btn-block" onclick="findPackage(this);" id="findPackage" disabled>
        <img src="/station/img/find.svg" alt="find"> @lang('station.find_package')
    </button>
    <button class="btn btn-default btn-setting btn-block mb-0" onclick="getPage('settings');">
        <img src="/station/img/cog.svg" alt="cog"> @lang('station.settings')
    </button>
</div>
