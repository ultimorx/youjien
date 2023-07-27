'use strict';

require('../../common');

$(document).ready(() => {
    const date_area = require('../../components/date-area')();
    const dateFormat = require('../../dateformat');

    const list = new Vue({
        el: '#list',
        data: {
            sheet: null,
            inputs: []
        },
        methods: {
            fetch() {
                $.ajax({
                    url: '/api/mst/aim/days',
                    data: {
                        'bizyear': $('#select-bizyear').val(),
                        'grade_id': $('#select-grade').val(),
                    },
                    type: 'get'
                }).done((res) => {
                    this.sheet = res;
                    this.setData(res);
                });
            },
            viewDate(d){
                return dateFormat(new Date(d), 'M/D');
            },
            setData(aims){
                for(let k in aims) {
                    let key = aims[k].id;
                    this.inputs[key] = {
                        'play' : aims[k].play,
                        'life' : aims[k].life,
                        'note' : aims[k].note
                    }
                }
            },
            update(aim) {
                $.ajax({
                    url: '/api/mst/aim/save/' + aim.id,
                    data: this.inputs[aim.id],
                    type: 'put'
                }).done((res) => {
                    console.log(res);
                });
            },
        }
    });

    const init = () => {
        list.fetch();
        adjustListHeight();
    }

    // 年、月の選択時
    $(document).on('change', '#select-bizyear, #select-grade', () => {
        // viewYearMonth();
        list.fetch();
    });
    // 再読込
    $(document).on('click', '#yearmonth-search', () => {
        viewYearMonth();
        list.fetch();
    });

    const adjustListHeight = () => {
        console.log('window resize', window.innerHeight);
        let listHeight = window.innerHeight - 250;
        let min = 300;
        if(listHeight < min) listHeight = min;
        $('#list').height(listHeight);
    }

    $(window).resize(function() {
        adjustListHeight();
    });

    init();
});
