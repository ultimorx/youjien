'use strict';

require('../../common');

$(document).ready(() => {
    let url_list = URL_LIST;
    let url_download = URL_DOWNLOAD;
    let roster_id = ROSTER_ID;
    let default_year = DEFAULT_YEAR;
    let default_month = DEFAULT_MONTH;

    const date_area = require('../../components/date-area')();

    const select_form = new Vue({
        el: '#select-form',
        data: {
            bizeyar: null,
        },
        methods: {
            fetch() {
                // $.ajax({
                //     url: url_list,
                //     data: {
                //         'bizyear': default_year,
                //         'month': $('#month-select-list').val(),
                //         'roster_id': roster_id,
                //     },
                //     type: 'get'
                // }).done((res) => {
                //     console.log(res);
                //     this.sheet = res;
                // });
            },
        }
    });

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
                        'bizyear': default_year,
                        'month': $('#month-select-list').val(),
                        'roster_id': roster_id,
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
        let year = default_year + '年度';
        let month = $('#month-select-list option:selected').text();
        $('#yearmonth').text(year + month);
    }

    const init = () => {
        select_form.bizyear = default_year;
        $("#month-select-list option[value=" + default_month + "]").prop('selected', true);
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
        let year = default_year;
        let month = $('#month-select-list').val();
        window.open(url_download + '?bizyear=' + year + '&month=' + month + '&roster_id=' + roster_id);
    });

    init();
});
