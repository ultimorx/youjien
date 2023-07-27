'use strict';

require('../common');

$(document).ready(() => {
    const date_area = require('../components/date-area')();

    const dayoff = new Vue({
        el: '#dayoff',
        data: {
            mimanji : '',
            ijyouji : ''
        },
        methods: {
            get() {
                $.ajax({
                    url: '/api/mst/calendar/dayoffs',
                    data: {
                        'date': date_area.dateObj.format(),
                    },
                    type: 'get'
                }).done((res) => {
                    console.log(res);
                    this.mimanji = res.mimanji;
                    this.ijyouji = res.ijyouji;
                });
            }
        }
    });

    const list = new Vue({
        el: '#children-list',
        data: {
            is_loading: true,
            morning_time_rostors: null
        },
        methods: {
            fetch() {
                dayoff.get();
                $.ajax({
                    url: '/api/attendance/earlylist',
                    data: {
                        'date': date_area.dateObj.format(),
                    },
                    type: 'get'
                }).done((res) => {
                    this.morning_time_rostors = res;
                    this.is_loading = false;
                });
            },
            arrive(attendance) {
                $.ajax({
                    url: '/api/attendance/arrive/' + attendance.id,
                    data: {'morning_using': 1},
                    type: 'put'
                }).done((res) => {
                    // 画面描画更新
                    for(let k in res) attendance[k] = res[k];
                });
            },
            absence(attendance) {
                $.ajax({
                    url: '/api/attendance/absence/' + attendance.id,
                    data: {},
                    type: 'put'
                }).done((res) => {
                    // 画面描画更新
                    for(let k in res) attendance[k] = res[k];
                });
            },
        }
    });

    const createAttendance = () => {
        let current_date = date_area.dateObj.format();
        $.ajax({
            url: '/api/attendance/create',
            data: {
                'date': current_date
                // ,'month': date_area.dateObj.separate().month
            },
            type: 'post'
        }).done((res) => {
            list.fetch();
        });
    }

    const init = () => {
        date_area.listen();
        createAttendance();
    }

    $(document).on('change', '#date-area', createAttendance);

    init();
});
