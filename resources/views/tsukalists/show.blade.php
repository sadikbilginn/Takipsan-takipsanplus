@extends('layouts.main')

@section('content')
<script src="/assets/vendors/general/jquery/dist/jquery.js" type="text/javascript"></script>

<style>
    .inputs
    {
        width:95%;
    }
</style>
<script>

    function Save()
    {
        var form = $('#frmEpcList');
        var data = JSON.stringify($(form).serializeArray());

        var inputs = form.find("input, select, button, textarea");
        inputs.prop("disabled", true);

        $.ajax({
        type: "POST",
        headers: {"X-CSRF-TOKEN":"{{csrf_token()}}"},
        url: "/ajax-transactions",
        data: data,
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function(data){
                inputs.prop("disabled", false);
                alert("list is saved.");
            },
        error: function(errMsg) {
                inputs.prop("disabled", false);
                alert(errMsg);
            }
        });
        return false;
    }

    function GetDetails(listId)
    {
        $.ajax({
        type: "POST",
        headers: {"X-CSRF-TOKEN":"{{csrf_token()}}"},
        url: "/ajax-transactions",
        data:JSON.stringify({
            process:"GetListDetails",
            listId:listId
        }),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function(data){

            renderDetails(data);
            },
        error: function(errMsg) {
                alert(errMsg);
            }
        });
        return false;
    }

    $( document ).ready(function() {        
       
       var url = window.location.href.split('/');
       var listId = url[url.length - 1];
       GetDetails(listId);

            $("#txtEpc").keydown(function() {
                if( $("#txtEpc").val().length + 1 == 25 )
                {
                    sendEPC($("#txtEpc").val());
                    $("#txtEpc").val('');
                }
            });

    });

    function renderDetails(datas)
    {
        datas.forEach(function(d) {
            var inx = $('#epcListTable tr').length;
            var str = `<tr id="row_`+ inx + `">
                                        <td><input type="hidden" name="EPC_`+ inx + `" value="`+ d.epc + `">`+ d.epc + `</td>
                                        <td><input class="inputs" type="text" value="` + d.po_number + `" name="PO_`+ inx + `" id="PO_`+ inx + `" /> </td>
                                        <td><input class="inputs" type="text" value="` + d.size + `" name="size_`+ inx + `" id="Size_`+ inx + `" /> </td>
                                        <td><input class="inputs" type="text" value="` + d.color + `" name="color_`+ inx + `" id="Color`+ inx + `" /> </td>
                                        <td><input class="inputs" type="text" value="` + d.brand + `" name="brand_`+ inx + `" id="Brand`+ inx + `" /> </td>
                                        <td><input class="inputs" type="text" value="` + d.model_no + `" name="modelNo_`+ inx + `" id="Model No`+ inx + `" /> </td>
                                        <td><button type="button" onclick="removeRow('row_`+ inx + `')"  id="btn_`+ inx + `">Remove</button></td>
                                        <td><button type="button" onclick="goToHistory('`+ d.epc + `')"  id="btn_`+ inx + `">History</button></td>
                                    </tr>`;

                                    $("#epcListTable").prepend(str);
        });
    }

    function goToHistory(epc)
    {
        window.location.href = '/history?epc=' + epc;
    }

    function removeRow(rowId)
    {
        $('#' + rowId).remove();
    }

    function sendEPC(epc)
    {
        $("#txtEpc").val(epc);
         
        var inx = $('#epcListTable tr').length;
        var str = `<tr id="row_`+ inx + `">
                                        <td><input type="hidden" name="EPC_`+ inx + `" value="`+ epc + `">`+ epc + `</td>
                                        <td><input class="inputs" type="text" name="PO_`+ inx + `" id="PO_`+ inx + `" /> </td>
                                        <td><input class="inputs" type="text" name="size_`+ inx + `" id="Size_`+ inx + `" /> </td>
                                        <td><input class="inputs" type="text" name="color_`+ inx + `" id="Color`+ inx + `" /> </td>
                                        <td><input class="inputs" type="text" name="brand_`+ inx + `" id="Brand`+ inx + `" /> </td>
                                        <td><input class="inputs" type="text" name="modelNo_`+ inx + `" id="Model No`+ inx + `" /> </td>
                                        <td><button type="button" onclick="removeRow('row_`+ inx + `')"  id="btn_`+ inx + `">Remove</button></td>
                                        <td><button type="button" onclick="goToHistory('`+ d.epc + `')"  id="btn_`+ inx + `">History</button></td>
                                    </tr>`;

                                    $("#epcListTable").prepend(str);
    }

    function EPCInserted()
    {
        var dd =$("#epcListTable");
        
    }

</script>

    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

        <!-- begin:: Subheader -->
        <div class="kt-subheader kt-grid__item" id="kt_subheader">
            <div class="kt-container  kt-container--fluid ">
                <div class="kt-subheader__main">
                    <h3 class="kt-subheader__title">
                        @lang('portal.consignments')
                    </h3>
                    <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                    <div class="kt-subheader__group" id="kt_subheader_search">
                        <span class="kt-subheader__desc" id="kt_subheader_total">{{ $countingList->name }} </span>
                    </div>
                </div>
                <div class="kt-subheader__toolbar"></div>
            </div>
        </div>


        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

            <div class="row">
                <div class="col-xl-12">
                    <!--begin:: Portlet-->
                    <div class="kt-portlet kt-portlet--height-fluid">
                        <div class="kt-portlet__body kt-portlet__body--fit">
                            <!--begin::Widget -->
                            <div class="kt-widget kt-widget--project-1">
                            <div class="kt-portlet__head kt-portlet__head--lg">
                                <div class="kt-portlet__head-label">
                                <span class="kt-portlet__head-icon">
                                    <i class="kt-font-brand flaticon2-shopping-cart"></i>
                                </span>
                                    <h3 class="kt-portlet__head-title">
                                        Counting List Details                        </h3>
                                </div>
                                <div class="kt-portlet__head-toolbar">
                                    <div class="kt-portlet__head-wrapper">
                                        <div class="kt-portlet__head-actions">&nbsp;
                                            <a onclick="return Save()" class="btn btn-brand btn-elevate btn-icon-sm">
                                                <i class="la la-plus"></i>
                                                Save                              </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                <div class="row p-3">
                                    <div class="col-12">
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">EPC</label>
                                                <div class="col-9">
                                                    <input onchange="EPCInserted" type="text" class="form-control" id="txtEpc">
                                                </div>
                                            </div>
                                    </div>
                                </div>
                                <!-- $countingListDetails -->
                                <div class="row p-3">
                                    <div class="col-12">
                                        <form id="frmEpcList">
                                                    <input type="hidden" name="process" value="InsertListDetails">
                                                    <input type="hidden" name="hdnListId" value="{{ $countingList->id }}"/>                                                    
                                                    <table style="width:100%;">
                                                        <thead>
                                                            <tr>
                                                                <th>EPC</th>
                                                                <th>PO</th>
                                                                <th>Size</th>
                                                                <th>Color</th>
                                                                <th>Brand</th>
                                                                <th>Model No</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="epcListTable">
                                                        </tbody>
                                                    </table>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!--end::Widget -->
                        </div>
                    </div>
                    <!--end:: Portlet-->
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


