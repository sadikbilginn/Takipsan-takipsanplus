class Item{
    constructor(epc, timestamp){
        this.epc        = epc;
        this.timestamp  = new Date(timestamp);
        this.size       = this.getSize(epc);

    }

    getSize = function(epc){
        var size = false;
        if(epc && epc.length  >= 24 ){
            var sizeHex     = epc.substring(12, 15);
            var sizeVal     = parseInt(sizeHex, 16);
            sizeVal         = sizeVal >> 3;
            size            = this.sizes(sizeVal);
        }
        return size;
    };

    sizes = function(sizeVal){

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
}

class Package{
    constructor(index, lastUpdated){
        this.items          = new Map();
        this.lastUpdated    = lastUpdated;
        this.model          = null;
        this.size           = null;
        this.isClosed       = false;
        this.sizes          = new Map();
    }
}

class Consignment{
    constructor(){
        this.packages           = new Map();
        this.epcs               = new Set();
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

                //Son paket kapal?? m???
                if (this.isLastBoxClosed){
                    //console.log("lastBoxClose");

                    //Paket say??s??n?? bir art??r
                    this.packageNo++;

                    //Yeni paket olu??tur
                    this.packages.set(this.packageNo, new Package(this.packageNo,  null));

                    //Paketi a????k hale getir
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

                //Paket son ekleme tarihi g??ncelle
                this.packages.get(this.packageNo).lastUpdated = tag.firstSeenTime;

                //epc'yi ekle
                this.epcs.add(tag.epc);

                //Miktar?? g??ncelle
                updateQuantity(this.packageNo, this.packages.get(this.packageNo).items.size);

                //Toplam okunan epc say??s??
                updateTotalQuantity(this.epcs.size);

                //Beden bilgisi ekle
                if (!this.packages.get((this.packageNo)).sizes.has(this.packages.get(this.packageNo).items.get(tag.epc).size)){
                    this.packages.get((this.packageNo)).sizes.set(this.packages.get(this.packageNo).items.get(tag.epc).size, 1);
                } else{
                    this.packages.get((this.packageNo)).sizes.set(this.packages.get(this.packageNo).items.get(tag.epc).size, this.packages.get((this.packageNo)).sizes.get(this.packages.get(this.packageNo).items.get(tag.epc).size)+1);
                }

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

    addFromDB = function (tag){

        //console.log("tag.packageNo>>> " + tag.packageNo);

        //Paket numaras??n?? al
        tag.packageNo = parseInt(tag.packageNo);

        //Paket kontrol?? yap yoksa i??eri gir
        if (!this.packages.has(tag.packageNo)){

            //Paket numaras??n?? bir art??r
            this.packageNo = this.packageNo + 1;

            //Yeni paket olu??tur
            this.packages.set(tag.packageNo, new Package(tag.packageNo,  null));

            //Paketi kapat
            this.isLastBoxClosed = true;
            this.packages.get(tag.packageNo).isClosed = true;

            //Model Beden Ekle
            this.packages.get(tag.packageNo).model = tag.model;
            this.packages.get(tag.packageNo).size  = tag.size;

            //Ekrana bas
            //insertRow(tag.packageNo, tag.model, tag.size);
        }

        //Pakete epc bilgilerini ekle
        this.packages.get(tag.packageNo).items.set(tag.epc, new Item(tag.epc, tag.firstSeenTime));

        //Paket son ekleme tarihi g??ncelle
        this.packages.get(tag.packageNo).lastUpdated = tag.firstSeenTime;

        //epc'yi ekle
        this.epcs.add(tag.epc);

        //Miktar?? g??ncelle
        //updateQuantity(tag.packageNo, this.packages.get(tag.packageNo).items.size);

        //Toplam okunan epc say??s??
        //updateTotalQuantity(this.epcs.size);

        //Beden bilgisi ekle
        if (!this.packages.get((this.packageNo)).sizes.has(this.packages.get(this.packageNo).items.get(tag.epc).size)){
            this.packages.get((this.packageNo)).sizes.set(this.packages.get(this.packageNo).items.get(tag.epc).size, 1);
        } else{
            this.packages.get((this.packageNo)).sizes.set(this.packages.get(this.packageNo).items.get(tag.epc).size, this.packages.get((this.packageNo)).sizes.get(this.packages.get(this.packageNo).items.get(tag.epc).size)+1);
        }

        this.getSizes();

        //console.log("epc added: " + tag.epc);
    };

    addHtml = function (){


        this.packages.forEach(function (item, index) {
            //Ekrana bas
            insertRow(index, item.model, item.size);
            updateQuantity(index, item.items.size);
        });

        updateTotalQuantity(this.epcs.size);
    };

    checkClose = function(boxCloseTime){

        let consignment =  this;

        //Son paket kapal?? m???
        if(!this.isLastBoxClosed){

            //Son paket sonras?? ge??en zaman kontrol??
            if ((new Date().getTime() -  this.packages.get(this.packageNo).lastUpdated) > (boxCloseTime * 1000)){
                //console.log('Closing Box');

                //paketi kapat
                this.isLastBoxClosed = true;
                this.packages.get(this.packageNo).isClosed = true;

                //Paket durumunu g??ncelle
                modalClose(this.packageNo);

                //console.log(this.packages.get(this.packageNo).items.values());
                axios({
                    url   : stationAjaxUrl,
                    method: 'post',
                    data  : {
                        process         : 'sendPackage',
                        consignmentId   : $('#consignments').val(),
                        orderId         : $('#consignments option:selected').attr('data-order'),
                        package         : this.packageNo,
                        model           : $('#consignmentList tbody tr#'+this.packageNo+' td:nth-child(4)').text(),
                        size            : $('#consignmentList tbody tr#'+this.packageNo+' td:nth-child(5)').text(),
                        data            : Array.from(this.packages.get(this.packageNo).items.values())
                    }
                }).then(function (response) {
                    console.log('save');
                }).catch(function (error) {
                    console.log(error)
                });

                if(auto_print == 1){
                    autoPrint();
                }

            } else {
                console.log('we have time to close');
            }

        }

        this.getSizes();

        updateSelectedQuantity();

    };

    allClose = function(){

        //Son paket kapal?? m???
        if(!this.isLastBoxClosed){

            //paketi kapat
            this.isLastBoxClosed = true;
            this.packages.get(this.packageNo).isClosed = true;

            //Paket durumunu g??ncelle
            modalClose(this.packageNo);

            //console.log(this.packages.get(this.packageNo).items.values());
            axios({
                url   : stationAjaxUrl,
                method: 'post',
                data  : {
                    process         : 'sendPackage',
                    consignmentId   : $('#consignments').val(),
                    orderId         : $('#consignments option:selected').attr('data-order'),
                    package         : this.packageNo,
                    data            : Array.from(this.packages.get(this.packageNo).items.values())
                }
            }).then(function (response) {
                console.log('save');
            }).catch(function (error) {
                console.log(error)
            });

        }

    };

    getSizes = function () {

        //console.log('get size');

        let packages    = this.packages;
        var allCheck    = new Array();
        var sizeMap     = new Map();

        $('input[type=checkbox].check:checked').each(function () {
            allCheck.push(this.value);
        });

        packages.forEach( function(pack, key2) {
            pack.sizes.forEach( function (value, key) {
                allCheck.forEach( function (check) {
                    if(check == key2){
                        if (!sizeMap.has(key)){
                            sizeMap.set(key,0);
                        }
                        sizeMap.set(key, sizeMap.get(key) + value);
                    }
                });
            })
        });

        updateSizes(sizeMap);

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

