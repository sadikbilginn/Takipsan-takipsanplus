class Item{
    constructor(epc,packageNo, timestamp){
        this.epc        = epc;
        this.packageNo  = packageNo;
        this.timestamp  = new Date(timestamp);
    }
}

class Package{
    constructor(index, itemsCount, lastUpdated){
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
        this.epcs               = new Map();
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

        }
    };

    addItem = function (tag){

        //Paket numarasını al
        tag.packageNo = parseInt(tag.packageNo);

        //Paket son ekleme tarihi güncelle
        this.packages.get(tag.packageNo).lastUpdated = tag.created_date;

        //epc'yi ekle
        this.epcs.set(tag.epc, new Item(tag.epc, tag.packageNo,  tag.created_date));

    };

    addHtml = function (){

        this.packages.forEach(function (item, index) {
            //Ekrana bas
            insertRow(index, item.model, item.size);
            updateQuantity(index, item.itemsCount);
        });

        updateTotalQuantity(this.epcs.size);
    };

    checkClose = function(boxCloseTime){

        let consignment =  this;

        //Son paket kapalı mı?
        if(!this.isLastBoxClosed){

            //Son paket sonrası geçen zaman kontrolü
            if ((new Date().getTime() -  this.packages.get(this.packageNo).lastUpdated) > (boxCloseTime * 1000)){
                //console.log('Closing Box');

                //paketi kapat
                this.isLastBoxClosed = true;
                this.packages.get(this.packageNo).isClosed = true;

                //Paket durumunu güncelle
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

        updateSelectedQuantity();

    };

    allClose = function(){

        //Son paket kapalı mı?
        if(!this.isLastBoxClosed){

            //paketi kapat
            this.isLastBoxClosed = true;
            this.packages.get(this.packageNo).isClosed = true;

            //Paket durumunu güncelle
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

