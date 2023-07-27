'use strict';

require('./bootstrap');
require('./datepicker.bundle');
require('./date-util');
require('vue-cookies/vue-cookies.js');

window.Vue = require('vue');

$.ajaxSetup({
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    dataType: 'json',
    cache: false,
    timeout: 100000,
    error: function (res) {
        alert([res.statusText, res.responseText].join(':'));
    }
});

$(document).ready(() => {
    close_modal_by_esc();
});

function close_modal_by_esc(){
    if( $('#edit-area').length == 0 ) {
        return ;
    }
    $(window).keyup(function(e){
        if(e.which != 27){
            return;
        }
        if( ! $('#edit-area').hasClass('show')) {
            return;
        }
        $('#edit-area .btn-close').trigger('click');
    });
}
