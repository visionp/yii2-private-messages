$(window).ready(function() {
    var base_url = 'http://fitbato.com/admin/battle/private-messages';
    var messages = {
        box : $("#message-container"),
        lastId: 0,
        console:function(m) {
            console.log(m);
        }
    };

    var getAllMessages = function (from_id) {
        $.ajax({
            type: "POST",
            url: base_url + '?action=getMessage&from_id=' + from_id,
            data: {from_id:from_id},
            success: function(msg){
                if(msg.status) {
                    updateBox(msg.data);
                }else{
                    messages.console(msg);
                }
            }
        });
    };

    var updateBox = function(m) {
        var html = '';
        messages.console(m);
    };




});