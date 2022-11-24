
//Read Ekranı hm
class Item{

    constructor(epc, timestamp,package_id,id,gtin,size,rendered = false){
        this.epc        = epc;
        this.timestamp  = new Date(timestamp);
        this.package_id = package_id;
        this.gtin       = gtin;
        this.id         = id;
        this.size       = size;
        this.rendered   = false;
    }
}

class Package{
    constructor(index, itemsCount, lastUpdated, items,pType,lType){
        this.id             = 0;
        this.items          = new Map();
        if(items && items.length > 0){

            for (let i = 0; i < items.length; i++) {
                this.items.set(items[i].epc, new Item(items[i].epc, new Date(),items[i].package_id,items[i].id,items[i].gtin,items[i].size));
            }
        }
        this.lastUpdated    = lastUpdated;
        this.itemsCount     = itemsCount ? itemsCount : 1 ;
        this.load_type      = lType ? lType : currentLType;
        this.box_type_id    = pType ? pType : currentPType;
        this.model          = null;
        this.size           = null;
        this.isClosed       = false;
    }
}

class Consignment{
    constructor(){
        this.packages           = new Map();
        this.epcs               = new Set();
        this.sizes              = new Map();
        this.packageNo          = 0;
        this.isLastBoxClosed    = true;
        this.databaseBusy       = false;
    }

    add = function (tag){

        //database bekle
        if (!this.databaseBusy){
            //console.log("started 1");

            if(!this.epcs.has(tag.epc)){
                //console.log("fn add not has");
                //Son paket kapalı mı?

                if (this.isLastBoxClosed){
                    //console.log("lastBoxClose");
                    //Paket sayısını bir artır
                    this.packageNo++;
                    //Yeni paket oluştur
                    this.packages.set(this.packageNo, new Package(this.packageNo,  null));


                    //Paketi açık hale getir
                    this.isLastBoxClosed = false;

                    //Ekrana bas
                    modalOpen();

                    var last_model = "-";
                    if(auto_model_name == 1 &&
                        $('#consignmentList tbody tr').length > 0 &&
                        $('#consignmentList tbody tr:first-child td:nth-child(4)').text() != '' &&
                        $('#consignmentList tbody tr:first-child td:nth-child(4)').text() != null &&
                        $('#consignmentList tbody tr:first-child td:nth-child(4)').text() != '-'){

                        last_model = $('#consignmentList tbody tr:first-child td:nth-child(4)').text();
                    }

                    var last_size = "-";
                    if(auto_size_name == 1 &&
                        $('#consignmentList tbody tr').length > 0 &&
                        $('#consignmentList tbody tr:first-child td:nth-child(5)').text() != '' &&
                        $('#consignmentList tbody tr:first-child td:nth-child(5)').text() != null &&
                        $('#consignmentList tbody tr:first-child td:nth-child(5)').text() != '-'){

                        last_size = $('#consignmentList tbody tr:first-child td:nth-child(5)').text();
                    }
                }

                var currentPackage = this.packages.get(this.packageNo);
                //Pakete epc bilgilerini ekle
                currentPackage.items.set(tag.epc, new Item(tag.epc, tag.firstSeenTime,0,0,"",""));

                //Paket son ekleme tarihi güncelle
                currentPackage.lastUpdated = tag.firstSeenTime;

                //epc'yi ekle
                this.epcs.add(tag.epc);

                insertRow(this.packageNo,currentPackage)

                //Miktarı güncelle
                updateQuantity(this.packageNo, currentPackage.items.size);

                //Toplam okunan epc sayısı
                updateTotalQuantity(this.epcs.size);

                 this.getSizes();

                //console.log("epc added:" + tag.epc);
            }

        } else {

            let consignment = this;
            let theTag = tag;

            setTimeout(function () {
                consignment.add(theTag);
            }, 300);

        }

    };

    addPackage = function (tag){

        //Paket numarasını al
        tag.packageNo = parseInt(tag.packageNo);

        //Paket kontrolü yap yoksa içeri gir
        if (!this.packages.has(tag.packageNo)){

            //Paket numarasını bir artır
            this.packageNo = this.packageNo + 1;

            //Yeni paket oluştur
            this.packages.set(tag.packageNo, new Package(tag.packageNo, tag.itemsCount,  null, tag.items,tag.box_type_id,tag.load_type));

            if (tag.items && tag.items.length > 0) {
                for (let i = 0; i < tag.items.length; i++) {
                    this.epcs.add(tag.items[i].epc);
                }
            }

            //Paketi kapat
            this.isLastBoxClosed = true;
            this.packages.get(tag.packageNo).isClosed = true;

            //Model Beden Ekle
            this.packages.get(tag.packageNo).model = tag.model;
            this.packages.get(tag.packageNo).size  = tag.size;

            //id atama
            this.packages.get(tag.packageNo).id  = tag.id;

        }

    };

    addItem = function (tag){

        //Paket numarasını al
        tag.packageNo = parseInt(tag.packageNo);

        //Pakete epc bilgilerini ekleitems[i].epc
        this.packages.get(tag.packageNo).items.set(tag.epc, new Item(tag.epc, tag.firstSeenTime,0,0,"",""));

        //Paket son ekleme tarihi güncelle
        this.packages.get(tag.packageNo).lastUpdated = tag.created_date;

        //epc'yi ekle
        this.epcs.add(tag.epc);

        //Toplam okunan epc sayısı
        updateTotalQuantity(this.epcs.size);
    };

    addHtml = function (){

        this.packages.forEach(function (item, index) {
            //Ekrana bas
            insertRow(index, item);
            updateQuantity(index, item.itemsCount);
            updateID(index, item.id);
        });
        $('#loading').hide();
    };

    checkClose = function(boxCloseTime){

        //Son paket kapalı mı?
        if(!this.isLastBoxClosed){
            var currentPackage = this.packages.get(this.packageNo);
            //Son paket sonrası geçen zaman kontrolü
            var packages =this.packages;
            if ((new Date().getTime() -  currentPackage.lastUpdated) > (boxCloseTime * 1000)){
                //console.log('Closing Box');

                this.isLastBoxClosed = true;
                currentPackage.isClosed = true;


                // //paketi kapat
                // this.isLastBoxClosed = true;
                // currentPackage.isClosed = true;

                //console.log(this.packages.get(this.packageNo).items.values());

                var packageNo = this.packageNo;
                axios.defaults.timeout = 5000;
                axios({
                    url   : stationAjaxUrl,
                    method: 'post',
                    data  : {
                        process         : 'sendPackage',
                        consignmentId   : $('#consignments').val(),
                        orderId         : $('#consignments option:selected').attr('data-order'),
                        package         : packageNo,
                        load_type       : currentPackage.load_type,
                        box_type_id     : currentPackage.box_type_id,
                        // model           : $('#consignmentList tbody tr#'+packageNo+' td:nth-child(4)').text(),
                        // size            : $('#consignmentList tbody tr#'+packageNo+' td:nth-child(5)').text(),
                        data            : Array.from(currentPackage.items.values())
                    }
                }).then(function (response) {
                    //Paket durumunu güncelle
                    modalClose(packageNo);
                    currentPackage.id = response.data;
                    updateID(packageNo, response.data);

                    console.log(response.data);
                    console.log('save');

                    if(packages && packages.size == 1)
                    {
                        if ((!packages.get(1).box_type_id || packages.get(1).box_type_id.length == 0) || (!packages.get(1).load_type ||packages.get(1).load_type.length == 0)) {
                            stopReader(readerId);
                            typesEdit( $("td[id='ptype_"+ packages.get(1).id  + "']")[0],currentPackage);
                            return;
                        }
                    }

                }).catch(function (error) {
                    modalCloseFail(packageNo);
                    stopReader(readerId);
                    location.reload();
                    console.log(error)
                });

                if(auto_print == 1){
                    autoPrint();
                }

            } else {
                console.log('we have time to close');
            }

        }
    };

    allClose = function(){

        //Son paket kapalı mı?
        if(!this.isLastBoxClosed){

            //paketi kapat
            this.isLastBoxClosed = true;
            this.packages.get(this.packageNo).isClosed = true;

            //console.log(this.packages.get(this.packageNo).items.values());

            var packageNo = this.packageNo;
            axios.defaults.timeout = 5000;
            axios({
                url   : stationAjaxUrl,
                method: 'post',
                data  : {
                    process         : 'sendPackage',
                    consignmentId   : $('#consignments').val(),
                    orderId         : $('#consignments option:selected').attr('data-order'),
                    package         : packageNo,
                    load_type       : currentPackage.load_type,
                        box_type_id     : currentPackage.box_type_id,
                    model           : $('#consignmentList tbody tr#'+packageNo+' td:nth-child(4)').text(),
                    size            : $('#consignmentList tbody tr#'+packageNo+' td:nth-child(5)').text(),
                    data            : Array.from(this.packages.get(packageNo).items.values())
                }
            }).then(function (response) {
                //Paket durumunu güncelle
                modalClose(packageNo);
                updateID(packageNo, response.data);

                console.log(response.data);
                console.log('save');
            }).catch(function (error) {
                modalCloseFail(packageNo);
                location.reload();
                console.log(error)
            });

        }

    };

    getSizes = function () {

        console.log(">>>>>>>sizes<<<<<<<");

        $('#consignmentDetails tbody tr').remove();

        let packages    = this.packages;
        var sizeMap     = this.sizes;
        var total       = 0;

        $('input[type=checkbox].check:checked').each(function () {

            var packageNo = parseInt($(this).attr('package-no'));
            var size = packages.get(packageNo).size;

            // if(size == "" || size == null || size == "-"){

                var new_size = "UND";
                packages.get(packageNo).items.forEach((values, keys)=>{
                    new_size = "UND";
                    var prds =  prodDetails.filter(x => x.gtin == values.gtin);
                    var  prod = prds && prds.length > 0 ? prds[0] : null;
                    if(prod){
                        new_size = prod.sds_code;
                    }
                    if (!sizeMap.has(new_size)){
                        sizeMap.set(new_size,0);
                    }
                    sizeMap.set(new_size, sizeMap.get(new_size) + 1);
                })

            // }else{

            //     if (!sizeMap.has(size)){
            //         sizeMap.set(size, packages.get(packageNo).items.size);
            //     }else{
            //         sizeMap.set(size, sizeMap.get(size) + packages.get(packageNo).items.size);
            //     }

            // }


            var number = packages.get(packageNo).items.size;
            total = parseInt(total) + parseInt(number);
        });

        updateSizes(sizeMap);

        $('#selectedQuantity').text(total);

        this.sizes.clear();
    }

}

class EpcList{
    constructor(){
        this.epcs               = new Set();
    }

    add = function (tag){

        if(!this.epcs.has(tag.epc)){

            //epc'yi ekle
            this.epcs.add(tag.epc);

        }

    };
}


function getSize(epc) {

    sizeVal = false;

    if(epc && epc.length  >= 24 ){
        var sizeHex     = epc.substring(12, 15);
        var sizeVal     = parseInt(sizeHex, 16);
        sizeVal         = sizeVal >> 3;
    }

    var size = 'UND';

    switch (sizeVal) {

        case 1 : size = 'XS - 01 - 100';    break;
        case 2 : size = 'S';                break;
        case 3 : size = 'M';                break;
        case 4 : size = 'L';                break;
        case 5 : size = 'XL';               break;
        case 6 : size = 'XXL';              break;
        case 7 : size = 'XXXL';             break;
        case 8 : size = 'XXS';              break;
        case 10 : size = 'XS';              break;
        case 32 : size = '32';              break;
        case 34 : size = '34';              break;
        case 36 : size = '36';              break;
        case 38 : size = '38';              break;
        case 40 : size = '40';              break;
        case 42 : size = '42';              break;
        case 44 : size = '44';              break;
        case 46 : size = '46';              break;
        case 48 : size = '48';              break;
        case 75 : size = '75';              break;
        case 85 : size = '85';              break;
        case 95 : size = '95';              break;
        case 96 : size = 'L - XL';          break;
        case 97 : size = 'S-M';             break;
        case 98 : size = 'M - L';           break;
    }

    return size;
}


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
updateSelectedCount();
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
    updateSelectedCount();
    consignment.getSizes();
}

function updateSelectedCount(){
    var totEl = $('#selectedQuantity');
    totEl.html('0');
            $(".check").each(function(inx,ch) {

                if(ch.checked == true){
                    var totEl = $('#selectedQuantity');
                    var totalSelected = parseInt(totEl.html());
                    var tr = $(ch).closest('tr');
                    var cnt = totalSelected + parseInt(tr.find("td:last").html());
                    totEl.html(cnt.toString());
                }

            });
}

function updateQuantity(key, value){
    $('#packageTotal').text(value);
    $("#consignmentList tbody").find('tr:first').find("td[id^='count']")[0].innerText = value;
}

function updateTotalQuantity(value){
    $('#totalQuantity').text(value);
}

function updateID(key, value){
    // debugger;
    var dd = $("input[package-id='" + key + "']");
    var df = $("input[package-id='" + key + "']").attr('package-id', value);
    $( "tr[id='row_0']" ).attr('id',"row_" + value.toString())
    $( "td[id='loadtype_0']" ).attr('id',"loadtype_" + value.toString());
    $( "td[id='ptype_0']" ).attr('id',"ptype_" + value.toString());
    $( "td[id='size_0']" ).attr('id',"size_" + value.toString())

    //$("input[package-id='" + key + "']").attr('package-id', value);
    $("#customCheck"+key).attr('package-id', value);
    $("#customCheck"+key).attr('data-id', value);
    $("#customCheck"+key).val(value);
    $("#description_0").attr('id',"description_" + value);
    $("#count_0").attr('id', "count_" + value);
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

        var package = {};
        package.id              = item.id;
        package.itemsCount      = item.items.length;
        package.load_type       = item.load_type;
        package.box_type_id     = item.box_type_id;
        package.items           = item.items;
        package.packageNo       = item.package_no;
        package.model           = item.model == null ? '-' : item.model;
        package.size            = item.size;

        consignment.addPackage(package);

    });

    consignment.addHtml();

}

async function insertFromDbItem(data){

    consignment.epcs            = new Set();
    await  $.each(data, function(i, item) {
        // insertRow(item.epc,item.package_no,item.package_id)
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
