'use strict';

require('../common');

$(document).ready(() => {
    const date_area = require('../components/date-area')();

    const list = new Vue({
        el: '#children-list',
        data: {
            evening_time_rostors: null,
        },
        methods: {
            fetch() {
                $.ajax({
                    url: '/api/depart/evening',
                    data: {
                        'date': date_area.dateObj.format()
                    },
                    type: 'get'
                }).done((res) => {
                    this.evening_time_rostors = res;
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
            update(attendance) {
                $.ajax({
                    url: '/api/attendance/'+attendance.id,
                    data: attendance,
                    type: 'put'
                }).done((res) => {
//                    this.fetch();
                });
            },
            edit(attendace) {
                editForm.set(_.cloneDeep(attendace));
            }
        }
    });
    $(document).on('change', '#date-area', list.fetch);

    const editForm = new Vue({
        el: '#edit-form',
        data: {
            id: null,
            outtime: null,
            pick_up: null,
            validation_error: null
        },
        methods: {
            set(attendace) {
                this.id = attendace.id;
                this.outtime = attendace.outtime ? attendace.outtime : new Date().format('HH:II');
                this.pick_up = attendace.pick_up;
            },
            save() {
                let fd = $('#edit-form').serializeArray();
                $.ajax({
                    url: '/api/attendance/' + _.find(fd, {name:'id'}).value, // attendaces.id
                    data: fd,
                    type: 'put'
                }).done((res) => {
                    if (typeof res.validation_error === 'undefined') {
                        list.fetch();
                        $('#edit-area').modal('hide');
                    }
                    this.validation_error = res.validation_error;
                    $('#edit-area').scrollTop(0);
                });
            }
        }
    });

    $(document).on('click', '#edit-area .btn-save', () => {
        editForm.save();
    });

    const init = () => {
        list.fetch();
        date_area.listen();
    }

    init();
});
