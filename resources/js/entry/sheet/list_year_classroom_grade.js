'use strict';

require('../../common');

$(document).ready(() => {
    let url_list = URL_LIST;
    let url_download = URL_DOWNLOAD;

    const date_area = require('../../components/date-area')();

    const list = new Vue({
        el: '#list',
        data: {
            sheet: [],
            classroom_id: 0,
            grade_id: 0,
            KEY_GENDER_M: KEY_GENDER_M,
            KEY_GENDER_W: KEY_GENDER_W,
            KEY_DAYS: KEY_DAYS,
        },
        methods: {
            setid() {
                this.classroom_id = 0;
                this.grade_id = 0;
                let $classroom = $('#classroom-select-list option:selected');
                let type = $classroom.attr('data-type');
                if( type == 'classroom') {
                    this.classroom_id = $classroom.val();
                } else {
                    this.grade_id = $classroom.val();
                }
                //console.log(this.classroom_id, this.grade_id);
            },
            fetch() {
                this.setid();
                $.ajax({
                    url: url_list,
                    data: {
                        'bizyear': $('#year-select-list').val(),
                        'classroom_id': this.classroom_id,
                        'grade_id': this.grade_id,
                    },
                    type: 'get'
                }).done((res) => {
                    //console.log(res);
                    this.sheet = res;
                });
            },
        }
    });

    const viewSelectOption = () => {
        let year = $('#year-select-list option:selected').text();
        let classroom = $('#classroom-select-list option:selected').text();
        $('#year').text(year);
        $('#classroom').text(classroom);
    }

    var hide_css = 'hide toast';
    const filterSelectOption = () => {
        let year = $('#year-select-list').val();
        $('#classroom-select-list option[data-bizyear]').addClass(hide_css);
        $('#classroom-select-list option[data-bizyear=' + year + ']').removeClass(hide_css);
        $('#classroom-select-list option[data-bizyear=' + year + ']:first').prop('selected', true);
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

    // 年、月の選択
    $(document).on('change', '#year-select-list, #classroom-select-list', () => {
        viewSelectOption();
        list.fetch();
    });
    // 再読込
    $(document).on('click', '#search', () => {
        viewSelectOption();
        list.fetch();
    });
    // ダウンロードボタン押下
    $(document).on('click', '#download', () => {
        list.setid();
        let classroom_id = list.classroom_id;
        let grade_id = list.grade_id;
        let year = $('#year-select-list').val();
        window.open(url_download + '?bizyear=' + year  + '&classroom_id=' + classroom + '&grade_id=' + grade_id);
    });

    init();
});
