'use strict';

require('../../common');

$(document).ready(() => {
    // const date_area = require('../../components/date-area')();
    // let month = date_area.dateObj.separate().month;
    // let $form = $('#classroom-select-form');

    const list = new Vue({
        el: '#list',
        data: {
            list: null,
        },
        methods: {
            fetch() {
                $.ajax({
                    url: '/api/mst/classroom',
                    // data: $form.serialize(),
                    type: 'get'
                }).done((res) => {
                    this.list = res;
                });
            },
            edit(classroom) {
                editForm.set(_.cloneDeep(classroom));
            }
        }
    });

    const editForm = new Vue({
        el: '#edit-form',
        data: {
            id: null,
            grade_id: null,
            bizyear: null,
            name: '',
            teacher: '',
            order: null,
            validation_error: null,
            original: null
        },
        methods: {
            reset() {
                this.id = null;
                this.grade_id = null;
                this.bizyear = null;
                this.name = '';
                this.teacher = '';
                this.order = 1;
                this.validation_error = null;
                this.original = null;
            },
            set(classroom) {
                this.id = classroom.id;
                this.bizyear = classroom.bizyear;
                this.grade_id = classroom.grade_id;
                this.name = classroom.name;
                this.teacher = classroom.teacher;
                this.order = classroom.order;
                this.validation_error = null;
                this.original = classroom;
            },
            save() {
                let fd = $('#edit-form').serializeArray();
                let classroom_id = _.find(fd, {name:'id'}).value;
                let url = '/api/mst/classroom/create';
                let type = 'post';
                if(classroom_id) {
                    url = '/api/mst/classroom/' + classroom_id;
                    type = 'put';
                }
                console.log('classroom_id',classroom_id, url, type,(classroom_id));
                $.ajax({
                    url: url,
                    data: fd,
                    type: type
                }).done((res) => {
                    if (typeof res.validation_error === 'undefined') {
                        list.fetch();
                        $('#edit-area').modal('hide');
                        this.reset();
                    }
                    this.validation_error = res.validation_error;
                    $('#edit-area').scrollTop(0);
                });
            }
        }
    });

    $(document).on('click', '#create', () => {
        editForm.reset();
        editForm.bizyear = $("#select-bizyear option:first").val();
        editForm.grade_id = $("#select-grade option:first").val();
    });

    $(document).on('click', '#edit-area .btn-save', () => {
        editForm.save();
    });

    $(document).on('click', '#edit-area .btn-close', () => {
        editForm.reset();
    });

    list.fetch();
});
