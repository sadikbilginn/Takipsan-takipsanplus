class Bridge extends WebSocket {
    //var socket = {};
    //var health;

    readers = new Map();
    gpios = [];
    comports = [];
    isConnected = false;
    heartbeatEnabled = false;
    readerIndex = 0;
    gpioIndex = 0;

    //statusChangeEvent = null;
    //onConnectEvent = null;
    //statusChangeEvent = new CustomEvent("onstatuschange", { detail: this.readyState });

    constructor(url, protocols){
        super(url, protocols);


        this.onopen = this.onOpenHandler;
        this.onclose = this.onCloseHandler;
        this.onmessage = this.onMessageHandler;
        this.onerror = this.onErrorHandler;



        this.readers = new Map();
        this.gpios = new Map();
        this.isConnected = false;
        this.heartbeatEnabled = false;
        this.readerIndex = 0;
        if (this.heartbeatEnabled) this.handleHeartbeat();
        //console.log('Bridge: connecting');
        //console.log(this);
    }

    /*connect = function() {
        try{
            //console.log(this.socket);
            //console.log('trying to connect');
            this.socket = new WebSocket(this.url);
            this.socket.onopen = this.onOpen;
            this.socket.onmessage = this.onMessage;
            this.socket.onclose = this.onClose;
            this.socket.onerror = this.onError;
            this.isConnected = true;
            if (this.heartbeatEnabled) this.handleHeartbeat();
            //console.log('tried to connect');
            //console.log(this.socket);
            return true;
        } catch (error){
            //console.log(error);
            return false;
        }
    }*/

    onOpenHandler = function(event){
        this.dispatchEvent(new CustomEvent("onstatuschange", {
            detail: this.readyState
        }));
        //console.log("opened");
        //console.log(event);
    }

    onMessageHandler = function(event){
        //console.log("message received");
        //console.log(event);
        var msg;
        try{
            msg = JSON.parse(event.data);

            // reader responses
            if(msg.type == "createreader"){
                //console.log("create reader response");
                //console.log(this);
                if(msg.body.isSuccess){
                    this.readers[msg.body.id].isCreated = true;
                }

                this.readers[msg.body.id].createCallback(msg.body);

            } else if(msg.type == "connectreader"){
                //console.log("connect reader response");
                //console.log(this);
                if(msg.body.isSuccess){

                    this.readers[msg.body.id].isConnected = true;
                    this.readers[msg.body.id].features = msg.body.readerFeatures;
                }

                this.readers[msg.body.id].connectCallback(msg.body);

            } else if(msg.type == "disconnectreader"){
                //console.log("disconnect reader response");
                //console.log(this);
                if(msg.body.isSuccess){
                    this.readers[msg.body.id].isConnected = false;
                }

                this.readers[msg.body.id].connectCallback(msg.body);

            } else if(msg.type == "startreader"){
                //console.log("start reader response");
                //console.log(this);
                if(msg.body.isSuccess){
                    //this.readers[msg.body.id].isConnected = false;
                }

                this.readers[msg.body.id].startCallback(msg.body);

            } else if(msg.type == "stopreader"){
                //console.log("stop reader response");
                //console.log(this);
                if(msg.body.isSuccess){
                    //this.readers[msg.body.id].isConnected = false;
                }

                this.readers[msg.body.id].stopCallback(msg.body);

            } else if(msg.type == "tagreport"){
                //console.log("tag reported:");
                //console.log(this);
                if(msg.body.isSuccess){
                    this.readers[msg.body[0].readerId].isCreated = true;
                }

                this.readers[msg.body[0].readerId].onTagRead(msg.body);
            }
            // gpio responses
            else if(msg.type == "creategpio"){
                //console.log("create gpio response");
                //console.log(this);
                if(msg.body.isSuccess){
                    this.gpios[msg.body.id].isCreated = true;
                }

                this.gpios[msg.body.id].createCallback(msg.body);

            } else if(msg.type == "connectgpio"){
                //console.log("connect gpio response");
                //console.log(this);
                if(msg.body.isSuccess){

                    this.gpios[msg.body.id].isConnected = true;
                }

                this.gpios[msg.body.id].connectCallback(msg.body);

            } else if(msg.type == "disconnectgpio"){
                //console.log("disconnect gpio response");
                //console.log(this);
                if(msg.body.isSuccess){
                    this.gpios[msg.body.id].isConnected = false;
                }

                this.gpios[msg.body.id].connectCallback(msg.body);

            } else if(msg.type == "inputread"){
                //console.log("disconnect gpio response");
                //console.log(this);
                if(msg.body.isSuccess){
                    //this.gpios[msg.body.id].isConnected = false;
                }

                this.gpios[msg.body.id].inputReadCallback(msg.body);

            } else if(msg.type == "inputlisten"){
                //console.log("disconnect gpio response");
                //console.log(this);
                if(msg.body.isSuccess){
                    //this.gpios[msg.body.id].isConnected = false;
                }

                this.gpios[msg.body.id].inputListenCallback(msg.body);

            } else if(msg.type == "inputchanged"){
                console.log("input changed");
                console.log(this);
                console.log(msg);
                if(msg.body.isSuccess){
                    //this.readers[msg.body[0].gpioId].isCreated = true;
                }

                this.gpios[msg.body.gpioId].onInputChange(msg.body);

            } else if(msg.type == "getBridgeSettings"){
                console.log("getBridgeSettings response");
                console.log(msg.body);
                if(msg.body.type == "Bridge Settings"){
                    this.bridgeSettings = msg.body;
                }
                this.bridgeSettingsCallback(msg.body);

            } else if(msg.type == "getcomports"){
                console.log("getcomports response");
                console.log(msg.body);

                this.comports = msg.body;

                this.getComPortsCallBack(msg);

            } else if(msg.type == "nm_create"){
                console.log("createnetworkmanager response");
                console.log(this);
                console.log(msg);
                if(msg.body.isSuccess){
                    //this.networkManager = new NetworkManager();
                    this.networkManager.isCreated = true;
                    //this.networkManager.hostname = msg.body.hostname;
                    this.networkManager.update(msg.body);
                    console.log("isSuccess ended");
                }

                this.networkManager.createCallback(msg.body);

            } else if(msg.type == "set_app_url"){
                console.log("set_app_url response");
                console.log(this);
                console.log(msg);
                if(msg.body.isSuccess){
                    this.bridgeSettings.appURL = msg.body.appURL;

                }

                this.set_app_urlCallback(msg.body);

            } else if(msg.type == "nm_set_hostname"){
                console.log("nm_set_hostname response");
                console.log(this);
                console.log(msg);
                if(msg.body.isSuccess){
                    //this.networkManager = new NetworkManager();

                    //this.networkManager.hostname = msg.body.hostname;
                    this.networkManager.update(msg.body);

                }

                this.networkManager.nm_set_hostnameCallback(msg.body);

            } else if(msg.type == "nm_wifi_up"){
                console.log("nm_wifi_up response");
                console.log(this);
                console.log(msg);
                if(msg.body.isSuccess){
                    //this.networkManager = new NetworkManager();

                    //this.networkManager.hostname = msg.body.hostname;
                    this.networkManager.update(msg.body);

                }

                this.networkManager.nm_wifi_upCallback(msg.body);

            } else if(msg.type == "nm_wifi_down"){
                console.log("nm_wifi_down response");
                console.log(this);
                console.log(msg);
                if(msg.body.isSuccess){
                    //this.networkManager = new NetworkManager();

                    //this.networkManager.hostname = msg.body.hostname;
                    this.networkManager.update(msg.body);

                }

                this.networkManager.nm_wifi_downCallback(msg.body);

            } else if(msg.type == "nm_connect_interface"){
                console.log("nm_connect_interface response");
                console.log(this);
                console.log(msg);
                if(msg.body.isSuccess){
                    //this.networkManager = new NetworkManager();

                    //this.networkManager.hostname = msg.body.hostname;
                    this.networkManager.update(msg.body);

                }

                this.networkManager.nm_connect_interfaceCallback(msg.body);

            } else if(msg.type == "nm_disconnect_interface"){
                console.log("nm_disconnect_interface response");
                console.log(this);
                console.log(msg);
                if(msg.body.isSuccess){
                    //this.networkManager = new NetworkManager();

                    //this.networkManager.hostname = msg.body.hostname;
                    this.networkManager.update(msg.body);

                }

                this.networkManager.nm_disconnect_interfaceCallback(msg.body);

            } else if(msg.type == "nm_ip_config"){
                console.log("nm_ip_config response");
                console.log(this);
                console.log(msg);
                if(msg.body.isSuccess){
                    //this.networkManager = new NetworkManager();

                    //this.networkManager.hostname = msg.body.hostname;
                    this.networkManager.update(msg.body);

                }

                this.networkManager.setIPv4Callback(msg.body);

            } else if(msg.type == "nm_connect_wifi"){
                console.log("nm_connect_wifi response");
                console.log(this);
                console.log(msg);
                if(msg.body.isSuccess){
                    //this.networkManager = new NetworkManager();

                    //this.networkManager.hostname = msg.body.hostname;
                    this.networkManager.update(msg.body);

                }

                this.networkManager.connectWifiCallback(msg.body);

            } else if(msg.type == "nm_forget_wifi"){
                console.log("nm_forget_wifi response");
                console.log(this);
                console.log(msg);
                if(msg.body.isSuccess){
                    //this.networkManager = new NetworkManager();

                    //this.networkManager.hostname = msg.body.hostname;
                    this.networkManager.update(msg.body);

                }

                this.networkManager.forgetWifiCallback(msg.body);

            } else if(msg.type == "nm_reconnect_wifi"){
                console.log("nm_reconnect_wifi response");
                console.log(this);
                console.log(msg);
                if(msg.body.isSuccess){
                    //this.networkManager = new NetworkManager();

                    //this.networkManager.hostname = msg.body.hostname;
                    this.networkManager.update(msg.body);

                }

                this.networkManager.reconnectWifiCallback(msg.body);

            } else if(msg.type == "nm_disconnect_wifi"){
                console.log("nm_disconnect_wifi response");
                console.log(this);
                console.log(msg);
                if(msg.body.isSuccess){
                    //this.networkManager = new NetworkManager();

                    //this.networkManager.hostname = msg.body.hostname;
                    this.networkManager.update(msg.body);

                }

                this.networkManager.disconnectWifiCallback(msg.body);

            }





        } catch (error){
            //console.log(error);
        }
        //console.log(msg);
        ////console.log(msg.type);
    }

    onCloseHandler = function(event){
        this.dispatchEvent(new CustomEvent("onstatuschange", {
            detail: this.readyState
        }));
        //console.log("closed");
        //console.log(event);
    }

    onErrorHandler = function(event){
        this.dispatchEvent(new CustomEvent("onstatuschange", {
            detail: this.readyState
        }));
        //console.log("error");
        //console.log(event);
    }

    sendRequest = function(request){

        this.waitForConnection(this, function(socket){
            //console.log("Sending: "+JSON.stringify(request));
            socket.send(JSON.stringify(request));
        });
    }
    waitForConnection = function (socket, wfc_callback){
        setTimeout(
            function () {
                if (socket.readyState === 1) {
                    //console.log("Connection is made")
                    if (wfc_callback != null){
                        wfc_callback(socket);
                    }
                } else {
                    //console.log("wait for connection...")
                    socket.waitForConnection(socket, wfc_callback);
                }

            }, 500); // wait 5 milisecond for the connection...
    }

    ////// NETWORK MANAGER ///////

    createNetworkManager = function(callback){

        this.networkManager = new NetworkManager(this, callback);

        var request = {};
        request.type = "nm_create";
        this.sendRequest(request);
        //this.networkManager=res;
        //return res;
    }

    createNetworkManagerPromise = function(br = this){
        //console.log(">>> 1");
        return new Promise(
            function(resolve, reject) {
                //console.log(">>> 2");
                br.createNetworkManager(
                    function(message){
                        if (message.isSuccess){
                            resolve(message);
                        } else {
                            reject(message.message);
                        }
                    }
                )
            }
        );
    }



    ////// READER ///////

    createReader = function(type, address, createCallback, onTagRead){
        //console.log(">>> 3");
        this.readerIndex++;
        var res = new Reader(this.readerIndex, this, type, address, createCallback, onTagRead);
        var request = {};
        request.type = "createreader";
        request.body = {};
        request.body.param = {};
        request.body.param.id = this.readerIndex;
        request.body.param.readerType = type;
        request.body.param.address = address;
        this.sendRequest(request);
        this.readers[this.readerIndex]=res;
        return res;
    }

    createReaderPromise = function(type, address, onTagRead, br = this){
        //console.log(">>> 1");
        return new Promise(function(resolve, reject) {
            //console.log(">>> 2");
            br.createReader(type, address,
                function(message){
                    if (message.isSuccess){
                        resolve(message);
                    } else {
                        reject(message.message);
                    }
                }, onTagRead)
        });
        //console.log(">>> 3");
    }


    // EOF READER////////

    printLabel = function(labelData){
        //console.log(">>> 3");

        var res = null;
        var request = {};
        request.type = "printLabel";
        request.body = {};
        request.body.param = labelData;

        this.sendRequest(request);
        //this.readers[this.readerIndex]=res;
        return res;
    }

    printLabelPlus2 = function(labelData){
        //console.log(">>> 3");

        var res = null;
        var request = {};
        request.type = "printLabelPlus2";
        request.body = {};
        request.body.param = labelData;

        this.sendRequest(request);
        //this.readers[this.readerIndex]=res;
        return res;
    }

    printLinesPlus2 = function(printData){
        //console.log(">>> 3");

        var res = null;
        var request = {};
        request.type = "printLinesPlus2";
        request.body = {};
        request.body.param = printData;

        this.sendRequest(request);
        //this.readers[this.readerIndex]=res;
        return res;
    }

    getBridgeSettings = function(bridgeSettingsCallback){
        this.bridgeSettingsCallback = bridgeSettingsCallback;
        var res = null;
        var request = {};
        request.type = "getBridgeSettings";
        request.body = {};
        this.sendRequest(request);
        return res;
    }
    getBridgeSettingsPromise = function(br = this){
        console.log(">>> 1");
        return new Promise(function(resolve, reject) {
            console.log(">>> 2");
            br.getBridgeSettings(
                function(message){
                    console.log(message.type);
                    if (message.type == "Bridge Settings"){
                        console.log("resolving");
                        resolve(message);
                    } else {
                        reject(message.message);
                    }
                })
        });
        //console.log(">>> 3");
    }

    getComPorts = function(getComPortsCallBack){
        this.getComPortsCallBack = getComPortsCallBack;
        var res = null;
        var request = {};
        request.type = "getcomports";
        request.body = {};
        this.sendRequest(request);
        return res;
    }

    getComPortsPromise = function(br = this){

        return new Promise(function(resolve, reject) {

            br.getComPorts(
                function(message){
                    console.log(message.type);
                    if (message.type == "getcomports"){

                        resolve(message.body);
                    } else {
                        reject(message.message);
                    }
                })
        });
        //console.log(">>> 3");
    }

    setAppURL = function(appURL, callback){

        this.set_app_urlCallback = callback;

        //var res = new NetworkManager();
        var request = {};
        request.type = "set_app_url";
        request.body = {};
        request.body.param = appURL;
        this.sendRequest(request);
        //this.networkManager=res;
        //return res;
    }

    setAppURLPromise = function(appURL, nm = this){
        //console.log(">>> 1");
        return new Promise(
            function(resolve, reject) {
                //console.log(">>> 2");
                nm.setAppURL(appURL,
                    function(message){
                        if (message.isSuccess){
                            resolve(message);
                        } else {
                            reject(message.message);
                        }
                    }
                )
            }
        );
    }



    ////// GPIO /////////

    createGpio = function(type, address, createCallback, onInputChange){
        console.log(">>> 3");
        this.gpioIndex++;
        var res = new Gpio(this.gpioIndex, this, type, address, createCallback, onInputChange);
        var request = {};
        request.type = "creategpio";
        request.body = {};
        request.body.param = {};
        request.body.param.id = this.gpioIndex;
        request.body.param.gpioType = type;
        request.body.param.address = address;
        this.sendRequest(request);
        this.gpios[this.gpioIndex]=res;
        return res;
    }

    createGpioPromise = function(type, address, onInputChange, br = this){
        //console.log(">>> 1");
        return new Promise(function(resolve, reject) {
            //console.log(">>> 2");
            br.createGpio(type, address,
                function(message){
                    if (message.isSuccess){
                        resolve(message);
                    } else {
                        reject(message.message);
                    }
                }, onInputChange)
        });
        //console.log(">>> 3");
    }

    /////// EOF GPIO

    handleHeartbeat = function() {
        //console.log(this.heartbeatTimer);
        if (this.heartbeatEnabled){
            if (!this.heartbeatTimer){
                this.heartbeatTimer = setInterval(this.heartbeat.bind(this), 2000);
            }
        } else {
            if(this.heartbeatTimer){
                this.heartbeatTimer = clearInterval(this.heartbeatTimer);
            }
        }
        //console.log(this.heartbeatTimer);
    }

    heartbeat = function() {
        if (!this.socket) {
            //console.log(this.socket);
            //console.log('socket null');
        } else if (this.socket.readyState !== 1){
            //console.log('socket not ready');
        } else {
            var request = {}
            request.type = "ping";
            this.socket.send(JSON.stringify(request));
            //console.log('gup>');
        }
        //console.log(">>>")
    }






}

class Gpio {
    constructor(id, socket, type, address, createCallback, onInputChange){
        this.id = id;
        this.socket = socket;
        this.type = type;
        this.address = address;
        this.isCreated = false;
        this.isConnected = false;
        //this.settings = new ReaderSettings();
        //this.features = new ReaderFeatures();
        this.createCallback = createCallback;
        this.onInputChange = onInputChange;
    }

    connect = function (connectCallback) {
        //console.log("connectingGPIO");
        this.connectCallback = connectCallback;

        //console.log("connecting3");
        var request = {};
        //console.log("connecting4");
        request.type = "connectgpio";
        request.body = {};
        request.body.id = this.id.toString();
        //request.body.param = {};
        //request.body.param = this.settings;
        //console.log(">>>>>>>>>>>>>>>>>>>>>");
        //console.log(request);
        this.socket.sendRequest(request);
        //console.log("connecting5");

    }



    connectPromise = function(io = this){
        return new Promise(function(resolve, reject) {
            io.connect(function(message){
                if (message.isSuccess){
                    resolve(message);
                } else {
                    reject(message.message);
                }
            })
        });
    }

    disconnect = function (connectCallback) {
        this.connectCallback = connectCallback;
        var request = {};
        request.type = "disconnectgpio";
        request.body = {};
        request.body.id = this.id.toString();
        this.socket.sendRequest(request);
    }



    disconnectPromise = function(io = this){
        return new Promise(function(resolve, reject) {
            //console.log("disconnecting22");
            io.disconnect(function(message){
                if (message.isSuccess){
                    resolve(message);
                } else {
                    reject(message.message);
                }
            })
        });
    }

    getDeviceId = function(getDeviceIdCallback){
        this.getDeviceIdCallback = getDeviceIdCallback;
        var request = {};
        request.type = "getdeviceid";
        request.body = {};
        request.body.id = this.id.toString();
        this.socket.sendRequest(request);

    }

    getVersion = function(getVersionCallback){
        this.getVersionCallback = getVersionCallback;
        var request = {};
        request.type = "getversion";
        request.body = {};
        request.body.id = this.id.toString();
        this.socket.sendRequest(request);

    }

    ping = function(pingCallback){
        this.pingCallback = pingCallback;
        var request = {};
        request.type = "pinggpio";
        request.body = {};
        request.body.id = this.id.toString();
        this.socket.sendRequest(request);
    }

    outputOn = function(out, outputOnCallback){
        this.outputOnCallback = outputOnCallback;
        var request = {};
        request.type = "outputon";
        request.body = {};
        request.body.id = this.id.toString();
        request.body.param = out;
        this.socket.sendRequest(request);

    }

    outputOff = function(out, outputOffCallback){
        this.outputOffCallback = outputOffCallback;
        var request = {};
        request.type = "outputoff";
        request.body = {};
        request.body.id = this.id.toString();
        request.body.param = out;
        this.socket.sendRequest(request);

    }

    outputPulse = function(out, pulse, outputPulseCallback){
        this.outputOnCallback = outputPulseCallback;
        var request = {};
        request.type = "outputpulse";
        request.body = {};
        request.body.id = this.id.toString();
        request.body.param = [out, pulse];
        this.socket.sendRequest(request);

    }

    setBlinks = function(out, setBlinksCallback){
        this.setBlinksCallback = setBlinksCallback;
        var request = {};
        request.type = "setblinks";
        request.body = {};
        request.body.id = this.id.toString();
        request.body.param = out;
        this.socket.sendRequest(request);
    }

    setBlinkInterval = function(interval, setBlinkInterval){
        this.setBlinkInterval = setBlinkIntervalCallback;
        var request = {};
        request.type = "setblinkinterval";
        request.body = {};
        request.body.id = this.id.toString();
        request.body.param = interval;
        this.socket.sendRequest(request);

    }

    inputRead = function(input, inputReadCallback){
        this.inputReadCallback = inputReadCallback;
        var request = {};
        request.type = "inputread";
        request.body = {};
        request.body.id = this.id.toString();
        request.body.param = input;
        this.socket.sendRequest(request);

    }

    inputReadPromise = function(input, io = this){
        return new Promise(function(resolve, reject) {
            //console.log("disconnecting22");
            io.inputRead(input, function(message){
                if (message.isSuccess){
                    resolve(message);
                } else {
                    reject(message.message);
                }
            })
        });
    }

    inputListen = function(input, inputListenCallback){
        this.inputListenCallback = inputListenCallback;
        var request = {};
        request.type = "inputlisten";
        request.body = {};
        request.body.id = this.id.toString();
        request.body.param = input;
        this.socket.sendRequest(request);

    }

    inputListenPromise = function(input, io = this){
        return new Promise(function(resolve, reject) {
            //console.log("disconnecting22");
            io.inputListen(input, function(message){
                if (message.isSuccess){
                    resolve(message);
                } else {
                    reject(message.message);
                }
            })
        });
    }


}

class NetworkManager {
    constructor(socket, createCallback){
        this.socket = socket;
        this.isCreated=false;
        this.hostname = null;
        this.interfaces = null;
        this.createCallback = createCallback;
    }

    update = function(nm){
        console.log('updatessss>>');
        this.hostname = nm.hostname;
        this.interfaces = new Map();
        console.log('updatessss>>2');

        for (var key in nm.interfaces) {
            this.interfaces.set(key, new NetworkInterface (nm.interfaces[key]) );
        }

    }



    setHostname = function(hostname, callback){

        this.nm_set_hostnameCallback = callback;

        //var res = new NetworkManager();
        var request = {};
        request.type = "nm_set_hostname";
        request.body = {};
        request.body.param = hostname;
        this.socket.sendRequest(request);
        //this.networkManager=res;
        //return res;
    }

    setHostnamePromise = function(hostname, nm = this){
        //console.log(">>> 1");
        return new Promise(
            function(resolve, reject) {
                //console.log(">>> 2");
                nm.setHostname(hostname,
                    function(message){
                        if (message.isSuccess){
                            resolve(message);
                        } else {
                            reject(message.message);
                        }
                    }
                )
            }
        );
    }

    wifiUp = function(callback){

        this.nm_wifi_upCallback = callback;

        //var res = new NetworkManager();
        var request = {};
        request.type = "nm_wifi_up";
        request.body = {};
        this.socket.sendRequest(request);

    }

    wifiUpPromise = function(nm = this){
        //console.log(">>> 1");
        return new Promise(
            function(resolve, reject) {
                //console.log(">>> 2");
                nm.wifiUp(
                    function(message){
                        if (message.isSuccess){
                            resolve(message);
                        } else {
                            reject(message.message);
                        }
                    }
                )
            }
        );
    }

    wifiDown = function(callback){

        this.nm_wifi_downCallback = callback;

        //var res = new NetworkManager();
        var request = {};
        request.type = "nm_wifi_down";
        request.body = {};
        this.socket.sendRequest(request);

    }

    wifiDownPromise = function(nm = this){
        //console.log(">>> 1");
        return new Promise(
            function(resolve, reject) {
                //console.log(">>> 2");
                nm.wifiDown(
                    function(message){
                        if (message.isSuccess){
                            resolve(message);
                        } else {
                            reject(message.message);
                        }
                    }
                )
            }
        );
    }


    connectInterface = function(netface, callback){

        this.nm_connect_interfaceCallback = callback;

        //var res = new NetworkManager();
        var request = {};
        request.type = "nm_connect_interface";
        request.body = {};
        request.body.param = netface;
        this.socket.sendRequest(request);
        //this.networkManager=res;
        //return res;
    }

    connectInterfacePromise = function(netface, nm = this){
        //console.log(">>> 1");
        return new Promise(
            function(resolve, reject) {
                //console.log(">>> 2");
                nm.connectInterface(netface,
                    function(message){
                        if (message.isSuccess){
                            resolve(message);
                        } else {
                            reject(message.message);
                        }
                    }
                )
            }
        );
    }


    disconnectInterface = function(netface, callback){

        this.nm_disconnect_interfaceCallback = callback;

        //var res = new NetworkManager();
        var request = {};
        request.type = "nm_disconnect_interface";
        request.body = {};
        request.body.param = netface;
        this.socket.sendRequest(request);
        //this.networkManager=res;
        //return res;
    }

    disconnectInterfacePromise = function(netface, nm = this){
        //console.log(">>> 1");
        return new Promise(
            function(resolve, reject) {
                //console.log(">>> 2");
                nm.disconnectInterface(netface,
                    function(message){
                        if (message.isSuccess){
                            resolve(message);
                        } else {
                            reject(message.message);
                        }
                    }
                )
            }
        );
    }

    connectWifi = function (netface, ssid, password, autoConnect, isHidden, callback){

        this.connectWifiCallback = callback;

        var request = {};
        request.type = "nm_connect_wifi";
        request.body = {};

        request.body.param = {};
        request.body.param.netface = netface;
        request.body.param.ssid = ssid;
        request.body.param.password = password;
        request.body.param.password = password;
        request.body.param.autoConnect = autoConnect
        request.body.param.isHidden = isHidden

        this.socket.sendRequest(request);

    }

    connectWifiPromise = function(netface, ssid, password, autoConnect, isHidden, callback, nm = this){
        //console.log(">>> 1");
        return new Promise(
            function(resolve, reject) {
                //console.log(">>> 2");
                nm.connectWifi(netface, ssid, password, autoConnect, isHidden,
                    function(message){
                        if (message.isSuccess){
                            resolve(message);
                        } else {
                            reject(message.message);
                        }
                    }
                )
            }
        );
    }

    forgetWifi = function (netface, ssid, callback){

        this.forgetWifiCallback = callback;

        var request = {};
        request.type = "nm_forget_wifi";
        request.body = {};

        request.body.param = {};
        request.body.param.netface = netface;
        request.body.param.ssid = ssid;

        this.socket.sendRequest(request);

    }

    forgetWifiPromise = function(netface, ssid, callback, nm = this){
        //console.log(">>> 1");
        return new Promise(
            function(resolve, reject) {
                //console.log(">>> 2");
                nm.forgetWifi(netface, ssid,
                    function(message){
                        if (message.isSuccess){
                            resolve(message);
                        } else {
                            reject(message.message);
                        }
                    }
                )
            }
        );
    }

    reconnectWifi = function (netface, ssid, callback){

        this.reconnectWifiCallback = callback;

        var request = {};
        request.type = "nm_reconnect_wifi";
        request.body = {};

        request.body.param = {};
        request.body.param.netface = netface;
        request.body.param.ssid = ssid;


        this.socket.sendRequest(request);

    }

    reconnectWifiPromise = function(netface, ssid, callback, nm = this){
        //console.log(">>> 1");
        return new Promise(
            function(resolve, reject) {
                //console.log(">>> 2");
                nm.reconnectWifi(netface, ssid,
                    function(message){
                        if (message.isSuccess){
                            resolve(message);
                        } else {
                            reject(message.message);
                        }
                    }
                )
            }
        );
    }

    disconnectWifi = function (netface, ssid, callback){

        this.disconnectWifiCallback = callback;

        var request = {};
        request.type = "nm_disconnect_wifi";
        request.body = {};

        request.body.param = {};
        request.body.param.netface = netface;
        request.body.param.ssid = ssid;


        this.socket.sendRequest(request);

    }

    disconnectWifiPromise = function(netface, ssid, callback, nm = this){
        //console.log(">>> 1");
        return new Promise(
            function(resolve, reject) {
                //console.log(">>> 2");
                nm.disconnectWifi(netface, ssid,
                    function(message){
                        if (message.isSuccess){
                            resolve(message);
                        } else {
                            reject(message.message);
                        }
                    }
                )
            }
        );
    }



    setIPv4 = function(netface, connection, isAuto, address, netmask, gateway, dns1, dns2, callback){

        this.setIPv4Callback = callback;

        //var res = new NetworkManager();
        var request = {};
        request.type = "nm_ip_config";
        request.body = {};

        request.body.param = {};
        request.body.param.netface = netface;
        request.body.param.connection = connection;
        request.body.param.isAuto = isAuto;
        request.body.param.address = address;
        request.body.param.netmask = netmask;
        request.body.param.gateway = gateway;
        request.body.param.dns = [];
        if (dns1) request.body.param.dns.push(dns1);
        if (dns2) request.body.param.dns.push(dns2);
        this.socket.sendRequest(request);
        //this.networkManager=res;
        //return res;
    }


    setIPv4Promise = function(netface, conn, isAuto, ipAddress, netmask, gateway, dns1, dns2, nm = this){
        //console.log(">>> 1");
        return new Promise(
            function(resolve, reject) {
                //console.log(">>> 2");
                nm.setIPv4(netface, conn, isAuto, ipAddress, netmask, gateway, dns1, dns2,
                    function(message){
                        if (message.isSuccess){
                            resolve(message);
                        } else {
                            reject(message.message);
                        }
                    }
                )
            }
        );
    }

}

class NetworkInterfaces extends Map{


}



/*class AccessPointDetails {
	constructor(){
		this.in_use = "";
		this.ssid = "";
		this.mode = "";
		this.chan = "";
		this.rate = "";
		this.signal = "";
		this.bars = "";
		this.security = "";
	}
}

class IPv4 {
	constructor(){
		this.addresses = new List();
		this.dns = new List();
		this.gateway = "";
		this.method = "manual";
	}
}*/

class Reader {
    constructor(id, socket, type, address, createCallback, onTagRead ){
        this.id = id;
        this.socket = socket;
        this.type = type;
        this.address = address;
        this.isCreated = false;
        this.isConnected = false;
        this.settings = new ReaderSettings();
        this.features = new ReaderFeatures();
        this.createCallback = createCallback;
        this.onTagRead = onTagRead;
    }

    connect = function (connectCallback) {
        //console.log("connecting2");
        this.connectCallback = connectCallback;

        //console.log("connecting3");
        var request = {};
        //console.log("connecting4");
        request.type = "connectreader";
        request.body = {};
        request.body.id = this.id.toString();
        request.body.param = {};
        request.body.param = this.settings;
        //console.log(">>>>>>>>>>>>>>>>>>>>>");
        //console.log(request);
        this.socket.sendRequest(request);
        //console.log("connecting5");

    }

    connectPromise = function(rd = this){
        return new Promise(function(resolve, reject) {
            rd.connect(function(message){
                if (message.isSuccess){
                    resolve(message);
                } else {
                    reject(message.message);
                }
            })
        });
    }

    disconnectPromise = function(rd = this){
        return new Promise(function(resolve, reject) {
            //console.log("disconnecting22");
            rd.disconnect(function(message){
                if (message.isSuccess){
                    resolve(message);
                } else {
                    reject(message.message);
                }
            })
        });
    }

    disconnect = function (connectCallback) {
        this.connectCallback = connectCallback;
        var request = {};
        request.type = "disconnectreader";
        request.body = {};
        request.body.id = this.id.toString();
        this.socket.sendRequest(request);
    }

    startPromise = function(rd = this){
        return new Promise(function(resolve, reject) {
            rd.start(function(message){
                if (message.isSuccess){
                    resolve(message);
                } else {
                    reject(message.message);
                }
            })
        });
    }

    stopPromise = function(rd = this){
        return new Promise(function(resolve, reject) {
            rd.stop(function(message){
                if (message.isSuccess){
                    resolve(message);
                } else {
                    reject(message.message);
                }
            })
        });
    }

    start = function (startCallback) {
        this.startCallback = startCallback;
        var request = {};
        request.type = "startreader";
        request.body = {};
        request.body.id = this.id.toString();
        this.socket.sendRequest(request);
    }

    stop = function (stopCallback) {
        this.stopCallback = stopCallback;
        var request = {};
        request.type = "stopreader";
        request.body = {};
        request.body.id = this.id.toString();
        this.socket.sendRequest(request);
    }

    outputOn = function(out, outputOnCallback){
        this.outputOnCallback = outputOnCallback;
        var request = {};
        request.type = "outputonreader";
        request.body = {};
        request.body.id = this.id.toString();
        request.body.param = out;
        this.socket.sendRequest(request);

    }

    outputOff = function(out, outputOffCallback){
        this.outputOffCallback = outputOffCallback;
        var request = {};
        request.type = "outputoffreader";
        request.body = {};
        request.body.id = this.id.toString();
        request.body.param = out;
        this.socket.sendRequest(request);

    }

    applySettings = function (applySettingsCallback){

    }

    /*setSesion = function(sesion){
        var request = {};
        request.type = "setsession";
        request.id = this.id.toString();
        request.body = {};
        request.body.param = session.toString();
        this.socket.sendRequest(request);
    }*/




}


class Antenna {
    constructor(){
        this.portNumber = null;
        this.isActive = true;
        this.writePower = null;
        this.readPower = null;
    }
}

class ReaderSettings {
    constructor(){
        this.settingsStr = "";
        this.readerMode = "";
        this.searchMode = "";
        this.session = 0;
        this.tagPopulation = 0;
        this.useCommonPowerSettings = true;
        this.commonWritePower= null;
        this.commonReadPower= null;
        this.antennas = [];
        this.tagReportMode = "interval"; // individual, interval, onstop
        this.tagReportInterval = 1000; // milliseconds
    }

}

class ReaderFeatures {
    constructor(){
        this.brand;
        this.model = "";
        this.serialNumber = "";
        this.firmwareVersion = "";
        this.antennaCount = null;
        this.gpiCount = null;
        this.gpoCount = null;
        this.gpioCount = null;
        this.readModes = [];
        this.readPowers = [];
        this.writePowers = [];
        this.rxSensitivities = [];
        this.searchModes = [];
    }
}

class SizeData{
    constructor(label, qty){
        this.label = label;
        this.qty = qty;
    }
}

class LabelData{
    constructor(){
        this.client = "";
        this.boxNo = null;
        this.sizes = [];
    }
}

class LineData{
    constructor(text, isBold = false){
        this.text = text;
        this.bold = isBold;
    }

}

class PrintData{
    constructor(){
        this.lines = [];
    }
}

class NetworkInterface{
    constructor(iface){
        this.name = iface.name;
        this.type = iface.type;
        this.state = iface.state;
        this.connection = iface.connection;
        this.connections = iface.connections;
        this.accessPoints = iface.accessPoints;
        this.ipv4 = iface.ipv4;
    }
}



