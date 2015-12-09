//Работа с окном сообщением
var visiPrivateMessages = (function() {
    "use strict";
    var di = {};

    var enableLog = false;

    function getUrl() {
        var pathArray = location.href.split( '/' );
        var protocol = pathArray[0];
        var host = pathArray[2];
        var url = protocol + '//' + host + '/' + baseUrlPrivateMessage  + '/private-messages';
        return url;
    }

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
            self.inputIsEmail = self.form.find('[name="send_mail"]');
            self.listBox = self.mainBox.find('.list_users div.overview');
            self.lastId = 1;
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
                        self.log(msg.data);
                        self.updateBox(msg.data);
                    } else {
                        self.log('error: ' + action);
                        self.log(msg);
                    }
                }
            });
        };

        //отправка сообщения
        var sendMessage = function(whom_id, text) {
            $.ajax({
                type: "GET",
                url: self.base_url,
                data: {whom_id:whom_id, text:text, isEmail:self.isEmail ? self.isEmail : '', action:'sendMessage'},
                success: function(msg){
                    if(msg.status) {
                        self.updateBox(msg.data);
                        self.inputText.val('');
                    } else {
                        self.log('error: ' + 'sendMessage');
                        self.log(msg);
                    }
                }
            });
        };

        var changeActiveUser = function(id) {
            var currentId = self.inputFromId.val();
            if(currentId != id) {
                self.inputFromId.val(id);
                self.mainBox.find('div.contact').removeClass('active');
                self.mainBox.find('div.contact[data-user=' + id + ']').addClass('active');
            }
        };

        this.reInit = function() {
            init();
            return self;
        };

        this.log = function(m) {
            if(enableLog){
                console.log(m);
            }
            return self;
        };

        this.getAllMessages = function () {
            updateMessage('getMessage');
            return self;
        };

        this.getNewMessages = function () {
            updateMessage('getNewMessage');
            return self;
        };

        //проверить на дублирование сообщение на почту
        this.isEmail = function() {
            return self.inputIsEmail.prop('checked');
        };

        //очистить окно сообщений
        this.clearBox = function() {
            self.box.html('');
            self.lastId = 1;
            return self;
        };

        //создать разметку нового сообщения
        this.createHtmlMessage = function(n) {
            var html = '';
            html += '<div data-id="' + n['id']  +'" class="message message-table ' + (n['i_am_sender'] ? 'my-message' : '') + '">';
            html += '<div class="message-row">';
            html += '<div class="massage-cell">';
            html += '<span class="delete-message">+</span>';
            html += '<span class="massage-cell-wrapper">' + n['message'] + '</span>';
            html += '</div></div></div>';
            return html;
        };

        //обработка событий обновления данных pool
        this.fromPooling = function(m) {
            var current_id = self.inputFromId.val();
            for(var k in m){
                var arr = m[k];
                self.setCountMessToList(arr['id'], arr['cnt_mess']);
                if(arr['id'] == current_id) {
                    self.getNewMessages();
                }
            }
            return self;
        };

        //Установить кол-во новых сообщений в списке пользователей
        this.setCountMessToList = function(user_id, count) {
            var contact = self.mainBox.find('div.contact[data-user=' + user_id + ']');
            var counter = contact.find('span.counter-message').eq(0);
            counter.html(count);
            if(count > 0){
                counter.show();
                self.moveToTop(contact);
            } else {
                counter.hide();
            }
            return self;
        };


        //перемещаем контактное лицо в топ списка
        this.moveToTop = function(contact) {
            contact.prependTo(self.listBox);
        }

        //сортировка контактов
        this.sortList = function(){
            self.mainBox.find('div.list_users div.overview div.contact').sortElements(function (a, b) {
                var f = $(a).find('span.counter-message').html();
                var s = $(b).find('span.counter-message').html();
                return (f > s) ? -1 : (f < s) ? 1 : 0;
            });
        };

        this.deleteMessage = function(idMessage) {
            $.ajax({
                type: "GET",
                url: self.base_url,
                data: {id_message:idMessage, action:'deleteMessage'},
                success: function(msg){
                    if(msg.status) {
                        self.mainBox.find('div.message[data-id=' + msg['data'] + ']').remove();
                    } else {
                        self.log('error: ' + 'deleteMessage');
                        self.log(msg);
                    }
                }
            });
            return self;
        };

        //обновление блока сообщений
        this.updateBox = function(data) {
            var m = data.messages;
            var fromId = data.from_id;
            var html = '';
            if(fromId != self.inputFromId.val()){
                self.log('Error id user message and fromId');
                return false;
            }
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
            self.Scroll();
            self.setCountMessToList(fromId, ' ');
            return self;
        };

        //отправка сообщения
        this.form.submit(function() {
            var text = self.inputText.val();
            var id = self.inputFromId.val();
            if(!id || !text) {
                return false;
            }
            sendMessage(id, text);
            return false;
        });


        //обработка кликов для удаления сообщений
        self.mainBox.click(function(event) {
            var target = $(event.target);
            if(target.is('span.delete-message')) {
                var idMessage = target.parents('div.message').data('id');
                self.deleteMessage(idMessage);
                return false;
            }
        });

        //обработка клика по кнопке удаления диалога
        self.mainBox.find('button.delete-friend').click(function(event){
            var target = $(this).parent('div.contact');
            var user_id = target.data('user');
            if(user_id) {
                $.ajax({
                    type: "GET",
                    url: self.base_url,
                    data: {user_id:user_id, action:'clearMessage'},
                    success: function(msg){
                        if(msg.status) {
                            target.trigger('click');
                        } else {
                            self.log('error: ' + 'deleteMessage');
                            self.log(msg);
                        }
                    }
                });
            }
            return false;
        });

        //обработка кликов на контакты
        this.mainBox.find('.contact').click(function(event){
            var user_id = $(this).data('user');
            if(user_id) {
                self.clearBox();
                changeActiveUser(user_id);
                self.inputText.attr('disabled', false);
                self.getAllMessages();
            }
        });


        //resize tinyscrollbar
        this.Scroll = function() {
            this.scrollbar1.data("plugin_tinyscrollbar").update('bottom');
        };


        //Поиск по контактам
        $('div.search input[type="search"]').keyup(function(){
            var val = this.value;
            $('.friends').each(function(){
                var target = $(this);
                var name = target.data('name');
                var expr = new RegExp(val, 'i');
                if(expr.test(name)){
                    target.show();
                } else {
                    target.hide();
                }
            });
            this.scrollbar3.data("plugin_tinyscrollbar").update();
        });

        di[id_block] = this;

        this.pools = new privateMessPooling(self.lastId);
        this.pools.addListener('newData', this.fromPooling);
        this.pools.start();

        this.scrollbar1 = $('#scrollbar1');
        this.scrollbar1.tinyscrollbar();
        this.scrollbar3 = $('#scrollbar3').tinyscrollbar();

    }
})();