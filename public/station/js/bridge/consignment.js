class Item{
    constructor(epc, timestamp){
        this.epc        = epc;
        this.timestamp  = new Date(timestamp);
    }
}

class Package{
    constructor(index, itemsCount, lastUpdated){
        this.id             = 0;
        this.items          = new Map();
        this.lastUpdated    = lastUpdated;
        this.itemsCount     = itemsCount;
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

                    insertRow(this.packageNo, last_model, last_size);
                }

                //Pakete epc bilgilerini ekle
                this.packages.get(this.packageNo).items.set(tag.epc, new Item(tag.epc, tag.firstSeenTime));

                //Paket son ekleme tarihi güncelle
                this.packages.get(this.packageNo).lastUpdated = tag.firstSeenTime;

                //epc'yi ekle
                this.epcs.add(tag.epc);

                //Miktarı güncelle
                updateQuantity(this.packageNo, this.packages.get(this.packageNo).items.size);

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
            this.packages.set(tag.packageNo, new Package(tag.packageNo, tag.itemsCount,  null));

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

        //Pakete epc bilgilerini ekle
        this.packages.get(tag.packageNo).items.set(tag.epc, new Item(tag.epc, tag.created_date));

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
            insertRow(index, item.model, item.size);
            updateQuantity(index, item.itemsCount);
            updateID(index, item.id);
        });

    };

    checkClose = function(boxCloseTime){

        //Son paket kapalı mı?
        if(!this.isLastBoxClosed){

            //Son paket sonrası geçen zaman kontrolü
            if ((new Date().getTime() -  this.packages.get(this.packageNo).lastUpdated) > (boxCloseTime * 1000)){
                //console.log('Closing Box');

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

            var packageNo = parseInt(this.value);
            var size = packages.get(packageNo).size;

            if(size == "" || size == null || size == "-"){

                packages.get(packageNo).items.forEach((values, keys)=>{

                    var new_size = getSize(keys);

                    if (!sizeMap.has(new_size)){
                        sizeMap.set(new_size,0);
                    }
                    sizeMap.set(new_size, sizeMap.get(new_size) + 1);
                })

            }else{

                if (!sizeMap.has(size)){
                    sizeMap.set(size, packages.get(packageNo).items.size);
                }else{
                    sizeMap.set(size, sizeMap.get(size) + packages.get(packageNo).items.size);
                }

            }


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

