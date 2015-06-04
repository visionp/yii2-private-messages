var messages = (function() {
    var di = {};

    return function(id_block_) {
        var id_block = typeof id_block_ != "undefined" ? id_block_ : '#message-container';
        if(id_block in di) {
            return di[id_block];
        }

        var self = this;

        var init = function() {
            self.base_url = getUrl();
            self.mainBox = $(id_block);
            self.box = self.mainBox.find('.message-container').eq(0);
            self.form = self.mainBox.find('form.message-form').eq(0);
            self.inputText   = self.form.find('[name="input_message"]');
            self.inputFromId = self.form.find('[name="message_id_user"]');
            self.lastId = 0;
        };

        init();

        var updateMessage = function(action) {
            var from_id = self.inputFromId.val();
            if(!from_id) {
                return null;
            }
            $.ajax({
                type: "GET",
                url: self.base_url,
                data: {from_id:from_id, action:action},
                success: function(msg){
                    if(msg.status) {
                        self.updateBox(msg.data);
                    } else {
                        self.log('error: ' + action);
                        self.log(msg);
                    }
                }
            });
        };

        var sendMessage = function(whom_id, text) {
            $.ajax({
                type: "GET",
                url: self.base_url,
                data: {whom_id:whom_id, text:text, action:'sendMessage'},
                success: function(msg){
                    if(msg.status) {
                        self.inputText.val('');
                        self.getNewMessages();
                    } else {
                        self.log('error: ' + 'sendMessage');
                        self.log(msg);
                    }
                }
            });
        };

        function getUrl() {
            pathArray = location.href.split( '/' );
            protocol = pathArray[0];
            host = pathArray[2];
            var url = protocol + '//' + host + '/admin/battle/private-messages';
            return url;
        }

        this.reInit = function() {
            init();
        };

        this.log = function(m) {
            console.log(m);
        };

        this.getAllMessages = function () {
            updateMessage('getMessage');
        };

        this.getNewMessages = function () {
            updateMessage('getNewMessage');
        };


        this.clearBox = function() {
            self.box.html('');
            self.lastId = 0;
        };

        this.createHtmlMessage = function(n) {
            var html = '';
            html += '<div class="message ' + (n['i_am_sender'] ? 'bubble-left' : 'bubble-right') + '">';
            html += '<label class="message-user">' + n['from_name'] + '</label>';
            html += '<label class="message-timestamp">' + n['created_at'] +'</label>';
            html += '<p>' + n['message'] + '</p>';
            html += '</div>';
            return html;
        };

        this.form.submit(function() {
            var text = self.inputText.val();
            var id = self.inputFromId.val();
            if(!id || !text) {
                return false;
            }
            sendMessage(id, text);
            return false;
        });


        this.updateBox = function(m) {
            var html = '';
            m.forEach(function(h){
                if(1*h['id'] > self.lastId) {
                    html += self.createHtmlMessage(h);
                    self.lastId = h['id'];
                }else {
                    self.log('last id: ' + self.lastId + ', current id:' + h['id']);
                    self.log(h);
                }
            });
            self.box.append(html);
            self.box.animate({scrollTop: self.box.prop("scrollHeight")}, 500);
        };

        this.mainBox.find('.contact').click(function(event){
            var user_id = $(this).data('user');
            if(user_id) {
                self.clearBox();
                self.inputFromId.val(user_id);
                self.inputText.attr('disabled', false);
                self.getAllMessages();
            }
        });

        di[id_block] = this;
    }
})();