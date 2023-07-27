'use strict';

require('../../common');

$(document).ready(() => {
    let url_list = URL_LIST;
    let url_download = URL_DOWNLOAD;

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
                        'classroom_id': $('#classroom-select-list').val(),
                    },
                    type: 'get'
                }).done((res) => {
                    console.log(res);
                    this.sheet = res;
                });
            },
        }
    });

    const viewSelectOption = () => {
        let year = $('#year-select-list option:selected').text();
        let month = $('#month-select-list option:selected').text();
        let classroom = $('#classroom-select-list option:selected').text();
        $('#yearmonth').text(year + month);
        $('#classroom').text(classroom);
    }

    var hide_css = 'hide toast';
    const filterSelectOption = () => {
        let year = $('#year-select-list').val();
        // 園全体の選択肢
        var $all_classroom = $('#classroom-select-list option[value=0]');

        // 選択肢をすべて非表示
        $('#classroom-select-list option').addClass(hide_css);

        // 該当の選択肢のみ表示
        $('#classroom-select-list option[data-bizyear=' + year + ']').removeClass(hide_css);
        $all_classroom.removeClass(hide_css);

        // 先頭の選択肢を選択済みにする
        if( $all_classroom.length == 0 ) {
          $('#classroom-select-list option[data-bizyear=' + year + ']:first').prop('selected', true);
        } else {
          $all_classroom.prop('selected', true);
        }
    }

    const checkSelectOption = () => {
    	$.each($('#year-select-list option'), function(i, v){
            let year = $(this).val();
            let count_classroom = $('#classroom-select-list option[data-bizyear=' + year + ']').length;
            if( count_classroom == 0 ) {
                $(this).addClass(hide_css);
            }
    	});
    }

    const init = () => {
        checkSelectOption();
        filterSelectOption();
        viewSelectOption();
        list.fetch();
    }

    // 年変更時
    $(document).on('change', '#year-select-list', () => {
        filterSelectOption();
    });

    // 月、クラス変更時
    $(document).on('change', '#year-select-list, #month-select-list, #classroom-select-list', () => {
        viewSelectOption();
        list.fetch();
    });
    // 再読込
    $(document).on('click', '#yearmonth-search', () => {
        viewSelectOption();
        list.fetch();
    });
    // ダウンロードボタン押下
    $(document).on('click', '#download', () => {
        let year = $('#year-select-list').val();
        let month = $('#month-select-list').val();
        let classroom = $('#classroom-select-list').val();
        window.open(url_download + '?bizyear=' + year + '&month=' + month + '&classroom_id=' + classroom);
    });

    init();
});
