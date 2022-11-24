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




var br, readerId;
var bridgeStatus = false;
var readerMode = 'consignment';
var readerStatus = false;
var connectReaderStatus = false;
var consignmentLoading  = false;
var comOldAddress = readerIp;
var comRetry = 1;
var comRetryMax = 100;
var impinjRetry = 1;
var impinjRetryMax = 5;

$(function () {
    connectBridge();
});

function connectBridge(){

    br = new Bridge("ws://" + bridgeIp + ":"+ bridgePort +"/ws/ws");

    br.addEventListener("onstatuschange", function (e) {

        switch (e.detail) {
            case br.CONNECTING 	: console.log("Bridge >>> CONNECTING"); break;
            case br.CLOSING 	: console.log("Bridge >>> CLOSING"); break;
            case br.OPEN 		:

                console.log("Bridge >>> OPEN");
                bridgeStatus = true;

                createReader();
                if(deviceType != 'box_station'){
                    createNetworkManager();
                }

                break;

            case br.CLOSED 		:

                console.log("Bridge >>> CLOSED");
                bridgeStatus = false;
                readerStatus = false;
                connectReaderStatus = false;

                $('#startStop').attr('disabled', true);
                $('#startStop').attr('data-default' , 'start');
                $('#startStop').html(startBtnText);
                $('#startStop').removeClass('btn-stop');

                br.removeEventListener("onstatuschange", this, false);
                setTimeout(function (){
                    Swal.fire({
                        icon: 'error',
                        title: langFailedText,
                        text: langErrorText + ' (Code 1) ',
                        confirmButtonText: langOkText,
                        footer: '<a href="javascript:window.location.reload();">'+ langTryAgainText +'?</a>'
                    });
                },1500);

                break;
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
                icon: 'error',
                title: langFailedText,
                text: langErrorText + ' (Code 2) ',
                confirmButtonText: langOkText,
                footer: '<a href="javascript:window.location.reload();">'+ langTryAgainText +'?</a>'
            });
        });

}

function connectReader(id){

    console.log("connecting reader "+ id);

    br.readers[id].connectPromise().then(function(result){

        console.log(result);

        $("#findPackage").prop("disabled", false);

        connectReaderStatus = true;

        if(connectReaderStatus){
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

        if(reader == 'thingmagic' && deviceType == 'box_station' && comRetry <= comRetryMax){

            readerIp = "COM" + comRetry++;
            createReader();

        }else if(reader == 'impinj' && impinjRetry <= impinjRetryMax){

            impinjRetry++
            createReader();

        }else{
            Swal.fire({
                icon: 'error',
                title: langFailedText,
                text: langErrorText + ' (Code 3) ',
                confirmButtonText: langOkText,
                footer: '<a href="javascript:window.location.reload();">'+ langTryAgainText +'?</a>'
            });
        }

    });
}

function disconnectReader(id){

    console.log("disconnecting reader "+ id);

    br.readers[id].disconnectPromise()
        .then(function(result){

            connectReaderStatus = false;

            $("#startStop").prop("disabled", true);
            $("#findPackage").prop("disabled", true);

            return true;
        })
        .catch(function(reason){
            console.log("disconnecting reader failed "+ reason);
            return false;
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
var currentReader;
var seconds = 0;
function CheckIdleTime()
{
    seconds += 1;
    console.log('Idle time : ' + seconds)
    if(seconds == 120)
    {
        stopReader(readerId);
        return;
    }else{
        if(readerStatus == true){
            setTimeout(CheckIdleTime, 1000);
        }
    }
}

function stopReader(id){

    recordStatus = false;

    console.log(">>>>>>> stop reader "+ id);

    $('#startStop').attr('disabled', true);


    br.readers[id].stopPromise()
        .then(function(result){
            readerStatus = false;
            seconds = 0;
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
                text: 'Okuma durdurulurken bir hata ile karşılaşıldı.',
                icon: 'error',
                confirmButtonText: 'Tamam',
            });
        });
}

function onTagRead(tags){

    // if(recordStatus == true){
        $.each(tags, function(index, tag) {
            console.log(">>>>>>>>TagRead>>>>>>>>>" + tag.epc);

            if(!consignment.epcs.has(tag.epc)){
                seconds = 0;
            }

            switch (readerMode) {

                case 'find' :

                    buffer.add(tag);

                    break;

                default :   consignment.add(tag,this);   break;
            }

        });
    // }

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

class Item{
    constructor(epc, timestamp,package_id,id,gtin,rendered = false){
        this.epc        = epc;
        this.timestamp  = new Date(timestamp);
        this.package_id = package_id;
        this.gtin       = gtin;
        this.id         = id;
        this.rendered   = false;
    }
}

class Package{
    constructor(index, itemsCount, lastUpdated, items){
        this.id             = 0;
        this.items          = new Map();
        if(items && items.length > 0){

            for (let i = 0; i < items.length; i++) {
                this.items.set(items[i].epc, new Item(items[i].epc, new Date(),items[i].package_id,items[i].id,items[i].gtin));
            }
        }

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
                }

                var currentPackage = this.packages.get(this.packageNo);
                //Pakete epc bilgilerini ekle
                currentPackage.items.set(tag.epc, new Item(tag.epc, tag.firstSeenTime,0,0,""));

                //Paket son ekleme tarihi güncelle
                currentPackage.lastUpdated = tag.firstSeenTime;

                //epc'yi ekle
                this.epcs.add(tag.epc);

                insertRow(this.packageNo,currentPackage)

                //Miktarı güncelle
                updateQuantity(this.packageNo, currentPackage.items.size);

                //Toplam okunan epc sayısı
                updateTotalQuantity(this.epcs.size);

                // this.getSizes();

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
            this.packages.set(tag.packageNo, new Package(tag.packageNo, tag.itemsCount,  null, tag.items));

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
        this.packages.get(tag.packageNo).items.set(tag.epc, new Item(tag.epc, tag.firstSeenTime,0,0,""));

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

            if ((new Date().getTime() -  currentPackage.lastUpdated) > (boxCloseTime * 1000)){
                //console.log('Closing Box');

                //paketi kapat
                this.isLastBoxClosed = true;
                currentPackage.isClosed = true;

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
                        data            : Array.from(currentPackage.items.values())
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
    // consignment.getSizes();
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
    // consignment.getSizes();
}

function updateSelectedCount()
{
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
    // $('#consignmentList tbody tr#' + key + ' td')[2].innerText = value;
}

function updateTotalQuantity(value){
    $('#totalQuantity').text(value);
}

function updateID(key, value){

    var dd = $("input[package-id='" + key + "']");
    var df = $("input[package-id='" + key + "']").attr('package-id', value);

    $("input[package-id='" + key + "']").attr('package-id', value);
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
        package.items           = item.items;
        package.packageNo       = item.package_no;
        package.model           = item.model == null ? '-' : item.model;
        package.size            = item.size == null ? '-' : item.size;

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

    // consignment.getSizes();

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
