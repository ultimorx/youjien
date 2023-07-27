'use strict';

require('../common');

$(document).ready(() => {
    const date_area = require('../components/date-area')();

    const list = new Vue({
        el: '#children-list',
        data: {
            rosters: [],
        },
        methods: {
            fetch() {
                $.ajax({
                    url: '/api/depart/daytime',
                    data: {
                        'date': date_area.dateObj.format()
                    },
                    type: 'get'
                }).done((res) => {
                    this.rosters = res;
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
                this.update(attendance);
            },
            update(attendance) {
                $.ajax({
                    url: '/api/attendance/'+attendance.id,
                    data: attendance,
                    type: 'put'
                }).done((res) => {
//                    this.fetch();
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
