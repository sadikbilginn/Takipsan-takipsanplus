<!--begin: Datatable -->
<style>
    table.dataTable td.dt-control:before {
        height: 1em;
        width: 1em;
        margin-top: -9px;
        display: inline-block;
        color: white;
        border: 0.15em solid white;
        border-radius: 1em;
        box-shadow: 0 0 0.2em #444;
        box-sizing: content-box;
        text-align: center;
        text-indent: 0 !important;
        font-family: "Courier New",Courier,monospace;
        line-height: 1em;
        content: "+";
        background-color: #31b131;
        cursor:pointer;
    }
    .bg-danger td{color:#fff!important;}
</style>
<table class="table table-striped- table-bordered table-hover table-checkable" id="consignmentListMs">
    <thead>
    <th></th>
    <th>U.P.C.</th>
    <th>@lang('station.size')</th>
    <th>@lang('station.target_qty')</th>
    <th>@lang('station.read_quantity')</th>
    <th>@lang('station.invalidQuantity')</th>

    </thead>
    <tbody>

    </tbody>
</table>
<!--end: Datatable -->
