'use strict';

require('../common');

$(document).ready(() => {
    let $information_message = $('.information_message');

    $(document).on('click', '#information_show_message', () => {
        $information_message.show();
        $('#information_show_message').hide();
        $('#information_hide_message').show();
    });

    $(document).on('click', '#information_hide_message', () => {
        $information_message.hide();
        $('#information_hide_message').hide();
        $('#information_show_message').show();
    });

    // 個別メッセージの表示／非表示
    $(document).on('click', '.information_title', (_this) => {
        // let ref_id = $(_this.target).attr('ref_id');
        // $('#'+ref_id).show(); console.log(' ref_id : ', ref_id);
        let $message = $(_this.target).next();
        if($message.css('display') == 'block') {
            $message.hide();
        } else {
            $message.show();
        }
    });
});
