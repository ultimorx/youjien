'use strict';

require('bootstrap-datepicker');
require('bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css');
require('bootstrap-datepicker/dist/locales/bootstrap-datepicker.ja.min.js');

$(window).on('load', function () {
    $('.datepicker')
        .not('.datepicker-custom')
        .not('.datepicker-enabled')
        .datepicker({
            format: 'yyyy-mm-dd',
            language: 'ja',
            autoclose: true
        })
        .addClass('datepicker-enabled');
});
