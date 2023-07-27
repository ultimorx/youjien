'use strict';

require('../common');

$(document).ready(() => {
    const date_area = require('../components/date-area')();

    const dayoff = new Vue({
        el: '#dayoff',
        data: {
            dayoff : ''
        },
        methods: {
            get() {
                $.ajax({
                    url: '/api/mst/calendar/dayoff',
                    data: {
                        'date': date_area.dateObj.format(),
                        'classroom_id': $('#classroom-select-list').val()
                    },
                    type: 'get'
                }).done((res) => {
                    console.log(res);
                    this.dayoff = res;
                });
            }
        }
    });

    const list = new Vue({
        el: '#children-list',
        data: {
            is_loading: true,
            rosters: null,
            late: '',
            early: '',
            disease: '',
            disease_mst: [],
            eveningtime_mst: [],
            bus_mst: [],
            date: date_area.dateObj.format()
        },
        methods: {
            fetch() {
                viewYearMonth();
                dayoff.get();
                $.ajax({
                    url: '/api/attendance/classroom',
                    data: {
                        'date': date_area.dateObj.format(),
                        'classroom_id': $('#classroom-select-list').val()
                    },
                    type: 'get'
                }).done((res) => {
                    this.rosters = res;
                    this.is_loading = false;
                    if(this.rosters.length >= 1) {
                        $('#util-btns, #children-list').removeClass('d-none');
                    } else {
                        $('#util-btns, #children-list').addClass('d-none');
                    }
                });
            },
            update(attendance, callback) {
                $.ajax({
                    url: '/api/attendance/'+attendance.id,
                    data: attendance,
                    type: 'put'
                }).done((res) => {
                    callback(res);
                });
            },
            arrive(attendance) {
                $.ajax({
                    url: '/api/attendance/arrive/' + attendance.id,
                    data: {},
                    type: 'put'
                }).done((res) => {
                    // 画面描画更新
                    for(let k in res) attendance[k] = res[k];
                });
            },
            arrive_multi() {
                let ids = [];
                for(let k in this.rosters) {
                    if (this.rosters[k].attendance.attendance !== null) continue;
                    ids[k] = this.rosters[k].attendance.id;
                }
                console.log(ids.length);
                if(ids.length == 0) {
                    return;
                }
                console.log('ids');
                $.ajax({
                    url: '/api/attendance/arrives',
                    data: {'attendance_ids': ids},
                    type: 'post'
                }).done((res) => {
                    // 画面描画更新
                    this.fetch();
                    // for(let k in res) attendance[k] = res[k];
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
                    this.late = '';
                    this.early = '';
                });
            },
            cancel(attendance) {
                $.ajax({
                    url: '/api/attendance/cancel/' + attendance.id,
                    data: {},
                    type: 'put'
                }).done((res) => {
                    // 画面描画更新
                    for(let k in res) attendance[k] = res[k];
                    this.late = '';
                    this.early = '';
                });
            },
            lateUpdate(attendance, isCancel) {
                if(this.late == '' && isCancel === undefined) return;
                let late = (isCancel === undefined)? this.late : null;
                console.log('lateUpdate', late);
                $.ajax({
                    url: '/api/attendance/' + attendance.id,
                    data: {'late': late},
                    type: 'put'
                }).done((res) => {
                    attendance.late = late;
                    this.late = '';
                });
            },
            earlyUpdate(attendance, isCancel) {
                if(this.early == '' && isCancel === undefined) return;
                let early = (isCancel === undefined)? this.early : null;
                $.ajax({
                    url: '/api/attendance/' + attendance.id,
                    data: {'early': early, 'outtime': early},
                    type: 'put'
                }).done((res) => {
                    attendance.early = early;
                    attendance.outtime = early;
                    this.early = '';
                });
            },
            diseaseUpdate(attendance, isCancel) {
                if(this.disease == '' && isCancel === undefined) return;
                let disease = (isCancel === undefined)? this.disease : null;
                $.ajax({
                    url: '/api/attendance/' + attendance.id,
                    data: {'diseases_id': disease},
                    type: 'put'
                }).done((res) => {
                    attendance.diseases_id = disease;
                    this.disease = '';
                });
            },
            busUpdate(attendance) {
                $.ajax({
                    url: '/api/attendance/' + attendance.id,
                    data: {'bus_id': attendance.bus_id},
                    type: 'put'
                }).done((res) => {});
            },
            eveningUpdate(attendance) {
                console.log('eveningUpdate',attendance.evening_time_id);
                $.ajax({
                    url: '/api/attendance/' + attendance.id,
                    data: {'evening_time_id': attendance.evening_time_id},
                    type: 'put'
                }).done((res) => {});
            },
            setDiseaseMst() {
                $.ajax({
                    url: '/api/disease',
                    data: {},
                    type: 'get'
                }).done((res) => {
                    for(let k in res) this.disease_mst[res[k].id] = res[k].name;
                });
            },
            setEveningTimeMst() {
                $.ajax({
                    url: '/api/eveningtime',
                    data: {},
                    type: 'get'
                }).done((res) => {
                    for(let k in res) this.eveningtime_mst[res[k].id] = res[k].time;
                });
            },
            setBusMst() {
                $.ajax({
                    url: '/api/bus',
                    data: {},
                    type: 'get'
                }).done((res) => {
                    for(let k in res) this.bus_mst[res[k].id] = res[k].name;
                });
            },
            getDiseaseName(id) {
                return (this.disease_mst[id]) ? this.disease_mst[id]: 'None';
            },
            getEveningTimeName(id) {
                return (this.eveningtime_mst[id]) ? this.eveningtime_mst[id]: 'None';
            },
            getBusName(id) {
                return (this.bus_mst[id]) ? this.bus_mst[id]: 'None';
            },
        },
        filters: {
            // todo : 案１　日時フォーマット  momentを使用することで実現できるらしい。momentの読み込みが必要
            // moment: function (time) {
            //     return moment(time).format('HH:mm');
            // },
            // todo : 案２　日時フォーマット  model Carbon::parse($value)->format("H:i");  ※クエリビルダー取得では適用されない　　　　　
            // todo : 案３　日時フォーマット　jsのfilters
            time: function (time) {
                console.log(time);
                if(time === null) return
                return time.substr(0, 5);
            }
        }
    });

    const createAttendance = () => {
        $.ajax({
            url: '/api/attendance/create',
            data: {
                'date': date_area.dateObj.format()
                // ,'month': date_area.dateObj.separate().month
            },
            type: 'post'
        }).done((res) => {
            list.fetch();
        });
    }

    const init = () => {
        createAttendance();
        $('input[type="time"]').tooltip('show');
        list.setDiseaseMst();
        list.setEveningTimeMst();
        list.setBusMst();
        date_area.listen();
    }

    const viewYearMonth = () => {
        let $classroom = $('#classroom-select-list option:selected');
        $('#classroom_name').text($classroom.text());
        let teacher = $classroom.attr('data-teacher') || "";
        $('#classroom_teacher').text(teacher);
        let age_type = $classroom.attr('data-grade-age-type') || "";
        $('#classroom_age_type').text(age_type);
    }

    $(document).on('click', '#date-area .btn', () => {
        // createAttendance();// changeイベントで実行のためここでは呼び出し不要
        list.date = date_area.dateObj.format();
        console.log('date change', list.date);
    });

    // クラス選択
    $(document).on('change', '#classroom-select-list', () => {
        list.fetch();
    });
    $(document).on('click', '#classroom-search', () => {
        list.fetch();
    });

    $(document).on('click', '#all-arrive', () => {
        list.arrive_multi();
    });
    $(document).on('change', '#date-area', createAttendance);

    init();
});
