var br, readerId;

(async () => {
    await connectBridge();
    await createReader();
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

    br.createReaderPromise(reader, readerIp)
        .then(function(result){

            readerId = result.id;

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

    br.readers[id].connectPromise().then(function(result){

        console.log(result);

        //maksimum anten sayısını belirler
        for (var i = 1 ; i <= 4 ; i++){
            console.log(br.readers[readerId].features.antennaCount);
            console.log(i);
            if(br.readers[readerId].features.antennaCount < i){
                $('[id^=antenna_'+i+']').prop('disabled', true);
                $('.antenna-d-'+i).addClass('d-none');
            }
        }

        //sadece reader a uygun ayar setlerini gösteriyoruz
        if(br.readers[id].features.brand == 'Impinj'){
            $('#read_type_id option[data-reader="thingmagic"]').remove();
        }else{
            $('#read_type_id option[data-reader="impinj"]').remove();
        }

        //reader üzerinden desteklenen modları çeker
        if(br.readers[id].features.readModes.length > 0){
            $('#reader_mode').find('option').remove()
            $('#reader_mode').append('<option value="" selected>Seçiniz</option>');
            for (var i = 0 ; i <= br.readers[id].features.readModes.length - 1; i++) {
                $('#reader_mode').append('<option '+(br.readers[id].features.readModes[i]==br.readers[id].settings.readerMode?'selected':'')+' value="'+br.readers[id].features.readModes[i]+'"> '+br.readers[id].features.readModes[i] +'</option>');
            }
        }

        //reader üzerinden desteklenen arama modlarını çeker
        if(br.readers[id].features.searchModes.length > 0){
            $('#search_mode').find('option').remove();
            $('#search_mode').append('<option value="" selected>Seçiniz</option>');
            for (var i = 0 ; i <= br.readers[id].features.searchModes.length - 1; i++) {
                $('#search_mode').append('<option value="'+br.readers[id].features.searchModes[i]+'"> '+br.readers[id].features.searchModes[i] +'</option>');
            }
        }


    }).catch(function(reason){
        console.log("connecting reader failed "+ reason);
        Swal.fire({
            title: 'Hata!',
            text: 'Okuyucuya bağlanırken bir hata ile karşılaşıldı.',
            icon: 'error',
            confirmButtonText: 'Tamam',
            footer: '<a href="javascript:window.location.reload();">Tekrar dene?</a>'
        });
    });
}
