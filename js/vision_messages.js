var messages = (function() {
    var di = {};

    return function(id_block_) {
        var id_block = typeof id_block_ != "undefined" ? id_block_ : '#message-container';
        if(id_block in di) {
            return di[id_block];
        }
        var self = this;
        this.base_url = 'http://fitbato.com/admin/battle/private-messages';

        this.mainBox = $(id_block);
        this.box = this.mainBox.find('.message-container').eq(0);
        this.inputText   = this.mainBox.find('input[name="input_message"]');
        this.inputFromId = this.mainBox.find('input[name="message_id_user"]');

        this.lastId = 0;

        this.log = function(m) {
            console.log(m);
        };




        this.getAllMessages = function () {
            var from_id = self.inputFromId.val();
            if(!from_id) {
                return null;
            }
            $.ajax({
                type: "GET",
                url: self.base_url,
                data: {from_id:from_id, action:'getMessage'},
                success: function(msg){
                    if(msg.status) {
                        self.updateBox(msg.data);
                    } else {
                        self.log('error: getAllMessages');
                        self.log(msg);
                    }
                }
            });
        };

        this.getNewMessages = function () {
            var from_id = self.inputFromId.val();
            if(!from_id) {
                return null;
            }
            $.ajax({
                type: "GET",
                url: self.base_url,
                data: {from_id:from_id, action:'getNewMessage'},
                success: function(msg){
                    if(msg.status) {
                        self.updateBox(msg.data);
                    } else {
                        self.log('error: getNewMessage');
                        self.log(msg);
                    }
                }
            });
        };




        this.clearBox = function() {
            self.box.html('');
            self.lastId = 0;
        };

        this.createHtmlMessage = function(n) {
            var html = '';
            html += '<div class="bubble ' + (n['i_am_sender'] ? 'bubble-alt green' : '') + '">';
            html += '<p>' + n['message'] + '</p>';
            html += '</div>';
            return html;
        };

        this.updateBox = function(m) {
            var html = '';
            m.forEach(function(h){
                if(h['id'] > self.lastId) {
                    html += self.createHtmlMessage(h);
                    self.lastId = h['id'];
                }
            });
            self.box.prepend(html);
            self.box.scrollTop(self.box.height());
        };

        di[id_block] = this;
    }
})();