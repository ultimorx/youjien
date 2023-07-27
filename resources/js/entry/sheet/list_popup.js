'use strict';

require('../../common');

$(document).ready(() => {
    let url_list = URL_LIST;
    let url_download = URL_DOWNLOAD;
    let popup_url_list = POPUP_URL_LIST;
    let popup_url_download = POPUP_URL_DOWNLOAD;

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
                let year = $('#year-select-list option:selected').text();
                let month = $('#month-select-list option:selected').text();
                console.log('roster_id:'+roster_id, 'bizyear:'+year, 'month:'+month, line);
                popup.title = line[2];
                popup.subtitle = line[3];
                popup.date = year + month;
                popup.fetch(roster_id);
            },
        }
    });

    const popup = new Vue({
        el: '#popup-area',
        data: {
            sheet: null,
            title: '',
            subtitle: '',
            date: '',
            roster_id: null
        },
        methods: {
            fetch(roster_id) {
                this.roster_id = roster_id;
                $.ajax({
                    url: popup_url_list,
                    data: {
                        'bizyear': $('#year-select-list').val(),
                        'month': $('#month-select-list').val(),
                        'roster_id': this.roster_id,
                    },
                    type: 'get'
                }).done((res) => {
                    console.log(res);
                    this.sheet = res;
                });
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

    // 月選択
    $(document).on('change', '#month-select-list', () => {
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
    // ダウンロードボタン押下
    $(document).on('click', '#popup-download', () => {
        let year = $('#year-select-list').val();
        let month = $('#month-select-list').val();
        window.open(popup_url_download + '?bizyear=' + year + '&month=' + month + '&roster_id=' + popup.roster_id);
    });

    init();
});
