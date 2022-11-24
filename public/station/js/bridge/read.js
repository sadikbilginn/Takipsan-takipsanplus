var consignment         = new Consignment();
var buffer              = new EpcList();
var inv;

inv = setInterval(function () {
    console.log('>>>>>>>>> check close <<<<<<<<<');
    consignment.checkClose(package_close_time);
}, 750);

// document.addEventListener("keydown", function(event) {
//     if(event.keyCode == 32){
//         $('#startStop').trigger('click');
//     }
// });

$(function () {
    //localStorage.removeItem('consignmentId');
    var consignmentId = localStorage.getItem('consignmentId');
    if(consignmentId !== null){
        console.log(consignmentId);
        $('#consignments').val(consignmentId).change();
    }

    checkNotification();
});

$("#checkAll").click(function () {
    //$(".check").prop('checked', $(this).prop('checked'));

    if(this.checked) {
        $('input[type=checkbox].check').each(function() {
            this.checked = true;
        });
    }else{
        $('input[type=checkbox].check').each(function() {
            this.checked = false;
        });
    }

    consignment.getSizes();
});

function checkClick(e) {

    var totalCheck = $(".check").length;
    var selectedCheck = $(".check:checked").length;

    if(totalCheck == selectedCheck){
        $("#checkAll").prop('checked', true);
    }else{
        $("#checkAll").prop('checked', false);
    }

    consignment.getSizes();
}

function updateQuantity(key, value){
    $('#packageTotal').text(value);
    $('#consignmentList tbody tr#' + key + ' td')[2].innerText = value;
}

function updateTotalQuantity(value){
    $('#totalQuantity').text(value);
}

function updateID(key, value){
    $('#customCheck'+key).attr('data-id', value);
}

function updateSizes(sizeMap){
    $('#consignmentDetails tbody tr').remove();
    sizeMap.forEach(function (size, key) {
        if(size != '' && size != 0){
            $('#consignmentDetails tbody').append('<tr>'
                +'<td>'+key+'</td><td>'+size+'</td>'
                +'</tr>');
        }
    });
}

async function insertFromDbPackage(data){

    $('#consignmentList tbody tr').remove();
    $('#totalQuantity').text(0);

    consignment.packages        = new Map();
    consignment.epcs            = new Set();
    consignment.packageNo       = 0;
    consignment.isLastBoxClosed = true;
    consignment.databaseBusy    = false;

    await  $.each(data.reverse(), function(i, item) {

        var tag = {};
        tag.id              = item.id;
        tag.packageNo       = item.package_no;
        tag.itemsCount      = item.items_count;
        tag.model           = item.model == null ? '-' : item.model;
        tag.size            = item.size == null ? '-' : item.size;

        consignment.addPackage(tag);

    });

    consignment.addHtml();

}

async function insertFromDbItem(data){

    consignment.epcs            = new Set();

    await  $.each(data, function(i, item) {

        var tag = {};
        tag.packageNo       = item.package_no;
        tag.epc             = item.epc;
        tag.created_date    = item.created_at;

        consignment.addItem(tag);

    });

    consignment.getSizes();

    consignmentLoading = true;

    if(connectReaderStatus && consignmentLoading){
        $("#startStop").prop("disabled", false);
    }else{
        window.setTimeout(function () {
            if(connectReaderStatus && consignmentLoading){
                $("#startStop").prop("disabled", false);
            }
        }, 2000);
    }

}

function checkNotification() {
    axios({
        url   : stationAjaxUrl,
        method: 'post',
        data  : {
            process         : 'notificationCheck'
        }
    }).then(function (response) {
        if(response.data == 0){
            $('#notification-img').attr('src', '/station/img/notification.svg');
        }else{
            $('#notification-img').attr('src', '/station/img/notification2.svg');
        }
        setTimeout(function () {
            checkNotification();
        },15000);

    }).catch(function (error) {
        console.log(error);
    });
}

function sweetAlert(title, text, icon, button = 'Tamam') {
    Swal.fire({
        allowOutsideClick: false,
        title: title,
        text: text,
        icon: icon,
        confirmButtonText: button
    });
}

function clearStorage() {
    localStorage.removeItem('deviceId');
}
