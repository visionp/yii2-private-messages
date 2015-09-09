//Пуллинг, данные отдаются через события
var privateMessPooling = (function() {
    "use strict";
    var lastId = 0;
    var di = false;
    var enableLog = false;
    var timePool = 30000;

    function getUrl() {
        var pathArray = location.href.split( '/' );
        var protocol = pathArray[0];
        var host = pathArray[2];
        var url = protocol + '//' + host + '/' + baseUrlPrivateMessage  + '/private-messages';
        return url;
    };

    return function(max_id) {
        if(di != false) {
            return di;
        }

        if(typeof max_id == 'undefined') {
            max_id = 0;
        }
        //обновляем последний полученный id сообщения
        if(max_id > lastId) {
            lastId = max_id;
        }

        var activeTimeout = false;
        var self = this;

        var pooling = function (){
            self.ajax = $.ajax({
                url:getUrl(),
                type:"GET",
                data:{last_id:lastId, action:'pooling'},
                cahce:false,
                timeout:3000,
                success:function(result){
                    if(result) {
                        self.triggerEvent('newData', result);
                    }
                },
                complete:function() {
                    if(activeTimeout) {
                        setTimeout(function() {pooling();}, timePool);
                    }
                }
            });
        };

        this.start = function() {
            if(!activeTimeout) {
                pooling();
                activeTimeout = true;
            }
            return self;
        };

        this.stop = function() {
            if(activeTimeout) {
                if(self.ajax) {
                    self.ajax.abort();
                }
                activeTimeout = false;
            }
            return self;
        };

        this.log = function(data) {
            if(enableLog) {
                console.log(data);
            }
            return self;
        };

        di = this;
        this.listeners = {};

        this.addListener = function(evt, callback) {

            if ( !this.listeners.hasOwnProperty(evt) ) {
                this.listeners[evt] = [];
            }

            this.listeners[evt].push(callback);
            return self;
        };

        this.removeListener = function(evt, callback) {
            if ( this.listeners.hasOwnProperty(evt) )    {
                var i,length;
                for (i = 0, length = this.listeners[evt].length; i < length; i += 1) {
                    if ( this.listeners[evt][i] === callback) {
                        this.listeners[evt].splice(i, 1);
                    }
                }
            }
            return self;
        };

        this.triggerEvent = function(evt, args) {
            if ( this.listeners.hasOwnProperty(evt) )    {
                var i,length;
                for (i = 0, length = this.listeners[evt].length; i < length; i += 1) {
                    this.listeners[evt][i](args);
                }
            }
        };
        return self;
    };
})();