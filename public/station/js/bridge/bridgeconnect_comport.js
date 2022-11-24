var br, readerId;
var readerMode = 'consignment';
var readerStatus = false;
var connectReaderStatus = false;
var consignmentLoading  = false;
var comPorts        = [];
var comOldAddress = readerIp;
var comRetry = 0;
var comRetryMax = 50;


(async () => {
    await connectBridge();
    await createReader();
    if(deviceType != 'box_station'){
        await createNetworkManager();
    }
})();

function connectBridge(){

    br = new Bridge("ws://" + bridgeIp + ":"+ bridgePort +"/ws/ws");

    br.addEventListener("onstatuschange", function (e) {

        console.log("Bridge >>> " + e.detail);

        switch (e.detail) {
            case br.CONNECTING 	: console.log("Bridge >>> CONNECTING"); break;
            case br.OPEN 		: console.log("Bridge >>> OPEN"); break;
            case br.CLOSING 	: console.log("Bridge >>> CLOSING"); break;
            case br.CLOSED 		: console.log("Bridge >>> CLOSED"); break;
        }

    }, false);

}

function createReader(){

    console.log(">>>>>>> create reader");

    br.createReaderPromise(reader, readerIp, onTagRead)
        .then(function(result){

            readerId = result.id;

            if(deviceSet.read_type_id == 0){

                deviceSet.search_mode 	        ? br.readers[readerId].settings.searchMode 				= deviceSet.search_mode 		 : '';
                deviceSet.reader_mode 	        ? br.readers[readerId].settings.readerMode 				= deviceSet.reader_mode  		 : '';
                deviceSet.session 		        ? br.readers[readerId].settings.session 				= deviceSet.session 		     : '';
                deviceSet.estimated_population   ? br.readers[readerId].settings.tagPopulation 			= deviceSet.estimated_population : 0;
                deviceSet.string_set             ? br.readers[readerId].settings.settingsStr 			= deviceSet.string_set           : '';

            }else{

                var mode = deviceSet.read_type;

                mode.search_mode 	        ? br.readers[readerId].settings.searchMode 				= mode.search_mode 		    : '';
                mode.reader_mode 	        ? br.readers[readerId].settings.readerMode 				= mode.reader_mode  		: '';
                mode.session 		        ? br.readers[readerId].settings.session 				= mode.session 		        : '';
                mode.estimated_population   ? br.readers[readerId].settings.tagPopulation 			= mode.estimated_population : 0;
                mode.string_set             ? br.readers[readerId].settings.settingsStr 			= mode.string_set           : '';

            }

            if(deviceSet.common_power  == 1){
                br.readers[readerId].settings.useCommonPowerSettings 	= true;
                br.readers[readerId].settings.commonReadPower 			= parseFloat(JSON.parse(deviceSet.antennas).read);
                br.readers[readerId].settings.commonWritePower 			= parseFloat(JSON.parse(deviceSet.antennas).write);
            }else{
                br.readers[readerId].settings.useCommonPowerSettings 	= false;
                br.readers[readerId].settings.antennas					= [];

                $.each(JSON.parse(deviceSet.antennas) ,function(index, value){
                    antenna = new Antenna();
                    antenna.portNumber = index;
                    antenna.isActive = true;
                    antenna.readPower = parseFloat(value.read);
                    antenna.writePower = parseFloat(value.write);
                    br.readers[readerId].settings.antennas.push(antenna);
                });
            }

            br.readers[readerId].settings.tagReportInterval = 250;

            connectReader(readerId);

        })
        .catch(function(reason){
            Swal.fire({
                title: 'Hata!',
                text: 'Okuyucu oluşturulurken bir hata ile karşılaşıldı.',
                icon: 'error',
                confirmButtonText: 'Tamam'
            });
        });

}

function connectReader(id){

    console.log("connecting reader "+ id);
    console.log("COMMM -> CONNECT RADER -> " + id);

    br.readers[id].connectPromise().then(function(result){

        console.log("COMMM -> CONNECT RADER OK -> " + id);

        console.log(result);

        $("#findPackage").prop("disabled", false);

        connectReaderStatus = true;

        if(connectReaderStatus && consignmentLoading){
            $("#startStop").prop("disabled", false);
        }

        if(comOldAddress != readerIp){
            console.log("YENIII");

            axios({
                url   : stationAjaxUrl,
                method: 'post',
                data  : {
                    process         : 'deviceChangeAddress',
                    ip              : readerIp,
                    id              : deviceSet.id
                }
            }).then(function (response) {
                console.log('save');
            }).catch(function (error) {
                console.log(error)
            });
        }

    }).catch(function(reason){
        console.log("connecting reader failed "+ reason);
        $("#startStop").prop("disabled", true);
        $("#findPackage").prop("disabled", true);

        console.log("COMMM -> HATAYA DÜŞTÜ");

        if(reader == 'thingmagic' && comRetry <= comRetryMax){

            console.log("COMMM -> 1");


            if(comPorts.length > 0 && (comPorts.length - 1) >= comRetry){
                console.log("COMMM -> 2");

                if(comPorts[comRetry].description.substring(0, 3) == 'M6E'){

                    if(deviceType == 'box_station'){
                        readerIp = comPorts[comRetry].portName;
                    }else{
                        readerIp = "dev/" + comPorts[comRetry].portName;
                    }
                }



                console.log("DENEME ---- " + comRetry);
                console.log("COMPORT ---- " + readerIp);

                createReader();

                comRetry++;

            }else{

                console.log("COMMM -> 3");

                br.getComPortsPromise()
                    .then(function(result){

                        console.log("COMMM -> 4");

                        comRetry = 0;

                        if(result.length > 0){
                            console.log("COMMM -> 5");

                            comPorts = result;

                            if(comPorts[comRetry].description.substring(0, 3) == 'M6E'){

                                if(deviceType == 'box_station'){
                                    readerIp = comPorts[comRetry].portName;
                                }else{
                                    readerIp = "dev/" + comPorts[comRetry].portName;
                                }
                            }


                            console.log("DENEME ---- " + comRetry);
                            console.log("COMPORT ---- " + readerIp);

                            createReader();

                            comRetry++;
                        }

                    })
                    .catch(function(reason){
                        console.log("getComPortsPromise failed "+ reason);
                    });

            }


        }else{
            Swal.fire({
                title: 'Hata!',
                text: 'Okuyucuya bağlanırken bir hata ile karşılaşıldı.',
                icon: 'error',
                confirmButtonText: 'Tamam',
                footer: '<a href="javascript:window.location.reload();">Tekrar dene?</a>'
            });
        }

    });
}

function disconnectReader(id){

    console.log("disconnecting reader "+ id);

    br.readers[id].disconnectPromise()
        .then(function(result){
            $("#startStop").prop("disabled", true);
            $("#findPackage").prop("disabled", true);
        })
        .catch(function(reason){
            console.log("disconnecting reader failed "+ reason);
            $("#startStop").prop("disabled", false);
            $("#findPackage").prop("disabled", false);
        });
}

function startReader(id){

    $('#startStop').attr('disabled', true);

    console.log(">>>>>>> start reader");

    if(br.readers[1].type == 'thingmagic'){
        gpio_call('start');
    }

    br.readers[id].startPromise()
        .then(function(result){

            if(br.readers[1].type == 'impinj'){
                gpio_call('start');
            }

            recordStatus = true;
            readerStatus = true;

            $('#startStop').attr('data-default' , 'stop');
            $('#startStop').html(stopBtnText);
            $('#startStop').addClass('btn-stop');

            console.log(">>>>>>> starting " + id);

            $('#startStop').attr('disabled', false);

        })
        .catch(function(reason){
            console.log(">>>>>>> starting failed " + reason);
            gpio_call('stop');

            Swal.fire({
                title: 'Hata!',
                text: 'Okuma başlatılırken bir hata ile karşılaşıldı.',
                icon: 'error',
                confirmButtonText: 'Tamam',
            });

            $('#startStop').attr('disabled', false);
        });

}

function stopReader(id){

    recordStatus = false;

    console.log(">>>>>>> stop reader "+ id);

    $('#startStop').attr('disabled', true);


    br.readers[id].stopPromise()
        .then(function(result){

            readerStatus = false;

            gpio_call('stop');

            $('#startStop').attr('data-default' , 'start');
            $('#startStop').html(startBtnText);
            $('#startStop').removeClass('btn-stop');

            console.log(">>>>>>> stopped " + id);

            $('#startStop').attr('disabled', false);

        })
        .catch(function(reason){
            console.log(">>>>>>> stopped failed" + reason);
            recordStatus = true;
            $('#startStop').attr('disabled', false);

            Swal.fire({
                title: 'Hata!',
                text: 'Okuma başlatılırken bir hata ile karşılaşıldı.',
                icon: 'error',
                confirmButtonText: 'Tamam',
            });
        });
}

function onTagRead(tags){

    if(recordStatus == true){
        $.each(tags, function(index, tag) {
            console.log(">>>>>>>>TagRead>>>>>>>>>" + tag.epc);

            switch (readerMode) {

                case 'find' :

                    buffer.add(tag);

                    break;

                default :   consignment.add(tag);   break;
            }

        });
    }

}

function gpio_call(type) {

    if(deviceType != 'box_station2'){
        switch (type) {
            case 'start' : gpio_set(gpioStart); break;
            case 'stop'  : gpio_set(gpioStop);  break;
            case 'error' : gpio_set(gpioError); break;
        }
    }

}

function gpio_set(gs) {

    var arr = gs.split(",");

    arr.forEach(function (item) {
        var value = item.split("=");

        switch (value[1]) {
            case 'on'   :   br.readers[readerId].outputOn(parseInt(value[0]));  break;
            case 'off'  :   br.readers[readerId].outputOff(parseInt(value[0])); break;
        }

    });

}

function createNetworkManager() {

    console.log(">>>>>>> createNetworkManager <<<<<<<");
    br.createNetworkManagerPromise()
        .then(function(result){

            console.log(result);
            var networkInfo     = false;
            var wifi            = false;
            var ethernet        = false;

            //Interfaces
            $.each(result.interfaces,function(index, value){
                if(value.type == 'wifi'){
                    if(value.state == 'unavailable'){
                        wifi = false;
                        $("#customSwitchWifi").prop('checked', false);
                    }else{
                        wifi = value;
                        $('#customSwitchWifi').prop('checked', true);
                    }
                }

                if(value.type == 'ethernet'){
                    if(value.state == 'connected'){
                        ethernet = value;
                        console.log('eth>>>>>>>>>>>>>>>');
                        console.log(ethernet.ipv4.address);
                        console.log('<<<<<<<<<<<<<<<<<<eth');
                    }else{
                        ethernet = false;
                    }
                }
            });

            if(wifi){
                networkInfo = wifi;
            }
            if(ethernet){
                networkInfo = ethernet;
            }

            $('#networkIcon').attr('src' ,'/station/img/network/earth-globe.svg');

            //Set Ethernet Icon
            if(networkInfo.type == 'ethernet'){
                $('#networkIcon').attr('src' ,'/station/img/network/ethernet.svg');
            }

            //Set Ethernet Icon
            if(networkInfo.type == 'wifi'){
                if(networkInfo.bars && networkInfo.bars != '' && networkInfo.bars != undefined && networkInfo.bars != null){
                    $('#networkIcon').attr('src' ,'/station/img/network/wifi-' + networkInfo.bars + '.svg');
                }
            }


        })
        .catch(function(reason){
            console.log(reason);
        });
}
