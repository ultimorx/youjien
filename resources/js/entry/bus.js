'use strict';

require('../common');

$(document).ready(() => {
    const date_area = require('../components/date-area')();

    const list = new Vue({
        el: '#children-list',
        data: {
            buses: [],
        },
        methods: {
            fetch() {
                $.ajax({
                    url: '/api/depart/bus',
                    data: {
                        'date': date_area.dateObj.format()
                    },
                    type: 'get'
                }).done((res) => {
                    this.buses = res;
                });
            },
            check() {
                $('input[type="checkbox"]').prop('checked', true);
            },
            depart() {
                $.ajax({
                    url: '/api/attendance/reparts',
                    data: $('input[type="checkbox"]').serializeArray(),
                    type: 'post'
                }).done((res) => {
                    this.fetch();
                });
            },
            revert(attendance) {
                attendance.outtime = null;
                attendance.pick_up = null;
                this.update(attendance);
            },
            wait(attendance) {
                if(confirm('お迎えに変更します。')) {
                    attendance.bus_id = 0;
                    this.update(attendance);
                    this.fetch();
                }
            },
            update(attendance) {
                $.ajax({
                    url: '/api/attendance/'+attendance.id,
                    data: attendance,
                    type: 'put'
                }).done((res) => {
                });
            },
        }
    });
    $(document).on('change', '#date-area', list.fetch);

    const init = () => {
        list.fetch();
        date_area.listen();
    }

    init();
});
