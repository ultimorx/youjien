'use strict';

require('../../common');

$(document).ready(() => {
    let url_list = URL_LIST;
    let url_download = URL_DOWNLOAD;
    let url_detail = URL_DETAIL;

    const date_area = require('../../components/date-area')();

    const list = new Vue({
        el: '#list',
        data: {
            sheet: null,
        },
        methods: {
            fetch() {
                $.ajax({
                    url: url_list,
                    data: {
                        'bizyear': $('#year-select-list').val(),
                        'month': $('#month-select-list').val(),
                    },
                    type: 'get'
                }).done((res) => {
                    console.log(res);
                    this.sheet = res;
                });
            },
            detail(roster_id, line) {
                let year = $('#year-select-list option:selected').val();
                let month = $('#month-select-list option:selected').val();
                console.log('roster_id:'+roster_id, 'bizyear:'+year, 'month:'+month, line);
                let href = url_detail + '/' + year + '/' + month + '/' + roster_id;
                window.open(href);
                // window.open(href, '_blank');
            },
        }
    });

    const viewYearMonth = () => {
        let year = $('#year-select-list option:selected').text();
        let month = $('#month-select-list option:selected').text();
        $('#yearmonth').text(year + month);
    }

    const init = () => {
        viewYearMonth();
        list.fetch();
    }

    // 年、月の選択
    $(document).on('change', '#year-select-list, #month-select-list', () => {
        viewYearMonth();
        list.fetch();
    });
    // 再読込
    $(document).on('click', '#yearmonth-search', () => {
        viewYearMonth();
        list.fetch();
    });
    // ダウンロードボタン押下
    $(document).on('click', '#download', () => {
        let year = $('#year-select-list').val();
        let month = $('#month-select-list').val();
        window.open(url_download + '?bizyear=' + year + '&month=' + month);
    });

    init();
});
