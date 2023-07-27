'use strict';

require('../../common');

$(document).ready(() => {

    const list = new Vue({
        el: '#list',
        data: {
            list: null,
        },
        methods: {
            fetch() {
                $.ajax({
                    url: '/api/city/absence',
                    // data: $form.serialize(),
                    type: 'get'
                }).done((res) => {
                    this.list = res;
                });
            },
            isActive(active) {
                return (active != 1);
            },
            viewActive(active) {
                return (active == 1)? '': '未使用';
            },
            edit(absence) {
                editForm.set(_.cloneDeep(absence));
            }
        }
    });

    const editForm = new Vue({
        el: '#edit-area',
        data: {
            id: null,
            absence_type: '',
            name: '',
            active: 1,
            order: 0,
            validation_error: null,
            original: null,
            deletable: false
        },
        methods: {
            reset() {
                this.id = null;
                this.absence_type = '';
                this.name = '';
                this.active = 1;
                this.order = 1;
                this.validation_error = null;
                this.original = null;
            },
            set(absence) {
                this.id = absence.id;
                this.absence_type = absence.absence_type;
                this.name = absence.name;
                this.active = absence.active;
                this.order = absence.order;
                this.validation_error = null;
                this.original = absence;
            },
            save() {
                let fd = $('#edit-form').serializeArray();
                let absence_id = _.find(fd, {name:'id'}).value;
                let url = '/api/city/absence/create';
                let type = 'post';
                if(absence_id) {
                    url = '/api/city/absence/' + absence_id;
                    type = 'put';
                }
                console.log('absence_id',absence_id, url, type,(absence_id));
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
            },
            delete() {
                let fd = $('#edit-form').serializeArray();
                let absence_id = _.find(fd, {name:'id'}).value;
                console.log('delete absence_id',absence_id);

                if(!absence_id) {
                    return;
                }
                let url = '/api/city/absence/' + absence_id;
                let type = 'delete';

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
    });

    $(document).on('click', '#edit-area .btn-save', () => {
        editForm.save();
    });

    $(document).on('click', '#edit-area .btn-close', () => {
        editForm.reset();
    });

    $(document).on('click', '#edit-area .btn-delete', () => {
        if( confirm('削除してもよろしいですか?') ) {
            editForm.delete();
        }
    });

    list.fetch();
});
